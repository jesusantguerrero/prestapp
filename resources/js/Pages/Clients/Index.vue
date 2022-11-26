<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { AtButton } from "atmosphere-ui";
import { ref } from "vue";
import ClientFormModal from "./Partials/ClientFormModal.vue";

defineProps<{
  data: IClient[];
}>();

const isModalOpen = ref(false);
</script>

<template>
  <AppLayout>
    <main class="p-5">
      <h1>Clients</h1>
      <div v-for="client in data">
        {{ client.names }}
      </div>
      <AtButton class="bg-blue-500 text-white rounded-md" @click="isModalOpen = true">
        Agregar Cliente
      </AtButton>

      <ClientFormModal v-model:show="isModalOpen" />
    </main>
  </AppLayout>
</template>
