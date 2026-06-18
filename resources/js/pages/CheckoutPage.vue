<template>
    <section class="form-page">
        <div class="form-header">
            <p class="breadcrumb">
                <RouterLink to="/">Inicio</RouterLink>
                <span>›</span>
                <RouterLink to="/carrito">Carrito</RouterLink>
                <span>›</span>
                <span>Pago</span>
            </p>

            <h1>Finalizar compra</h1>
            <p>Ingrese sus datos de entrega y seleccione el método de pago para completar su pedido.</p>
        </div>

        <form class="form-card checkout-form" @submit.prevent="submitCheckout">
            <Message
                v-for="error in errors"
                :key="error"
                severity="error"
                class="mb-3"
            >
                {{ error }}
            </Message>

            <div class="form-section-title">
                <h2>Datos de entrega</h2>
                <p>Esta información será utilizada para coordinar el envío del pedido.</p>
            </div>

            <div class="grid">
                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Nombre completo</label>
                    <InputText v-model="form.full_name" required />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Correo electrónico</label>
                    <InputText v-model="form.email" type="email" required />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Teléfono</label>
                    <InputText v-model="form.phone" required />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Provincia</label>
                    <InputText v-model="form.province" required />
                </div>

                <div class="col-12 flex flex-column gap-2">
                    <label>Dirección exacta</label>
                    <Textarea v-model="form.address" rows="4" autoResize required />
                </div>
            </div>

            <div class="form-section-title">
                <h2>Información de pago</h2>
                <p>Complete los datos requeridos para registrar el pedido.</p>
            </div>

            <div class="grid">
                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Método de pago</label>
                    <Select
                        v-model="form.payment_method"
                        :options="paymentMethods"
                        optionLabel="label"
                        optionValue="value"
                        required
                    />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Número de tarjeta</label>
                    <InputText
                        v-model="form.card_number"
                        maxlength="19"
                        required
                    />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>Fecha de expiración</label>
                    <InputText
                        :modelValue="form.expiry_date"
                        placeholder="MM/AA"
                        maxlength="5"
                        required
                        @update:modelValue="formatExpiryDate"
                    />
                </div>

                <div class="col-12 md:col-6 flex flex-column gap-2">
                    <label>CVV</label>
                    <InputText
                        v-model="form.cvv"
                        maxlength="4"
                        required
                    />
                </div>
            </div>

            <div class="form-actions flex flex-wrap gap-3">
                <Button type="submit" label="Confirmar pedido" class="action-dark" />

                <Button
                    type="button"
                    label="Volver al carrito"
                    class="action-light"
                    @click="router.push('/carrito')"
                />
            </div>
        </form>
    </section>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import { cartStore } from '../services/cartStore';

const router = useRouter();
const errors = ref([]);

const paymentMethods = [
    { label: 'Tarjeta de crédito', value: 'Tarjeta de crédito' },
    { label: 'Tarjeta de débito', value: 'Tarjeta de débito' },
    { label: 'Transferencia bancaria', value: 'Transferencia bancaria' },
];

const form = reactive({
    full_name: '',
    email: '',
    phone: '',
    province: '',
    address: '',
    payment_method: 'Tarjeta de crédito',
    card_number: '',
    expiry_date: '',
    cvv: '',
});

onMounted(async () => {
    await cartStore.load();

    if (!cartStore.count) {
        router.push('/carrito');
    }
});

function formatExpiryDate(value) {
    let cleanValue = String(value || '').replace(/\D/g, '').slice(0, 4);

    if (cleanValue.length >= 3) {
        cleanValue = `${cleanValue.slice(0, 2)}/${cleanValue.slice(2)}`;
    }

    form.expiry_date = cleanValue;
}

async function submitCheckout() {
    errors.value = [];

    try {
        await api.post('/checkout', form);
        await cartStore.load();
        router.push('/checkout/exito');
    } catch (error) {
        errors.value = Object.values(error.response?.data?.errors || {}).flat();

        if (!errors.value.length && error.response?.data?.message) {
            errors.value = [error.response.data.message];
        }
    }
}
</script>