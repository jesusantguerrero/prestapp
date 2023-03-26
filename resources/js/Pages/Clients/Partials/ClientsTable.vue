<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";

import AtTable from "@/Components/shared/BaseTable.vue";
import ClientCard from "./ClientCard.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import cols from "./cols";
import { IPaginatedData } from "@/utils/constants";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { getClientLink } from "@/Modules/clients/constants";

const { t } = useI18n();

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

const columns = cols(t);

getClientLink;
</script>

<template>
  <section class="mt-5">
    <AtTable
      :table-data="clients"
      :cols="columns"
      :config="tableConfig"
      :pagination="pagination"
      :total="total"
      responsive
      @search="$emit('search')"
      @paginate="$emit('paginate', $event)"
      @size-change="$emit('size-change', $event)"
    >
      <template v-slot:card="{ row }">
        <ClientCard :client="row" :label="$t('commons.ACTIVE')" />
      </template>

      <template v-slot:actions="{ scope: { row } }">
        <div class="flex justify-end items-center">
          <Link
            class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
            :href="getClientLink(row)"
          >
            <IMdiChevronRight />
          </Link>
          <div class="flex">
            <AppButton
              class="hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400"
              variant="neutral"
              v-if="row.contract"
              @click="router.visit(getClientLink(row))"
            >
              <IMdiFile />
            </AppButton>
          </div>
          <AppButton
            variant="neutral"
            class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
            @click="$emit('delete', row)"
          >
            <IMdiTrash />
          </AppButton>
        </div>
      </template>
    </AtTable>
  </section>
</template>
