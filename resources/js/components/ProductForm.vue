<template>
    <form class="form-card" @submit.prevent="submit">
        <Message
            v-for="error in errors"
            :key="error"
            severity="error"
            class="mb-3"
        >
            {{ error }}
        </Message>

        <div class="grid">
            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Categoría</label>
                <Select
                    v-model="form.category_id"
                    :options="categoryOptions"
                    optionLabel="label"
                    optionValue="id"
                    placeholder="Seleccione una categoría"
                />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Nombre del producto</label>
                <InputText v-model="form.name" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Productor</label>
                <InputText v-model="form.producer" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Imagen del producto</label>
                <FileUpload
                    mode="basic"
                    accept="image/png,image/jpeg,image/jpg,image/webp"
                    chooseLabel="Seleccionar imagen"
                    customUpload
                    @select="selectImage"
                />
                <small>Formatos permitidos: jpg, jpeg, png o webp.</small>
            </div>

            <div class="col-12 flex flex-column gap-2">
                <label>Descripción</label>
                <Textarea v-model="form.description" rows="5" autoResize required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Precio</label>
                <InputNumber v-model="form.price" mode="decimal" :min="0" :minFractionDigits="2" :maxFractionDigits="2" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Tipo</label>
                <Select v-model="form.wine_type" :options="wineTypeOptions" optionLabel="label" optionValue="value" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Uva</label>
                <InputText v-model="form.grape" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>País</label>
                <Select v-model="form.country" :options="countryOptions" optionLabel="label" optionValue="value" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Región</label>
                <InputText v-model="form.region" required />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Denominación</label>
                <InputText v-model="form.appellation" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Año / añada</label>
                <InputNumber v-model="form.vintage_year" :min="1900" :max="2035" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Volumen</label>
                <Select v-model="form.volume_ml" :options="volumeOptions" optionLabel="label" optionValue="value" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Alcohol %</label>
                <InputNumber
                    v-model="form.alcohol_percentage"
                    mode="decimal"
                    :min="5"
                    :max="20"
                    :minFractionDigits="1"
                    :maxFractionDigits="1"
                />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Stock</label>
                <InputNumber v-model="form.stock" :min="0" :max="999" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Condición</label>
                <Select v-model="form.condition" :options="conditionOptions" optionLabel="label" optionValue="value" />
            </div>

            <div class="col-12 md:col-6 flex flex-column gap-2">
                <label>Fuente de reseña</label>
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

            <div class="col-12 flex align-items-center gap-2">
                <label class="flex align-items-center gap-2 m-0">
                    <Checkbox v-model="form.is_featured" binary />
                    <span>Mostrar como producto destacado</span>
                </label>
            </div>
        </div>

        <div class="form-actions flex flex-wrap gap-3">
            <Button type="submit" :label="submitLabel" class="action-dark" />

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
import { computed, reactive, watch, ref } from 'vue';
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
        default: 'Guardar producto',
    },
});

const emit = defineEmits(['submit']);

const errors = ref([]);
const router = useRouter();

const categoryOptions = computed(() => {
    return props.categories.map((category) => ({
        ...category,
        label: `${category.type} - ${category.name}`,
    }));
});

const wineTypeOptions = [
    { label: 'Tinto', value: 'Tinto' },
    { label: 'Blanco', value: 'Blanco' },
    { label: 'Rosado', value: 'Rosado' },
];

const countryOptions = [
    { label: 'Francia', value: 'Francia' },
    { label: 'Chile', value: 'Chile' },
    { label: 'Argentina', value: 'Argentina' },
    { label: 'España', value: 'España' },
    { label: 'Italia', value: 'Italia' },
    { label: 'Nueva Zelanda', value: 'Nueva Zelanda' },
    { label: 'EE.UU.', value: 'EE.UU.' },
    { label: 'Australia', value: 'Australia' },
];

const volumeOptions = [
    { label: '375 ml', value: 375 },
    { label: '750 ml', value: 750 },
    { label: '1500 ml', value: 1500 },
];

const conditionOptions = [
    { label: 'Excelente', value: 'Excelente' },
    { label: 'Muy bueno', value: 'Muy bueno' },
    { label: 'Bueno', value: 'Bueno' },
];

const form = reactive({
    category_id: '',
    name: '',
    producer: '',
    description: '',
    image: null,
    price: '',
    wine_type: 'Tinto',
    grape: '',
    country: 'Francia',
    region: '',
    appellation: '',
    vintage_year: '',
    volume_ml: 750,
    alcohol_percentage: 12,
    stock: 0,
    condition: 'Excelente',
    rating_source: '',
    rating_score: '',
    is_featured: false,
});

watch(
    () => props.product,
    (product) => {
        if (!product) return;

        Object.assign(form, {
            category_id: product.category_id || '',
            name: product.name || '',
            producer: product.producer || '',
            description: product.description || '',
            image: null,
            price: product.price || '',
            wine_type: product.wine_type || 'Tinto',
            grape: product.grape || '',
            country: product.country || 'Francia',
            region: product.region || '',
            appellation: product.appellation || '',
            vintage_year: product.vintage_year || '',
            volume_ml: product.volume_ml || 750,
            alcohol_percentage: product.alcohol_percentage || 12,
            stock: product.stock || 0,
            condition: product.condition || 'Excelente',
            rating_source: product.rating_source || '',
            rating_score: product.rating_score || '',
            is_featured: !!product.is_featured,
        });
    },
    { immediate: true }
);

function selectImage(event) {
    const files = event.files || [];
    form.image = files[0] || null;
}

function submit() {
    errors.value = [];

    const data = new FormData();

    Object.entries(form).forEach(([key, value]) => {
        if (key === 'image') {
            if (value) data.append(key, value);
            return;
        }

        if (value === null || value === undefined) {
            data.append(key, '');
            return;
        }

        if (key === 'is_featured') {
            data.append(key, value ? '1' : '0');
            return;
        }

        data.append(key, value);
    });

    emit('submit', data, errors);
}
</script>
