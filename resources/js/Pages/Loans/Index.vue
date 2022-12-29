<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
// @ts-ignore: its my template
import AtTable from "@/Components/AtTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import cols from "./cols";
import { formatMoney } from "@/utils";
import { ElMessageBox } from "element-plus";

interface IPaginatedData {
  data: ILoan[];
}

const props = defineProps<{
  loans: ILoan[] | IPaginatedData;
}>();

const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});

const deleteLoan = async (loan: ILoan) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el prestamo ${loan.id} por ${loan.amount} a ${loan.client?.fullName}?`,
    "Eliminar prestamo"
  );
  if (isValid) {
    router.delete(route("loans.destroy", loan), {
      onSuccess() {
        router.reload();
      },
    });
  }
};
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
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
      >
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex">
            <Link
              class="relative inline-block px-5 py-2 overflow-hidden font-bold text-white transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary"
              :href="`/loans/${row.id}`"
            >
              Edit</Link
            >
            <AppButton @click="deleteLoan(row)"> Delete </AppButton>
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
    </main>
  </AppLayout>
</template>
