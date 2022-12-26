<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { ILoan } from "../../Modules/loans/loanEntity";
import { computed } from "vue";
import cols from "./cols";
import AtTable from "../../Components/AtTable.vue";
import AppButton from "../../Components/shared/AppButton.vue";
import { Link } from "@inertiajs/vue3";
import LoanSectionNav from "./Partials/LoanSectionNav.vue";

interface IPaginatedData {
    data: ILoan[]
}

const props = defineProps<{
  loans: ILoan[] | IPaginatedData;
}>();

const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});
</script>

<template>
  <AppLayout title="Prestamos">
    <template #header>
      <LoanSectionNav>
          <template #actions>
            <AppButton variant="inverse" @click="router.visit('/loans/create')">
              Nuevo prestamo
            </AppButton>
          </template>
      </LoanSectionNav>
    </template>

    <main class="pt-16">
        <AtTable :table-data="listData" :cols="cols" class="bg-white rounded-md text-body-1">
          <template v-slot:actions="{ scope: { row } }" class="flex">
            <div class="flex">
              <Link
                class="relative inline-block px-5 py-2 overflow-hidden font-bold text-white transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary"
                :href="`/loans/${row.id}`"
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
