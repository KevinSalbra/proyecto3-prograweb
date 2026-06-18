<template>
    <section class="catalog-header">
        <p class="breadcrumb">
            <RouterLink to="/">Inicio</RouterLink>
            <span>›</span>
            <span>Catálogo</span>
        </p>

        <h1>Administrar catálogo</h1>
        <p>Consulte, registre y actualice los productos disponibles en González & Salazar.</p>
    </section>

    <section class="catalog-page-layout">
        <aside class="filters">
            <h2>Búsqueda</h2>

            <form class="flex flex-column gap-3" @submit.prevent="loadProducts(1)">
                <label>Palabra clave</label>
                <InputText v-model="search" placeholder="Nombre, productor, uva..." />

                <label>Categoría</label>
                <Select
                    v-model="categoryId"
                    :options="categoryOptions"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Todas"
                />

                <label>Orden</label>
                <Select
                    v-model="sort"
                    :options="sortOptions"
                    optionLabel="label"
                    optionValue="value"
                />

                <Button type="submit" label="Buscar" class="action-dark full" />
                <Button type="button" label="Limpiar" class="action-outline full" @click="clearFilters" />
            </form>
        </aside>

        <section class="catalog-content">
            <div class="catalog-tools">
                <span>{{ meta?.total || 0 }} productos registrados</span>

                <Button
                    type="button"
                    label="Agregar producto"
                    class="action-dark"
                    @click="router.push('/productos/crear')"
                />
            </div>

            <DataTable
                v-if="products.length"
                :value="products"
                class="admin-table-wrapper"
                responsiveLayout="scroll"
            >
                <Column header="Imagen" style="width: 115px">
                    <template #body="{ data }">
                        <img
                            :src="data.image_url"
                            :alt="data.name"
                            class="table-image"
                        >
                    </template>
                </Column>

                <Column header="Producto" style="min-width: 230px">
                    <template #body="{ data }">
                        <div class="admin-product-info">
                            <strong>{{ data.name }}</strong>
                            <span>{{ data.producer }}</span>
                        </div>
                    </template>
                </Column>

                <Column header="Categoría" style="width: 150px">
                    <template #body="{ data }">
                        {{ data.category?.name }}
                    </template>
                </Column>

                <Column header="Precio" style="width: 130px">
                    <template #body="{ data }">
                        ₡ {{ formatPrice(data.price) }}
                    </template>
                </Column>

                <Column header="Stock" style="width: 90px">
                    <template #body="{ data }">
                        {{ data.stock }}
                    </template>
                </Column>

                <Column header="Acciones" style="width: 170px">
                    <template #body="{ data }">
                        <div class="catalog-actions">
                            <Button
                                label="Ver"
                                class="action-outline compact-button"
                                @click="router.push(`/productos/${data.slug}`)"
                            />

                            <Button
                                label="Editar"
                                class="action-outline compact-button"
                                @click="router.push(`/productos/${data.slug}/editar`)"
                            />

                            <Button
                                label="Eliminar"
                                class="action-outline compact-button danger-button"
                                @click="deleteProduct(data)"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>

            <div v-else class="empty-state">
                No se encontraron productos.
            </div>

            <Pagination
                v-if="meta"
                :meta="meta"
                @change="loadProducts"
            />
        </section>
    </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useConfirm } from 'primevue/useconfirm';
import api from '../services/api';
import Pagination from '../components/Pagination.vue';

const search = ref('');
const categoryId = ref('');
const sort = ref('recientes');
const categories = ref([]);
const products = ref([]);
const meta = ref(null);

const confirm = useConfirm();
const router = useRouter();

const categoryOptions = computed(() => {
    return categories.value.map((category) => ({
        ...category,
        label: `${category.type} - ${category.name}`,
    }));
});

const sortOptions = [
    { label: 'Más recientes', value: 'recientes' },
    { label: 'Nombre', value: 'nombre' },
    { label: 'Precio menor a mayor', value: 'precio_asc' },
    { label: 'Precio mayor a menor', value: 'precio_desc' },
    { label: 'Año más reciente', value: 'anio_desc' },
];

onMounted(() => {
    loadProducts();
});

async function loadProducts(page = 1) {
    const response = await api.get('/products', {
        params: {
            buscar: search.value,
            categoria: categoryId.value,
            orden: sort.value,
            page,
        },
    });

    categories.value = response.data.categories;
    products.value = response.data.products.data;
    meta.value = response.data.products.meta;
}

function clearFilters() {
    search.value = '';
    categoryId.value = '';
    sort.value = 'recientes';
    loadProducts(1);
}

async function deleteProduct(product) {
    confirm.require({
        message: `¿Desea eliminar ${product.name}?`,
        header: 'Confirmar eliminación',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'Cancelar',
        acceptLabel: 'Eliminar',
        acceptClass: 'action-dark',
        accept: async () => {
            await api.delete(`/products/${product.slug}`);
            loadProducts(meta.value?.current_page || 1);
        },
    });
}

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>