<script setup>
import AppLayout from '@/Components/templates/AppLayout.vue';

defineProps({
    data: {
        type: Array
    }
})
</script>

<template>
    <AppLayout>
        <h1> Clients</h1>
        <main>
            {{ data }}

        </main>
    </AppLayout>

</template>