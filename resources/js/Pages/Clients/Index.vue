<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { AtButton } from "atmosphere-ui";
import { IPaginatedData } from "@/utils/constants";
import { computed, ref } from "vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import ClientFormModal from "./Partials/ClientFormModal.vue";

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
    <main class="p-5">

      <section class="mt-5">
        <ElTable :data="clients">
          <ElTableColumn prop="id" label="#" />
          <ElTableColumn prop="names" label="Nombres" />
          <ElTableColumn prop="lastnames" label="Apellidos" />
          <ElTableColumn prop="dni" label="DNI/ID Doc." />
          <ElTableColumn prop="cellphone" label="Celular" />
          <ElTableColumn prop="status" label="Estatus" />
          <ElTableColumn prop="address_details" label="Direccion" />
        </ElTable>
      </section>

      <ClientFormModal v-model:show="isModalOpen" />
    </main>
  </AppLayout>
</template>
