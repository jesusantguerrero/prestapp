<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { computed, toRefs, reactive, ref } from "vue";

import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";
import UnitTag from "@/Components/realState/UnitTag.vue";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";

import cols from "./Partials/unitCols";
import { IPaginatedData } from "@/utils/constants";
import { IProperty, IUnit } from "@/Modules/properties/propertyEntity";
import { useServerSearch, IServerSearchData } from "@/utils/useServerSearch";
import { Link } from "@inertiajs/vue3";
import { propertyStatus } from "@/Modules/properties/constants";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

const props = defineProps<{
  units: IProperty[] | IPaginatedData<IProperty>;
  serverSearchOptions: IServerSearchData;
}>();

const listData = computed(() => {
  return Array.isArray(props.units)
    ? {
        data: props.units,
      }
    : props.units;
});

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
    updateSearch(`/units?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const filters = reactive({
  status:
    propertyStatus.find((status) => status.name === searchState.filters.status) ??
    propertyStatus[0],
});

const onStateSelected = (status: Record<string, string>) => {
  searchState.filters.status = status.name;
  executeSearch();
};

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};

const deleteUnit = (unit: IUnit) => {};

const section = ref("units");
const sections: Record<string, any> = {
  units: {
    label: "Unidades",
    link: "/units?filter[status]=RENTED",
  },
  properties: {
    label: "Propiedades",
    link: "/properties",
  },
};
const handleChange = (sectionName: string) => {
  router.get(sections[sectionName].link);
};
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <BaseSelect
          placeholder="Filtrar"
          :options="propertyStatus"
          v-model="filters.status"
          label="label"
          track-by="name"
          @update:model-value="onStateSelected"
        />
        <ButtonGroup
          class="w-full md:w-fit"
          @update:modelValue="handleChange"
          :values="sections"
          v-model="section"
        />
        <AppButton variant="secondary" @click="router.visit(route('properties.create'))">
          Agregar Propiedad
        </AppButton>
      </section>
      <AtTable
        class="bg-white rounded-md text-body-1 mt-4"
        :table-data="listData.data"
        :cols="cols"
        :pagination="searchState"
        :total="units.total"
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
        :config="tableConfig"
      >
        <template v-slot:actions="{ scope: { row } }">
          <div class="flex justify-end items-center">
            <UnitTag :status="row.status" />

            <Link
              class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/properties/${row.property_id}?unit=${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <div class="flex">
              <AppButton
                class="hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400"
                variant="neutral"
                v-if="row.contract"
                @click="router.visit(`/rents/${row.contract.id}`)"
              >
                <IMdiFile />
              </AppButton>
              <AppButton
                class="hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400"
                variant="neutral"
                v-else
                @click="router.visit(`/rents/create?unit=${row.id}`)"
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
