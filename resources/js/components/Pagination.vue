<template>
    <div v-if="meta && meta.last_page > 1" class="pagination-container">
        <p class="pagination-info">
            Mostrando {{ meta.from }} a {{ meta.to }} de {{ meta.total }} resultados
        </p>

        <Paginator
            :first="(meta.current_page - 1) * meta.per_page"
            :rows="meta.per_page"
            :totalRecords="meta.total"
            :template="paginatorTemplate"
            @page="onPage"
        />
    </div>
</template>

<script setup>
const paginatorTemplate = 'PrevPageLink PageLinks NextPageLink';

defineProps({
    meta: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['change']);

function onPage(event) {
    emit('change', event.page + 1);
}
</script>