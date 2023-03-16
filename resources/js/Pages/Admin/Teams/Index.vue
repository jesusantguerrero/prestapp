<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed, ref, toRefs } from "vue";
import { router } from "@inertiajs/core";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import cols from "./cols";
import { IRent } from "@/Modules/property/propertyEntity";
import { getRangeParams } from "@/utils";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";

interface IPaginatedData {
  data: IRent[];
}

const props = defineProps<{
  teams: IRent[] | IPaginatedData;
  serverSearchOptions: IServerSearchData;
}>();

const { serverSearchOptions } = toRefs(props);
const {
  executeSearch,
  updateSearch,
  changeSize,
  paginate,
  reset,
  state: searchState,
} = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/rents?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const filters = ref({});
const expiringRanges = [
  {
    text: "Este mes",
    range: [30, 0],
  },
  {
    text: "3 Meses",
    range: [90, 0],
  },
  {
    text: "Last 6 months",
    range: [180, 0],
  },
];

const setRange = (field: string, range: number[]) => {
  const params = getRangeParams(field, range);
  router.get(
    `/admin/teams?${params}`,
    {},
    {
      preserveState: true,
    }
  );
};
const listData = computed(() => {
  return Array.isArray(props.teams) ? props.teams : props.teams.data;
});

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};
</script>

<template>
  <AppLayout title="Empresas">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-16">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <BaseSelect
          placeholder="Expira en"
          class="min-w-max"
          :options="expiringRanges"
          v-model="filters.end_date"
          label="text"
          track-by="text"
          @update:model-value="setRange('end_date', $event.range)"
        />
        <AppButton @click="router.visit(route('rents.create'))"
          >Agregar Contrato</AppButton
        >
      </section>
      <AtTable
        class="bg-white rounded-md text-body-1 mt-4"
        :table-data="listData"
        :cols="cols"
        :pagination="searchState"
        :total="teams.total"
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
        :config="tableConfig"
      >
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex justify-end items-center">
            <UnitTag :status="row.status" />

            <Link
              class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/rents/${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <div class="flex">
              <AppButton
                class="hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400"
                variant="neutral"
                @click="router.visit(`/property/${row.property_id}`)"
              >
                <IMdiFile />
              </AppButton>
              <!-- <AppButton variant="neutral"><IMdiFile /></AppButton>
              <AppButton variant="neutral"><IMdiFile /></AppButton> -->
            </div>
            <AppButton
              variant="neutral"
              class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
              @click="deleteUnit(row)"
            >
              <IMdiTrash />
            </AppButton>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
