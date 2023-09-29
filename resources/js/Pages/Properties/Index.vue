<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { Link } from "@inertiajs/vue3";
import { computed, toRefs, ref } from "vue";

import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import BudgetProgress from "@/Components/BudgetProgress.vue";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { IPaginatedData } from "@/utils/constants";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";
import { ElMessageBox } from "element-plus";
import { IProperty } from "@/Modules/properties/propertyEntity";
import cols from "./Partials/propertyCols";
import { useI18n } from "vue-i18n";

const props = defineProps<{
  properties: ILoan[] | IPaginatedData<ILoan>;
  serverSearchOptions: IServerSearchData;
}>();

const listData = computed(() => {
  return Array.isArray(props.properties) ? props.properties : props.properties.data;
});

const { serverSearchOptions } = toRefs(props);
const { executeSearch, updateSearch, reset, state: searchState } = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/properties?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const { t } = useI18n();
const section = ref("properties");
const sections: Record<string, any> = {
  units: {
    label: t("Units"),
    link: "/units?filter[status]=RENTED",
  },
  properties: {
    label: t("Properties"),
    link: "/properties",
  },
};
const handleChange = (sectionName: string) => {
  router.get(sections[sectionName].link);
};

const deleteProperty = async (property: IProperty) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar la propiedad ${property.name}?`,
    "Eliminar propiedad"
  );
  if (isValid) {
    router.delete(route("properties.destroy", property), {
      onSuccess() {
        router.reload();
      },
    });
  }
};
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-16 -mx-4 md:mx-0">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <ButtonGroup
          class="w-full md:w-fit"
          @update:modelValue="handleChange"
          :values="sections"
          v-model="section"
        />
        <AppButton variant="secondary" @click="router.visit(route('properties.create'))">
          <IMdiPlus />
          <span class="hidden md:inline-block">
            {{ $t("add property") }}
          </span>
        </AppButton>
      </section>
      <AtTable
        :table-data="listData"
        :cols="cols($t)"
        class="mt-4 md:bg-white rounded-md text-body-1"
      >
        <template v-slot:status="{ scope: { row } }" class="flex">
          <div>
            <BudgetProgress
              :goal="row.unit_count"
              :current="row.available_units"
              class="h-2.5 text-white rounded-md"
              :progress-class="['bg-primary', 'bg-primary/5']"
              :show-labels="false"
            >
              <template v-slot:before="{ progress }">
                <header class="flex justify-between mb-1 font-bold">
                  <div class="text-sm">
                    <span class="text-secondary">
                      {{ row.available_units }}
                      {{ $t("available") }}
                    </span>
                    {{ $t("of") }} {{ row.unit_count }} {{ $t("Units") }}
                  </div>
                  <span class="text-primary">{{ progress }}% </span>
                </header>
              </template>
            </BudgetProgress>
          </div>
        </template>
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex justify-end">
            <Link
              class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/properties/${row.id}`"
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
              <!-- <AppButton variant="neutral"><IMdiFile /></AppButton>
              <AppButton variant="neutral"><IMdiFile /></AppButton> -->
            </div>
            <AppButton
              variant="neutral"
              @click="deleteProperty(row)"
              class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
            >
              <IMdiTrash />
            </AppButton>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
