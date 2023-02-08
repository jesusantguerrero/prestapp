<script setup lang="ts">
import { IPaginatedData } from "@/utils/constants";

import AtTable from "@/Components/shared/BaseTable.vue";

import { IClient } from "@/Modules/clients/clientEntity.ts";
import cols from "./cols";
import ClientCard from "./ClientCard.vue";

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
      responsive
      @search="$emit('search')"
      @paginate="$emit('paginate', $event)"
      @size-change="$emit('size-change', $event)"
    >
      <template v-slot:card="{ row }">
        <ClientCard :client="row" />
      </template>
    </AtTable>
  </section>
</template>
