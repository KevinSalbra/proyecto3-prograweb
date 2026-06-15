<template>
    <section class="form-page">
        <div class="form-header">
            <p class="breadcrumb">
                <RouterLink to="/">Inicio</RouterLink>
                <span>›</span>
                <RouterLink to="/productos">Catálogo</RouterLink>
                <span>›</span>
                <span>Agregar producto</span>
            </p>

            <h1>Agregar producto</h1>
            <p>Complete la información del vino o champán que desea registrar en el catálogo.</p>
        </div>

        <ProductForm
            :categories="categories"
            submit-label="Guardar producto"
            @submit="storeProduct"
        />
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import ProductForm from '../components/ProductForm.vue';

const router = useRouter();
const categories = ref([]);

onMounted(() => {
    loadCategories();
});

async function loadCategories() {
    const response = await api.get('/categories');
    categories.value = response.data.categories;
}

async function storeProduct(formData, errors) {
    try {
        const response = await api.post('/products', formData, {
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