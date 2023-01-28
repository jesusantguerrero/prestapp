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

  <ClientFormModal v-model:show="isContactModalOpen" v-bind="contactData" />
</template>

<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import PaymentFormModal from "@/Pages/Loans/Partials/PaymentFormModal.vue";
import TransactionModal from "@/Components/shared/TransactionModal.vue";

import {
  usePaymentModal,
  modalState as paymentModalState,
} from "@/Modules/transactions/usePaymentModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";

import {
  useTransactionModal,
  transactionModalState,
} from "@/Modules/transactions/useTransactionModal";
import ClientFormModal from "@/Pages/Clients/Partials/ClientFormModal.vue";

const { isOpen } = usePaymentModal();

const { isOpen: isTransactionModalOpen, closeTransactionModal } = useTransactionModal();
const onTransactionSaved = () => {
  router.reload();
  closeTransactionModal();
};

const {
  isOpen: isContactModalOpen,
  closeModal: closeClientModal,
  data: contactData,
} = useToggleModal("contact");
const onContactSaved = () => {
  router.reload();
  closeClientModal();
};
</script>
