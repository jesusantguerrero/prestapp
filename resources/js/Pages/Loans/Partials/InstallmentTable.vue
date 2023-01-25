<script setup lang="ts">
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore
import BaseTable from "@/Components/shared/BaseTable.vue";
// @ts-ignore
import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import {
  ILoanInstallment,
  ILoanInstallmentSaved,
} from "@/Modules/loans/loanInstallmentEntity";
import cols from "./installmentCols";
import { router } from "@inertiajs/core";

interface Props {
  installments: ILoanInstallment[];
  loanId?: number;
}

const props = defineProps<Props>();

const emit = defineEmits(["pay"]);

const { openModal } = usePaymentModal();

const handlePayment = (installment: ILoanInstallment) => {
  const payment = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_due) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  const defaultConcept = `Pago prestamo ${installment.loan_id} (${installment.number}/${props.installments.length})`;

  openModal({
    data: {
      title: `Pagar prestamo`,
      endpoint: `/loans/${installment.loan_id}/installments/${installment.id}/pay`,
      due: payment.amount,
      account_id: payment.account_id,
      payment,
      defaultConcept,
      accountsEndpoint: "/loan-accounts",
    },
  });
  emit("pay", installment);
};

const onUpdateRepayment = (repayment: ILoanInstallmentSaved) => {
  router.post(`/loans/${repayment.loan_id}/installments/${repayment.id}/update-status`);
};
</script>

<template>
  <BaseTable :cols="cols" :table-data="installments">
    <template v-slot:actions="{ scope: { row } }">
      <div class="flex space-x-2 justify-end">
        <AppButton
          @click="handlePayment(row)"
          variant="inverse-secondary"
          class="flex items-center"
          v-if="row?.payment_status !== 'PAID' || loanId"
        >
          <IIcSharpPayment class="mr-2" />
          Pagar
        </AppButton>
      </div>
    </template>
  </BaseTable>
</template>
