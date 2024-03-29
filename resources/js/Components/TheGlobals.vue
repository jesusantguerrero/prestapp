<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import PaymentFormModal from "@/Pages/Loans/Partials/PaymentFormModal.vue";
import TransactionModal from "@/Components/shared/TransactionModal.vue";
import PropertyChargeModal from "@/Pages/Journal/Invoices/Partials/PropertyChargeModal.vue";
import InvoiceModal from "@/Pages/Journal/Invoices/Partials/InvoiceModal.vue";

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
import { useResponsive } from "@/utils/useResponsive";
import { useActionSheet } from "@/Modules/_app/useActionSheet";
import { computed } from "vue";
import MoreOptionsModal from "./MoreOptionsModal.vue";

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
  isOpen: isChargeModalOpen,
  closeModal: closeChargeModal,
  data: chargeData,
} = useToggleModal("propertyCharge");

const {
  isOpen: isInvoiceModalOpen,
  closeModal: closeInvoiceModal,
  data: invoiceData,
} = useToggleModal("invoice");

const { isMobile } = useResponsive();
const { isOpen: isActionSheetOpen, closeAction, data: actionData } = useActionSheet(
  "global"
);

const modalMaxWidth = computed(() => {
  return isMobile.value ? "mobile" : null;
});
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

  <MoreOptionsModal
    v-model:show="isActionSheetOpen"
    @close="closeAction()"
    max-width="mobile"
    v-bind="actionData"
    v-if="isMobile"
  />

  <ClientFormModal
    v-model:show="isContactModalOpen"
    v-bind="contactData"
    :full-height="isMobile"
    :max-width="isMobile ? 'mobile' : null"
    @saved="onContactSaved"
  />

  <PropertyChargeModal
    title="Registrar gasto de propiedad"
    v-if="isChargeModalOpen"
    v-model="isChargeModalOpen"
    v-bind="chargeData"
    @saved="router.reload()"
  />

  <InvoiceModal
    :title="$t('Create invoice')"
    v-if="isInvoiceModalOpen"
    v-model="isInvoiceModalOpen"
    v-bind="invoiceData"
    @saved="router.reload()"
  />
</template>
