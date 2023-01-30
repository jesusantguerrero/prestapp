<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { computed, toRefs } from "vue";
import cols from "./unitCols";
import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import { IPaginatedData } from "@/utils/constants";
import { IProperty } from "@/Modules/properties/propertyEntity";
import { useServerSearch } from "@/utils/useServerSearch";

const props = defineProps<{
  units: IProperty[] | IPaginatedData<IProperty>;
  serverSearchOptions: Object;
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

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton variant="secondary" @click="router.visit(route('properties.create'))"
            >Agregar Propiedad
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <AtTable
        class="bg-white rounded-md text-body-1"
        :table-data="listData.data"
        :cols="cols"
        :pagination="searchState"
        :total="listData.total"
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
        :config="tableConfig"
      />
    </main>
  </AppLayout>
</template>
