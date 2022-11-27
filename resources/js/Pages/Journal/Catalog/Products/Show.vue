<template>
    <EditProductForm v-bind="$props" />
</template>

<script setup>
    import EditProductForm from './Edit.vue'
    defineProps({
        product: {
            type: Object,
            required: true
        },
        availableTaxes: {
            type: Array,
            required: true
        }
    });
</script>
