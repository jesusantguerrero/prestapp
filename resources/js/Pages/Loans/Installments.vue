<script setup lang="ts">
import { ref } from "vue";

import InstallmentTable from "./Partials/InstallmentTable.vue";

import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { ILoanWithInstallments } from "@/Modules/loans/loanEntity";
import LoanTemplate from "./Partials/LoanTemplate.vue";

export interface Props {
  loans: ILoanWithInstallments;
  currentTab: string;
  stats: Object;
}

defineProps<Props>();
// payment related things
type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
  documents: any[];
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);
const onPayment = (installment: ILoanInstallment) => {
  selectedPayment.value = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_due) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  isPaymentModalOpen.value = true;
};
</script>

<template>
  <LoanTemplate :loans="loans" :current-tab="currentTab" :stats="stats">
    <section class="mt-12 px-4 overflow-hidden bg-white rounded-md shadow-md">
      <InstallmentTable
        class="mt-0"
        :installments="loans.installments"
        accept-payment
        @pay="onPayment"
      />
    </section>
  </LoanTemplate>
</template>
