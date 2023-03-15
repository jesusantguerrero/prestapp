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
import { useI18n } from "vue-i18n";
import { onMounted, ref } from "vue";

interface Props {
  installments: ILoanInstallment[];
  loanId?: number;
  hiddenCols?: string[];
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

const { t } = useI18n();
const columns = cols(t);
</script>

<template>
  <BaseTable :cols="columns" :table-data="installments" :hidden-cols="hiddenCols">
    <template v-slot:actions="{ scope: { row } }">
      <div class="flex space-x-2 justify-end">
        <AppButton
          @click="handlePayment(row)"
          variant="inverse-secondary"
          class="flex items-center justify-center"
          v-if="row?.payment_status !== 'PAID' || loanId"
        >
          <IIcSharpPayment />
        </AppButton>
      </div>
    </template>
  </BaseTable>
</template>
