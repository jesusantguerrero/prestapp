<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import WelcomeWidget from "@/Components/WelcomeWidget.vue";

import RentTemplate from "../Partials/RentTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";
import PaymentsCard from "@/Components/PaymentsCard.vue";
import { IPayment } from "@/Modules/loans/loanEntity";
import { ElMessageBox } from "element-plus";

interface Props {
  rents: IRent;
  currentTab: string;
}

const props = defineProps<Props>();

const deletePayment = async (payment: IPayment) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el pago ${payment.id} ${props.rents.client_name}?`,
    "Eliminar contrato"
  );
  if (isValid) {
    router.delete(
      `/rents/${props.rents.id}/invoices/${payment.payable_id}/payments/${payment.id}`,
      {
        onSuccess() {
          router.reload();
        },
      }
    );
  }
};
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab">
    <WelcomeWidget message="Pagos del contrato">
      <template #content>
        <PaymentsCard
          v-for="payment in rents.payments"
          :key="payment.id"
          :payment="payment"
          :allow-delete="true"
          @delete="deletePayment"
          type="invoices"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
