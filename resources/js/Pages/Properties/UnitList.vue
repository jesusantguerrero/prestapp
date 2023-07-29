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
import { ElMessageBox, ElNotification } from "element-plus";
import { useResponsive } from "@/utils/useResponsive";
import ButtonCircle from "@/Components/mobile/ButtonCircle.vue";

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

const deleteUnit = async (unit: IUnit) => {
  const isConfirmed = await ElMessageBox.confirm(
    `Estas seguro de eliminar la unidad ${unit.name}?`,
    "Eliminar unidad"
  );

  if (!isConfirmed) return;
  router.delete(`/properties/${unit.property_id}/units/${unit.id}`, {
    onSuccess() {
      ElNotification({
        message: `Unidad ${unit.name} borrada con exito`,
        title: "Unidad eliminada",
        type: "success",
      });
    },
  });
};

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

const { isMobile } = useResponsive();
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-16 -mx-4 md:mx-0">
      <section class="grid grid-cols-2 md:flex md:space-x-4 gap-4 md:gap-0">
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
          class="hidden md:flex w-full md:w-fit"
          @update:modelValue="handleChange"
          :values="sections"
          v-model="section"
        />
        <AppButton
          class="hidden"
          variant="secondary"
          @click="router.visit(route('properties.create'))"
        >
          <IMdiPlus />
          <span class="hidden md:inline-block">
            {{ $t("add property") }}
          </span>
        </AppButton>
      </section>

      <AtTable
        class="mt-4 md:bg-white rounded-md text-body-1"
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
      <div class="fixed bottom-16 right-5 flex space-x-2 z-50" v-if="isMobile">
        <ButtonCircle @click="router.visit(route('properties.create'))">
          <IMdiPlus />
        </ButtonCircle>
      </div>
    </main>
  </AppLayout>
</template>
