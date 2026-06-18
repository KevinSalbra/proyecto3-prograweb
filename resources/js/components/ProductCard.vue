<template>
    <Card class="product-card">
        <template #content>
            <RouterLink :to="`/productos/${product.slug}`" class="product-image-wrapper">
                <img
                    :src="product.image_url"
                    :alt="product.name"
                    class="product-image"
                >
            </RouterLink>

            <div class="product-info">
                <span class="product-category">
                    {{ product.category?.name }}
                </span>

                <h3>
                    <RouterLink :to="`/productos/${product.slug}`">
                        {{ product.name }}
                    </RouterLink>
                </h3>

                <p class="product-producer">
                    {{ product.producer }}
                </p>

                <div class="product-meta">
                    <span>{{ product.country }}</span>
                    <span>{{ product.vintage_year || 'NV' }}</span>
                </div>

                <strong class="price">
                    ₡ {{ formatPrice(product.price) }}
                </strong>
            </div>
        </template>
    </Card>
</template>

<script setup>
defineProps({
    product: {
        type: Object,
        required: true,
    },
});

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>