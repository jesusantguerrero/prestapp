<template>
  <PaymentFormModal
    v-if="paymentModalState"
    v-bind="paymentModalState?.data"
    v-model="isOpen"
    @saved="router.reload()"
  />

  <TransactionModal
    v-model:show="isTransactionModalOpen"
    v-bind="transactionModalState"
    @saved="onTransactionSaved"
    @close="onTransactionSaved"
  />
</template>

<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import PaymentFormModal from "@/Pages/Loans/Partials/PaymentFormModal.vue";
import TransactionModal from "@/Components/shared/TransactionModal.vue";

import {
  usePaymentModal,
  modalState as paymentModalState,
} from "@/Modules/transactions/usePaymentModal";
import {
  useTransactionModal,
  transactionModalState,
} from "@/Modules/transactions/useTransactionModal";

const { isOpen } = usePaymentModal();

const { isOpen: isTransactionModalOpen, closeTransactionModal } = useTransactionModal();
const onTransactionSaved = () => {
  Inertia.reload();
  closeTransactionModal();
};
</script>
