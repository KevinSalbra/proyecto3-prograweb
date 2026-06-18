<template>
    <section class="catalog-header">
        <p class="breadcrumb">
            <RouterLink to="/">Inicio</RouterLink>
            <span>›</span>
            <span>Carrito</span>
        </p>

        <h1>Carrito de compras</h1>
        <p>Revise sus productos seleccionados antes de continuar con el pago.</p>
    </section>

    <section class="cart-page">
        <div v-if="visibleItems.length" class="cart-page-layout">
            <DataTable
                :value="visibleItems"
                class="cart-table"
                responsiveLayout="scroll"
            >
                <Column header="Producto" style="min-width: 260px">
                    <template #body="{ data }">
                        <div class="cart-product-info">
                            <img
                                :src="data.product.image_url"
                                :alt="data.product.name"
                                class="cart-product-image"
                            >

                            <div>
                                <strong>{{ data.product.name }}</strong>
                                <span>{{ data.product.producer }}</span>
                            </div>
                        </div>
                    </template>
                </Column>

                <Column header="Precio" style="width: 140px">
                    <template #body="{ data }">
                        ₡ {{ formatPrice(data.unit_price) }}
                    </template>
                </Column>

                <Column header="Cantidad" style="width: 150px">
                    <template #body="{ data }">
                        <InputNumber
                            :modelValue="data.displayQuantity"
                            :min="0"
                            :max="data.product.stock"
                            :useGrouping="false"
                            class="quantity-input"
                            @update:modelValue="setQuantity(data.product.id, $event, data.product.stock)"
                        />
                    </template>
                </Column>

                <Column header="Subtotal" style="width: 150px">
                    <template #body="{ data }">
                        ₡ {{ formatPrice(data.displaySubtotal) }}
                    </template>
                </Column>

                <Column header="Acción" style="width: 160px">
                    <template #body="{ data }">
                        <Button
                            label="Eliminar"
                            class="action-outline compact-button"
                            @click="removeItem(data.product)"
                        />
                    </template>
                </Column>
            </DataTable>

            <aside class="cart-summary">
                <h2>Resumen</h2>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <strong>₡ {{ formatPrice(previewSubtotal) }}</strong>
                </div>

                <div class="summary-row total-row">
                    <span>Total</span>
                    <strong>₡ {{ formatPrice(previewTotal) }}</strong>
                </div>

                <p v-if="isSaving" class="cart-status">
                    Actualizando carrito...
                </p>

                <Button
                    type="button"
                    label="Continuar compra"
                    class="action-dark full"
                    @click="router.push('/checkout')"
                />
            </aside>
        </div>

        <div v-else class="empty-state">
            El carrito está vacío.
        </div>
    </section>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { cartStore } from '../services/cartStore';

const draftQuantities = reactive({});
const isSaving = ref(false);
const router = useRouter();

let saveTimeout = null;

onMounted(async () => {
    await cartStore.load();
});

const visibleItems = computed(() => {
    return cartStore.items.map((item) => {
        const quantity = getQuantity(item);

        return {
            ...item,
            displayQuantity: quantity,
            displaySubtotal: Number(item.unit_price) * quantity,
        };
    });
});

const previewSubtotal = computed(() => {
    return visibleItems.value.reduce((total, item) => {
        return total + Number(item.displaySubtotal);
    }, 0);
});

const previewTotal = computed(() => {
    return previewSubtotal.value;
});

function getQuantity(item) {
    return draftQuantities[item.product.id] ?? item.quantity ?? 1;
}

function setQuantity(productId, value, stock) {
    const numericValue = Number(value);

    if (!Number.isFinite(numericValue)) {
        draftQuantities[productId] = 1;
        return;
    }

    draftQuantities[productId] = Math.max(0, Math.min(numericValue, Number(stock)));

    scheduleCartUpdate();
}

function scheduleCartUpdate() {
    clearTimeout(saveTimeout);

    saveTimeout = setTimeout(() => {
        syncCartWithServer();
    }, 450);
}

async function syncCartWithServer() {
    if (!cartStore.items.length) {
        return;
    }

    const payload = {};

    cartStore.items.forEach((item) => {
        payload[item.product.id] = {
            quantity: getQuantity(item),
        };
    });

    isSaving.value = true;

    try {
        await cartStore.update(payload);
        clearDraftQuantities();
        await cartStore.load();
    } finally {
        isSaving.value = false;
    }
}

async function removeItem(product) {
    await cartStore.remove(product);
    clearDraftQuantities();
    await cartStore.load();
}

function clearDraftQuantities() {
    Object.keys(draftQuantities).forEach((key) => {
        delete draftQuantities[key];
    });
}

function formatPrice(value) {
    return Number(value).toLocaleString('es-CR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
}
</script>