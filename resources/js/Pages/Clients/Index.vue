<script setup lang="ts">
import AppLayout from '@/Components/templates/AppLayout.vue';
import { IClient } from "@/Modules/clients/clientEntity.ts";

defineProps<{
    data: IClient[]
}>()
</script>

<template>
    <AppLayout>
        <h1> Clients</h1>
        <main>
            <div v-for="client in data">
                {{ client.name }}
            </div>
        </main>
    </AppLayout>

</template>
