<script setup lang="ts">
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";
import RentTemplate from "../Partials/RentTemplate.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";

import { IRent } from "@/Modules/properties/propertyEntity";

interface Props {
  rents: IRent;
  currentTab: string;
}

defineProps<Props>();
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab" :hide-panel="true">
    <WelcomeWidget message="Facturas de depositos">
      <template #content>
        <InvoiceCard
          v-for="invoice in rents.deposits"
          :key="invoice.id"
          :invoice="invoice"
          type="invoices"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
