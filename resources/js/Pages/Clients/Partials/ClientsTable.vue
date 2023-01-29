<script setup lang="ts">
import { IPaginatedData } from "@/utils/constants";

import AtTable from "@/Components/shared/BaseTable.vue";

import { IClient } from "@/Modules/clients/clientEntity.ts";
import cols from "./cols";

defineProps<{
  clients: IClient[] | IPaginatedData<IClient>;
  pagination: Object;
  total: number;
}>();

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};
</script>

<template>
  <section class="mt-5">
    <AtTable
      :table-data="clients"
      :cols="cols"
      :config="tableConfig"
      :pagination="pagination"
      :total="total"
      @search="$emit('search')"
      @paginate="$emit('paginate', $event)"
      @size-change="$emit('size-change', $event)"
    />
  </section>
</template>
