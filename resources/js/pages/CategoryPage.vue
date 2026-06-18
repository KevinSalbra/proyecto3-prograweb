<template>
    <section class="catalog-header">
        <p class="breadcrumb">
            <RouterLink to="/">Inicio</RouterLink>
            <span>›</span>
            <span>{{ title }}</span>
        </p>

        <h1>{{ title }}</h1>
        <p>{{ category?.description }}</p>
    </section>

    <section class="section grid align-items-start gap-4 catalog-layout">
        <aside class="filters col-12 lg:col-3">
            <h2>Filtros</h2>

            <form class="flex flex-column gap-3" @submit.prevent="loadCategory(1)">
                <label>Buscar</label>
                <InputText v-model="search" placeholder="Nombre, productor, región..." />

                <label>Ordenar por</label>
                <Select
                    v-model="sort"
                    :options="sortOptions"
                    optionLabel="label"
                    optionValue="value"
                />

                <Button type="submit" label="Aplicar filtros" class="action-dark full" />
                <Button type="button" label="Limpiar" class="action-outline full" @click="clearFilters" />
            </form>

            <div class="filter-list flex flex-column gap-2 mt-5">
                <h3>Categorías</h3>

                <RouterLink
                    v-for="item in categories"
                    :key="item.id"
                    :to="`/categorias/${item.slug}`"
                    class="flex justify-content-between align-items-center border-bottom-1 surface-border pb-2"
                >
                    {{ item.name }}
                    <span>{{ item.products_count }}</span>
                </RouterLink>
            </div>
        </aside>

        <section class="catalog-content col-12 lg:col-9">
            <div class="catalog-tools flex justify-content-between align-items-center gap-3 flex-wrap">
                <span>{{ meta?.total || 0 }} productos encontrados</span>

                <Button
                    type="button"
                    label="Agregar producto"
                    class="action-light"
                    @click="router.push('/productos/crear')"
                />
            </div>

            <div v-if="products.length" class="grid">
                <div
                    v-for="product in products"
                    :key="product.id"
                    class="col-12 md:col-6 xl:col-4"
                >
                    <ProductCard :product="product" />
                </div>
            </div>

            <div v-else class="empty-state">
                No hay productos para mostrar.
            </div>

            <Pagination
                v-if="meta"
                :meta="meta"
                @change="loadCategory"
            />
        </section>
    </section>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import ProductCard from '../components/ProductCard.vue';
import Pagination from '../components/Pagination.vue';

const route = useRoute();
const router = useRouter();

const title = ref('');
const category = ref(null);
const search = ref('');
const sort = ref('recientes');
const categories = ref([]);
const products = ref([]);
const meta = ref(null);

const sortOptions = [
    { label: 'Más recientes', value: 'recientes' },
    { label: 'Nombre', value: 'nombre' },
    { label: 'Precio menor a mayor', value: 'precio_asc' },
    { label: 'Precio mayor a menor', value: 'precio_desc' },
    { label: 'Año más reciente', value: 'anio_desc' },
];

onMounted(() => {
    loadCategory();
});

watch(
    () => route.params.slug,
    () => {
        clearFilters();
    }
);

async function loadCategory(page = 1) {
    const response = await api.get(`/categories/${route.params.slug}`, {
        params: {
            buscar: search.value,
            orden: sort.value,
            page,
        },
    });

    title.value = response.data.title;
    category.value = response.data.category;
    categories.value = response.data.categories;
    products.value = response.data.products.data;
    meta.value = response.data.products.meta;
}

function clearFilters() {
    search.value = '';
    sort.value = 'recientes';
    loadCategory(1);
}
</script>