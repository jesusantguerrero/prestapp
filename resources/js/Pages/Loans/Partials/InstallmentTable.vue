<script setup lang="ts">
import AppButton from "@/Components/shared/AppButton.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";

import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import cols from "./installmentCols";

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

  const defaultConcept = `Pago prestamo ${installment.loan_id} (${installment.installment_number}/${props.installments.length})`;

  openModal({
    data: {
      title: `Pagar prestamo`,
      endpoint: `/loans/${installment.loan_id}/installments/${installment.id}/pay`,
      due: payment.amount,
      account_id: payment.account_id,
      payment,
      defaultConcept,
    },
  });
  emit("pay", installment);
};
</script>

<template>
  <BaseTable :cols="cols" :table-data="installments">
    <template v-slot:actions="{ scope: { row } }">
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
