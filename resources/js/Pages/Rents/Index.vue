<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { router } from "@inertiajs/core";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import cols from "./cols";
import { ILoan } from "@/Modules/loans/loanEntity";
import { subDays } from "date-fns";
import { dateToIso } from "@/utils";

interface IPaginatedData {
  data: ILoan[];
}

const props = defineProps<{
  loans: ILoan[] | IPaginatedData;
}>();

const filters = ref({});
const DAY = 3600 * 1000 * 24;
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
  const date = new Date();
  const rangeString = range
    .map((dateCount) => dateToIso(subDays(date, dateCount)))
    .join("~");
  const params = `filter[${field}]=${rangeString}`;
  router.get(
    `/rents?${params}`,
    {},
    {
      preserveState: true,
    }
  );
};
const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});
</script>

<template>
  <AppLayout title="Contratos de alquiler">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <BaseSelect
            placeholder="Expira en"
            :options="expiringRanges"
            v-model="filters.end_date"
            label="text"
            track-by="text"
            @update:model-value="setRange('end_date', $event.range)"
          />
          <AppButton @click="router.visit(route('rents.create'))"
            >Agregar Contrato</AppButton
          >
        </template>
      </PropertySectionNav>
    </template>

    <main class="py-16">
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
      >
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex">
            <Link
              class="relative inline-block px-5 py-2 overflow-hidden font-bold text-white transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary"
              :href="`/rents/${row.id}`"
            >
              Edit</Link
            >
            <AppButton> Delete</AppButton>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
