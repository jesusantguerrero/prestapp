<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
// @ts-ignore: its my template
import AtTable from "@/Components/AtTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";
import LoanProductModal from "./Partials/LoanProductModal.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import cols from "./loanProductCols";
import { formatMoney } from "@/utils";

interface IPaginatedData {
  data: ILoan[];
}

const props = defineProps<{
  loanProducts: ILoan[] | IPaginatedData;
}>();

const listData = computed(() => {
  return Array.isArray(props.loanProducts) ? props.loanProducts : props.loanProducts.data;
});

const isModalOpen = ref(false);
</script>

<template>
  <AppLayout title="Tipo de prestamos">
    <template #header>
      <LoanSectionNav>
        <template #actions>
          <AppButton variant="inverse" @click="isModalOpen = !isModalOpen">
            Nuevo tipo
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <main class="pt-16">
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
      >
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex space-x-4 mx-auto">
            <Link
              class="relative inline-block px-5 py-2 overflow-hidden font-bold text-white transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary"
              :href="`/loans/${row.id}`"
            >
              Edit</Link
            >
            <AppButton> Delete </AppButton>
          </div>
        </template>
        <template v-slot:amount="{ scope: { row } }">
          <div class="font-bold">
            {{ formatMoney(row.amount) }}
            <p class="font-bold text-green-500">
              {{ formatMoney(row.total) }}
            </p>
          </div>
        </template>
      </AtTable>

      <LoanProductModal v-model:show="isModalOpen" />
    </main>
  </AppLayout>
</template>
