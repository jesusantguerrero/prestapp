<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { AtButton } from "atmosphere-ui";
import { ref } from "vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import ClientFormModal from "./Partials/ClientFormModal.vue";

defineProps<{
  data: IClient[];
}>();

const isModalOpen = ref(false);
</script>

<template>
  <AppLayout title="Clientes">
    <main class="p-5">
      <AppSectionHeader name="Clientes" class="rounded-md" @create="isModalOpen = true" />

      <section class="mt-5">
        <ElTable :data="data">
          <ElTableColumn prop="names" label="Nombres" />
          <ElTableColumn prop="lastnames" label="Apellidos" />
          <ElTableColumn prop="dni" label="DNI/ID Doc." />
          <ElTableColumn prop="cellphone" label="Celular" />
          <ElTableColumn prop="address_details" label="Direccion" />
        </ElTable>
      </section>

      <ClientFormModal v-model:show="isModalOpen" />
    </main>
  </AppLayout>
</template>
