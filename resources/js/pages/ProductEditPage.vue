<template>
    <section class="form-page">
        <div class="form-header">
            <p class="breadcrumb">
                <RouterLink to="/">Inicio</RouterLink>
                <span>›</span>
                <RouterLink to="/productos">Catálogo</RouterLink>
                <span>›</span>
                <span>Editar producto</span>
            </p>

            <h1>Editar producto</h1>
            <p>Modifique los datos del producto seleccionado.</p>
        </div>

        <ProductForm
            v-if="product"
            :product="product"
            :categories="categories"
            submit-label="Actualizar producto"
            @submit="updateProduct"
        />
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';
import ProductForm from '../components/ProductForm.vue';

const route = useRoute();
const router = useRouter();

const product = ref(null);
const categories = ref([]);

onMounted(() => {
    loadData();
});

async function loadData() {
    const [productResponse, categoryResponse] = await Promise.all([
        api.get(`/products/${route.params.slug}`),
        api.get('/categories'),
    ]);

    product.value = productResponse.data.product;
    categories.value = categoryResponse.data.categories;
}

async function updateProduct(formData, errors) {
    try {
        const response = await api.post(`/products/${route.params.slug}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        router.push(`/productos/${response.data.product.slug}`);
    } catch (error) {
        errors.value = Object.values(error.response?.data?.errors || {}).flat();
    }
}
</script>