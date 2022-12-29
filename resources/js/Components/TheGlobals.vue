<template>
  <PaymentFormModal
    v-if="paymentModalState"
    v-bind="paymentModalState?.data"
    v-model="isOpen"
    @saved="router.reload()"
  />
</template>

<script setup>
import { router } from "@inertiajs/vue3";
import {
  usePaymentModal,
  modalState as paymentModalState,
} from "@/Modules/transactions/usePaymentModal";
import PaymentFormModal from "../Pages/Loans/Partials/PaymentFormModal.vue";

const { isOpen } = usePaymentModal();
</script>
