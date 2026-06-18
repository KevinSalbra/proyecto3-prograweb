<template>
    <form class="form-card" @submit.prevent="submitForm">
        <Message
            v-for="error in errors"
            :key="error"
            severity="error"
            class="mb-3"
        >
            {{ error }}
        </Message>

        <div class="form-section-title">
            <h2>Información principal</h2>
            <p>Ingrese los datos básicos del producto.</p>
        </div>

        <div class="grid">
            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Categoría</label>
                <Select
                    v-model="form.category_id"
                    :options="categoryOptions"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Seleccione una categoría"
                    required
                />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Nombre</label>
                <InputText v-model="form.name" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Productor</label>
                <InputText v-model="form.producer" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Precio</label>
                <InputNumber
                    v-model="form.price"
                    mode="decimal"
                    :min="0"
                    :minFractionDigits="2"
                    :maxFractionDigits="2"
                    required
                />
            </div>

            <div class="col-12 flex flex-column gap-2">
                <label>Descripción</label>
                <Textarea
                    v-model="form.description"
                    rows="5"
                    autoResize
                    required
                />
            </div>
        </div>

        <div class="form-section-title">
            <h2>Características</h2>
            <p>Complete la información técnica del vino o champán.</p>
        </div>

        <div class="grid">
            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Tipo</label>
                <Select
                    v-model="form.wine_type"
                    :options="wineTypes"
                    optionLabel="label"
                    optionValue="value"
                    required
                />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Uva</label>
                <InputText v-model="form.grape" required />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>País</label>
                <Select
                    v-model="form.country"
                    :options="countries"
                    optionLabel="label"
                    optionValue="value"
                    required
                />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Región</label>
                <InputText v-model="form.region" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Denominación</label>
                <InputText v-model="form.appellation" />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Añada</label>
                <InputNumber
                    v-model="form.vintage_year"
                    :useGrouping="false"
                    :min="1900"
                    :max="2035"
                />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Volumen</label>
                <Select
                    v-model="form.volume_ml"
                    :options="volumes"
                    optionLabel="label"
                    optionValue="value"
                    required
                />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Alcohol</label>
                <InputNumber
                    v-model="form.alcohol_percentage"
                    mode="decimal"
                    suffix="%"
                    :min="5"
                    :max="20"
                    :minFractionDigits="1"
                    :maxFractionDigits="1"
                    required
                />
            </div>
        </div>

        <div class="form-section-title">
            <h2>Inventario e imagen</h2>
            <p>Defina la disponibilidad, valoración e imagen del producto.</p>
        </div>

        <div class="grid">
            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Stock</label>
                <InputNumber
                    v-model="form.stock"
                    :useGrouping="false"
                    :min="0"
                    :max="999"
                    required
                />
            </div>

            <div class="col-12 md:col-4 flex flex-column gap-2">
                <label>Condición</label>
                <Select
                    v-model="form.condition"
                    :options="conditions"
                    optionLabel="label"
                    optionValue="value"
                    required
                />
            </div>

            <div class="col-12 md:col-4 flex align-items-center gap-3 pt-4">
                <Checkbox
                    v-model="form.is_featured"
                    inputId="is_featured"
                    binary
                />

                <label for="is_featured">Producto destacado</label>
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Fuente de calificación</label>
                <InputText v-model="form.rating_source" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Puntaje</label>
                <InputNumber
                    v-model="form.rating_score"
                    mode="decimal"
                    :min="0"
                    :max="100"
                    :minFractionDigits="1"
                    :maxFractionDigits="1"
                />
            </div>

            <div class="col-12 flex flex-column gap-2">
                <label>Imagen</label>

                <FileUpload
                    mode="basic"
                    accept="image/*"
                    chooseLabel="Seleccionar imagen"
                    :auto="false"
                    customUpload
                    @select="onImageSelect"
                />

                <small v-if="product?.image_url">
                    Imagen actual:
                    <a :href="product.image_url" target="_blank">
                        ver imagen
                    </a>
                </small>
            </div>
        </div>

        <div class="form-actions flex flex-wrap gap-3">
            <Button
                type="submit"
                :label="submitLabel"
                class="action-dark"
            />

            <Button
                type="button"
                label="Cancelar"
                class="action-light"
                @click="router.push('/productos')"
            />
        </div>
    </form>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue';
import { useRouter } from 'vue-router';

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        required: true,
    },
    submitLabel: {
        type: String,
        default: 'Guardar',
    },
});

const emit = defineEmits(['submit']);
const router = useRouter();
const errors = ref([]);
const selectedImage = ref(null);

const form = reactive({
    category_id: '',
    name: '',
    producer: '',
    description: '',
    price: 0,
    wine_type: 'Tinto',
    grape: '',
    country: 'Francia',
    region: '',
    appellation: '',
    vintage_year: null,
    volume_ml: 750,
    alcohol_percentage: 12.0,
    stock: 0,
    condition: 'Excelente',
    rating_source: '',
    rating_score: null,
    is_featured: false,
});

const categoryOptions = computed(() => {
    return props.categories.map((category) => ({
        id: category.id,
        label: `${category.type} - ${category.name}`,
    }));
});

const wineTypes = [
    { label: 'Tinto', value: 'Tinto' },
    { label: 'Blanco', value: 'Blanco' },
    { label: 'Rosado', value: 'Rosado' },
];

const countries = [
    { label: 'Francia', value: 'Francia' },
    { label: 'Chile', value: 'Chile' },
    { label: 'Argentina', value: 'Argentina' },
    { label: 'España', value: 'España' },
    { label: 'Italia', value: 'Italia' },
    { label: 'Nueva Zelanda', value: 'Nueva Zelanda' },
    { label: 'EE.UU.', value: 'EE.UU.' },
    { label: 'Australia', value: 'Australia' },
];

const volumes = [
    { label: '375 ml', value: 375 },
    { label: '750 ml', value: 750 },
    { label: '1500 ml', value: 1500 },
];

const conditions = [
    { label: 'Excelente', value: 'Excelente' },
    { label: 'Muy bueno', value: 'Muy bueno' },
    { label: 'Bueno', value: 'Bueno' },
];

watch(
    () => props.product,
    (product) => {
        if (!product) {
            return;
        }

        Object.assign(form, {
            category_id: product.category_id,
            name: product.name,
            producer: product.producer,
            description: product.description,
            price: Number(product.price),
            wine_type: product.wine_type,
            grape: product.grape,
            country: product.country,
            region: product.region,
            appellation: product.appellation || '',
            vintage_year: product.vintage_year,
            volume_ml: product.volume_ml,
            alcohol_percentage: Number(product.alcohol_percentage),
            stock: product.stock,
            condition: product.condition,
            rating_source: product.rating_source || '',
            rating_score: product.rating_score !== null ? Number(product.rating_score) : null,
            is_featured: Boolean(product.is_featured),
        });
    },
    { immediate: true }
);

function onImageSelect(event) {
    selectedImage.value = event.files?.[0] || null;
}

function submitForm() {
    errors.value = [];

    const formData = new FormData();

    Object.entries(form).forEach(([key, value]) => {
        if (value === null || value === undefined) {
            formData.append(key, '');
            return;
        }

        if (typeof value === 'boolean') {
            formData.append(key, value ? '1' : '0');
            return;
        }

        formData.append(key, value);
    });

    if (selectedImage.value) {
        formData.append('image', selectedImage.value);
    }

    emit('submit', formData, errors);
}
</script>