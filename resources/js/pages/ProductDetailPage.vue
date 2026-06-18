<template>
    <section v-if="product" class="product-detail-page">
        <div class="product-detail-image">
            <img :src="product.image_url" :alt="product.name">
        </div>

        <div class="product-detail-info">
            <p class="breadcrumb detail-breadcrumb">
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

            <p class="detail-description">
                {{ product.description }}
            </p>

            <dl class="product-specs">
                <div>
                    <dt>Tipo</dt>
                    <dd>{{ product.wine_type }}</dd>
                </div>

                <div>
                    <dt>Uva</dt>
                    <dd>{{ product.grape }}</dd>
                </div>

                <div>
                    <dt>País</dt>
                    <dd>{{ product.country }}</dd>
                </div>

                <div>
                    <dt>Región</dt>
                    <dd>{{ product.region }}</dd>
                </div>

                <div>
                    <dt>Denominación</dt>
                    <dd>{{ product.appellation || 'No especificada' }}</dd>
                </div>

                <div>
                    <dt>Añada</dt>
                    <dd>{{ product.vintage_year || 'NV' }}</dd>
                </div>

                <div>
                    <dt>Volumen</dt>
                    <dd>{{ product.volume_ml }} ml</dd>
                </div>

                <div>
                    <dt>Alcohol</dt>
                    <dd>{{ product.alcohol_percentage }}%</dd>
                </div>

                <div>
                    <dt>Stock</dt>
                    <dd>{{ product.stock }}</dd>
                </div>

                <div>
                    <dt>Condición</dt>
                    <dd>{{ product.condition }}</dd>
                </div>
            </dl>

            <div class="detail-actions">
                <Button
                    type="button"
                    label="Agregar al carrito"
                    class="action-dark"
                    :disabled="product.stock < 1"
                    @click="addToCart"
                />

                <Button
                    label="Ir al carrito"
                    class="action-light"
                    @click="router.push('/carrito')"
                />
            </div>

            <Message v-if="message" severity="success" class="mt-4">
                {{ message }}
            </Message>
        </div>
    </section>

    <section v-if="relatedProducts.length" class="section">
        <div class="section-header">
            <p class="eyebrow">Relacionados</p>
            <h2>Productos similares</h2>
        </div>

        <div class="grid">
            <div
                v-for="item in relatedProducts"
                :key="item.id"
                class="col-12 md:col-6 xl:col-4"
            >
                <ProductCard :product="item" />
            </div>
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
    message.value = '';
}

async function addToCart() {
    const response = await cartStore.add(product.value, 1);
    message.value = response.message;
}

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>