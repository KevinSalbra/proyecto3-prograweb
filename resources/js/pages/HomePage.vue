<template>
    <section class="search-section">
        <form class="main-search flex align-items-center gap-3" @submit.prevent="loadHome(1)">
            <span class="search-icon">⌕</span>
            <InputText v-model="search" class="flex-1"
                placeholder="Buscar por nombre, productor, uva, país, región o año" />
            <Button type="submit" label="Buscar" class="action-dark" />
            <Button v-if="search" type="button" label="Limpiar" class="action-outline" @click="clearSearch" />
        </form>
    </section>

    <section v-if="!search && featuredProducts.length" class="section">
        <div class="section-header">
            <p class="eyebrow">Recomendados</p>
            <h2>Productos destacados</h2>
        </div>

        <div class="grid">
            <div v-for="product in featuredProducts" :key="product.id" class="col-12 md:col-6 lg:col-4">
                <ProductCard :product="product" />
            </div>
        </div>
    </section>

    <section class="section" id="ultimos">
        <div class="section-header">
            <p class="eyebrow">Catálogo</p>
            <h2>{{ search ? `Resultados para “${search}”` : 'Últimos productos agregados' }}</h2>
        </div>

        <div v-if="products.length" class="grid">
            <div v-for="product in products" :key="product.id" class="col-12 md:col-6 lg:col-4">
                <ProductCard :product="product" />
            </div>
        </div>

        <div v-else class="empty-state">
            No se encontraron productos con ese criterio de búsqueda.
        </div>

        <Pagination v-if="meta" :meta="meta" @change="loadHome" />
    </section>

    <section class="section" id="categorias">
        <div class="section-header">
            <p class="eyebrow">Exploración</p>
            <h2>Categorías del catálogo</h2>
        </div>

        <div class="grid">
            <div v-for="category in categories" :key="category.id" class="col-12 md:col-6 lg:col-4">
                <RouterLink :to="`/categorias/${category.slug}`" class="category-card">
                    <img v-if="category.image_url" :src="category.image_url" :alt="category.name"
                        class="category-image">

                    <span>{{ category.type }}</span>
                    <h3>{{ category.name }}</h3>
                    <p>{{ category.description }}</p>
                    <small>{{ category.products_count }} productos</small>
                </RouterLink>
            </div>
        </div>
    </section>

    <section class="section" id="blog">
        <Card class="info-card">
            <template #content>
                <div class="info-content">
                    <div>
                        <p class="eyebrow">Blog</p>
                        <h2>Notas sobre vinos</h2>
                    </div>

                    <p>
                        El vino puede clasificarse por su tipo, uva, región, añada y método de elaboración.
                        Los vinos tintos suelen acompañar carnes, pastas y comidas con mayor intensidad;
                        los blancos se asocian con pescados, mariscos, quesos suaves y platos ligeros;
                        mientras que los rosados y champanes funcionan bien en aperitivos, celebraciones
                        y maridajes frescos.
                    </p>
                </div>
            </template>
        </Card>
    </section>

    <section class="section" id="historia">
        <Card class="info-card">
            <template #content>
                <div class="info-content">
                    <div>
                        <p class="eyebrow">Historia</p>
                        <h2>Historia de González & Salazar</h2>
                    </div>

                    <p>
                        González & Salazar nace como una tienda costarricense especializada en vinos y
                        champanes seleccionados. Su propuesta se enfoca en ofrecer un catálogo organizado,
                        elegante y fácil de consultar, donde cada producto pueda identificarse por su
                        productor, país, región, uva, añada, precio y disponibilidad.
                    </p>
                </div>
            </template>
        </Card>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../services/api';
import ProductCard from '../components/ProductCard.vue';
import Pagination from '../components/Pagination.vue';

const search = ref('');
const products = ref([]);
const featuredProducts = ref([]);
const categories = ref([]);
const meta = ref(null);

onMounted(() => {
    loadHome();
});

async function loadHome(page = 1) {
    const response = await api.get('/home', {
        params: {
            buscar: search.value,
            page,
        },
    });

    products.value = response.data.products.data;
    meta.value = response.data.products.meta;
    featuredProducts.value = response.data.featuredProducts;
    categories.value = response.data.categories;
}

function clearSearch() {
    search.value = '';
    loadHome(1);
}
</script>
