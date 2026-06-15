<template>
    <section v-if="product" class="grid align-items-stretch gap-5 product-detail">
        <div class="col-12 lg:col-6 product-detail-image">
            <img :src="product.image_url" :alt="product.name">
        </div>

        <div class="col-12 lg:col-6 product-detail-info">
            <p class="breadcrumb">
                <RouterLink to="/">Inicio</RouterLink>
                <span>›</span>
                <RouterLink to="/productos">Catálogo</RouterLink>
                <span>›</span>
                <span>{{ product.name }}</span>
            </p>

            <span class="product-category">{{ product.category?.name }}</span>
            <h1>{{ product.name }}</h1>
            <p class="producer">{{ product.producer }}</p>

            <strong class="detail-price">
                ₡ {{ formatPrice(product.price) }}
            </strong>

            <p>{{ product.description }}</p>

            <dl class="product-specs">
                <div><dt>Tipo</dt><dd>{{ product.wine_type }}</dd></div>
                <div><dt>Uva</dt><dd>{{ product.grape }}</dd></div>
                <div><dt>País</dt><dd>{{ product.country }}</dd></div>
                <div><dt>Región</dt><dd>{{ product.region }}</dd></div>
                <div><dt>Denominación</dt><dd>{{ product.appellation || 'No especificada' }}</dd></div>
                <div><dt>Añada</dt><dd>{{ product.vintage_year || 'NV' }}</dd></div>
                <div><dt>Volumen</dt><dd>{{ product.volume_ml }} ml</dd></div>
                <div><dt>Alcohol</dt><dd>{{ product.alcohol_percentage }}%</dd></div>
                <div><dt>Stock</dt><dd>{{ product.stock }}</dd></div>
                <div><dt>Condición</dt><dd>{{ product.condition }}</dd></div>
            </dl>

            <div class="detail-actions flex align-items-center gap-3 flex-wrap">
                <Button
                    type="button"
                    label="Agregar al carrito"
                    class="action-dark"
                    :disabled="product.stock < 1"
                    @click="addToCart"
                />

                <Button
                    label="Editar producto"
                    class="action-light"
                    @click="goToEdit"
                />
            </div>

            <Message v-if="message" severity="success">{{ message }}</Message>
        </div>
    </section>

    <section v-if="relatedProducts.length" class="section">
        <div class="section-header">
            <p class="eyebrow">Relacionados</p>
            <h2>Productos similares</h2>
        </div>

        <div class="grid gap-4">
            <ProductCard
                v-for="item in relatedProducts"
                :key="item.id"
                :product="item"
                class="col-12 md:col-6 xl:col-4"
            />
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import { cartStore } from '../services/cartStore';
import ProductCard from '../components/ProductCard.vue';

const route = useRoute();
const router = useRouter();

const product = ref(null);
const relatedProducts = ref([]);
const message = ref('');

onMounted(() => {
    loadProduct();
});

watch(
    () => route.params.slug,
    () => loadProduct()
);

async function loadProduct() {
    const response = await api.get(`/products/${route.params.slug}`);

    product.value = response.data.product;
    relatedProducts.value = response.data.relatedProducts;
}

async function addToCart() {
    const response = await cartStore.add(product.value, 1);
    message.value = response.message;
}

function goToEdit() {
    router.push(`/productos/${route.params.slug}/editar`);
}

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>
