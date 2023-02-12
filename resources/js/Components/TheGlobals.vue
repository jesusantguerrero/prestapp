<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import PaymentFormModal from "@/Pages/Loans/Partials/PaymentFormModal.vue";
import TransactionModal from "@/Components/shared/TransactionModal.vue";
import InvoiceFormModal from "@/Pages/Journal/Invoices/Partials/InvoiceFormModal.vue";

import { useToggleModal } from "@/Modules/_app/useToggleModal";
import ClientFormModal from "@/Pages/Clients/Partials/ClientFormModal.vue";
import {
  usePaymentModal,
  modalState as paymentModalState,
} from "@/Modules/transactions/usePaymentModal";
import {
  useTransactionModal,
  transactionModalState,
} from "@/Modules/transactions/useTransactionModal";

const emit = defineEmits(["reload"]);

const { isOpen } = usePaymentModal();
const onPaymentSaved = () => {
  isOpen.value = false;
  emit("reload");
};

const { isOpen: isTransactionModalOpen, closeTransactionModal } = useTransactionModal();
const onTransactionSaved = () => {
  closeTransactionModal();
  emit("reload");
};

//  contact form management
const {
  isOpen: isContactModalOpen,
  closeModal: closeClientModal,
  data: contactData,
} = useToggleModal("contact");

const onContactSaved = () => {
  closeClientModal();
  emit("reload");
};

const {
  isOpen: isInvoiceModalOpen,
  closeModal: closeInvoiceModal,
  data: invoiceData,
} = useToggleModal("invoice");
</script>

<template>
  <PaymentFormModal
    v-if="paymentModalState"
    v-bind="paymentModalState?.data"
    v-model="isOpen"
    @saved="onPaymentSaved"
  />

  <TransactionModal
    v-model:show="isTransactionModalOpen"
    v-bind="transactionModalState"
    @saved="onTransactionSaved"
    @close="onTransactionSaved"
  />

  <ClientFormModal
    v-model:show="isContactModalOpen"
    v-bind="contactData"
    @saved="onContactSaved"
  />

  <InvoiceFormModal
    title="Registar gasto de propiedad"
    v-if="isInvoiceModalOpen"
    v-model="isInvoiceModalOpen"
    v-bind="invoiceData"
    @saved="router.reload()"
  />
</template>
