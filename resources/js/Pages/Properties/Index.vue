<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { ILoan } from "../../Modules/loans/loanEntity";
import { computed } from "vue";
import cols from "./propertyCols";
import AtTable from "../../Components/AtTable.vue";
import AppButton from "../../Components/shared/AppButton.vue";
import { Link } from "@inertiajs/vue3";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import { IPaginatedData } from "@/utils/constants";
import BudgetProgress from "@/Components/BudgetProgress.vue";

const props = defineProps<{
  properties: ILoan[] | IPaginatedData<ILoan>;
}>();

const listData = computed(() => {
  return Array.isArray(props.properties) ? props.properties : props.properties.data;
});
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton variant="inverse" @click="router.visit(route('properties.create'))"
            >Agregar Propiedad</AppButton
          >
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
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
                      disponible
                    </span>
                    de {{ row.unit_count }} unidades
                  </div>
                  <span class="text-primary">{{ progress }}% </span>
                </header>
              </template>
            </BudgetProgress>
          </div>
        </template>
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex">
            <Link
              class="relative inline-block px-5 py-2 overflow-hidden font-bold transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary/10 text-primary"
              :href="`/properties/${row.id}`"
            >
              Ver Propiedad
            </Link>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
