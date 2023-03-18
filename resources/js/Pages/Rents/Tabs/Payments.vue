<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import AppButton from "@/Components/shared/AppButton.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";

import { formatMoney, formatDate } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import RentTemplate from "../Partials/RentTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";
import PaymentsCard from "@/Components/PaymentsCard.vue";

interface Props {
  rents: IRent;
  currentTab: string;
}

defineProps<Props>();
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab">
    <WelcomeWidget message="Pagos del contrato">
      <template #content>
        <PaymentsCard
          v-for="payment in rents.payments"
          :key="payment.id"
          :payment="payment"
          type="invoices"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
