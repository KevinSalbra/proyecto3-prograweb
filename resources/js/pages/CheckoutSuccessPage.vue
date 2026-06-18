<template>
    <section class="checkout-success-page">
        <Card v-if="order" class="success-card">
            <template #content>
                <div class="success-content">
                    <p class="eyebrow">Compra registrada</p>

                    <h1>Gracias por su compra</h1>

                    <div class="success-meta">
                        <p>
                            Código de confirmación:
                            <strong>{{ order.confirmation }}</strong>
                        </p>

                        <p>
                            Fecha:
                            <strong>{{ order.placed_at }}</strong>
                        </p>
                    </div>

                    <h2>Resumen de la compra</h2>

                    <ul class="order-list">
                        <li v-for="item in order.items" :key="item.name">
                            <span>{{ item.quantity }} × {{ item.name }}</span>
                            <strong>₡ {{ formatPrice(item.subtotal) }}</strong>
                        </li>
                    </ul>

                    <p class="success-total">
                        Total: ₡ {{ formatPrice(order.total) }}
                    </p>

                    <Button
                        type="button"
                        label="Volver al inicio"
                        class="action-dark"
                        @click="router.push('/')"
                    />
                </div>
            </template>
        </Card>

        <div v-else class="empty-state">
            No hay una compra registrada.
        </div>
    </section>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const order = ref(null);
const router = useRouter();

onMounted(async () => {
    try {
        const response = await api.get('/checkout/success');
        order.value = response.data.order;
    } catch {
        order.value = null;
    }
});

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>