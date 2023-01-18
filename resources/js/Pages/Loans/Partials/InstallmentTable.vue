<script setup lang="ts">
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import AppButton from "@/Components/shared/AppButton.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";

import cols from "./installmentCols";

interface Props {
  installments: ILoanInstallment[];
  loanId?: number;
}
defineProps<Props>();

const emit = defineEmits(["pay"]);

const handlePayment = (installment: ILoanInstallment) => {
  emit("pay", installment);
};
</script>

<template>
  <BaseTable :cols="cols" :table-data="installments">
    <template v-slot:actions="{ row }">
      <div class="p-2">
        <AppButton
          @click="handlePayment(row)"
          v-if="row?.payment_status !== 'PAID' || loanId"
        >
          Pagar
        </AppButton>
      </div>
    </template>
  </BaseTable>
</template>
