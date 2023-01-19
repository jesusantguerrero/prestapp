<script setup lang="ts">
import { IPaginatedData } from "@/utils/constants";

import AtTable from "@/Components/AtTable.vue";

import { IClient } from "@/Modules/clients/clientEntity.ts";
import cols from "./cols";

defineProps<{
  clients: IClient[] | IPaginatedData<IClient>;
}>();
</script>

<template>
  <section class="mt-5">
    <AtTable :table-data="clients" :cols="cols" />
  </section>
</template>
