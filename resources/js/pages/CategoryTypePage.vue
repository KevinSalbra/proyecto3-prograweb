<template>
    <section class="catalog-header">
        <p class="breadcrumb">
            <RouterLink to="/">Inicio</RouterLink>
            <span>›</span>
            <span>{{ title }}</span>
        </p>

        <h1>{{ title }}</h1>
        <p>Explore los productos disponibles mediante categorías, búsqueda y ordenamiento.</p>
    </section>

    <section class="grid align-items-start gap-4 catalog-layout">
        <aside class="filters col-12 lg:col-3">
            <h2>Filtros</h2>

            <form class="flex flex-column gap-3" @submit.prevent="loadProducts(1)">
                <label>Buscar</label>
                <InputText v-model="search" placeholder="Nombre, productor, región..." />

                <label>Categoría</label>
                <Select
                    v-model="categoryId"
                    :options="categoryOptions"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Todas"
                />

                <label>Ordenar por</label>
                <Select
                    v-model="sort"
                    :options="sortOptions"
                    optionLabel="label"
                    optionValue="value"
                />

                <Button type="submit" label="Aplicar filtros" class="action-dark full" />
            </form>
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

            <div v-if="products.length" class="grid gap-4">
                <ProductCard
                    v-for="product in products"
                    :key="product.id"
                    :product="product"
                    class="col-12 md:col-6 xl:col-4"
                />
            </div>

            <div v-else class="empty-state">
                No hay productos para mostrar.
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
import { computed, onMounted, ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import ProductCard from '../components/ProductCard.vue';
import Pagination from '../components/Pagination.vue';

const props = defineProps({
    type: {
        type: String,
        required: true,
    },
});

const title = ref('');
const router = useRouter();
const search = ref('');
const sort = ref('recientes');
const categoryId = ref('');
const categories = ref([]);
const products = ref([]);
const meta = ref(null);

const categoryOptions = computed(() => {
    return categories.value.map((category) => ({
        ...category,
        label: `${category.name} (${category.products_count})`,
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

watch(
    () => props.type,
    () => {
        search.value = '';
        sort.value = 'recientes';
        categoryId.value = '';
        loadProducts(1);
    }
);

async function loadProducts(page = 1) {
    const response = await api.get(`/categories/type/${props.type}`, {
        params: {
            buscar: search.value,
            orden: sort.value,
            categoria: categoryId.value,
            page,
        },
    });

    title.value = response.data.title;
    categories.value = response.data.categories;
    products.value = response.data.products.data;
    meta.value = response.data.products.meta;
}
</script>
