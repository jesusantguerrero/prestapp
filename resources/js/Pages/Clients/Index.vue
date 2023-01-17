<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { IPaginatedData } from "@/utils/constants";
import { computed, ref } from "vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import ClientsTable from "./Partials/ClientsTable.vue";

const props = defineProps<{
  clients: IClient[] | IPaginatedData<IClient>;
}>();

const listData = computed(() => {
  return Array.isArray(props.clients) ? props.clients : props.clients.data;
});

const isModalOpen = ref(false);
</script>

<template>
  <AppLayout title="Clientes">
    <template #header>
      <AppSectionHeader name="Clientes" class="rounded-md" @create="isModalOpen = true" />
    </template>
    <main class="mt-16 bg-white rounded-md">
      <ClientsTable :clients="listData" />
    </main>
  </AppLayout>
</template>
