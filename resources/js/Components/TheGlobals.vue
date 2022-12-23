<template>
  <PaymentFormModal
    v-if="paymentModalState"
    v-bind="paymentModalState?.data"
    v-model="isOpen"
    @saved="router.reload()"
  />

  <DialogModal :show="confirmingPassword" @close="closeModal">
    <template #title>
        {{ title }}
    </template>

    <template #content>
        {{ content }}
    </template>

    <template #footer>
        <AppButton variant="secondary" @click="closeModal">
            Cancel
        </AppButton>

        <AppButton
            class="ml-3"
            :class="{ 'opacity-25': form.processing }"
            :disabled="form.processing"
            @click="confirmPassword"
        >
            {{ button }}
        </AppButton>
    </template>
</DialogModal>

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
