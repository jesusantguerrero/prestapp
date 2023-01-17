<script setup lang="ts">
import { IPaginatedData } from "@/utils/constants";
import { computed, ref } from "vue";

import ClientFormModal from "./ClientFormModal.vue";
import AtTable from "@/Components/AtTable.vue";

import { IClient } from "@/Modules/clients/clientEntity.ts";
import cols from "./cols";

const props = defineProps<{
  clients: IClient[] | IPaginatedData<IClient>;
}>();

const isModalOpen = ref(false);
</script>

<template>
  <section class="mt-5">
    <AtTable :table-data="clients" :cols="cols" />
    <!-- <ElTable :data="clients">
      <ElTableColumn prop="id" label="#" />
      <ElTableColumn prop="cellphone" label="Celular" />
      <ElTableColumn prop="address_details" label="Direccion" />
      <ElTableColumn prop="status" label="Estatus" />
    </ElTable> -->

    <ClientFormModal v-model:show="isModalOpen" />
  </section>
</template>
