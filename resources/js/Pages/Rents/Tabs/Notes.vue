<script setup lang="ts">
import WelcomeWidget from "@/Components/WelcomeWidget.vue";
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
    <WelcomeWidget message="Notas de credito">
      <template #content>
        <InvoiceCard
          v-for="invoice in rents.invoiceNotes"
          :key="invoice.id"
          :invoice="invoice"
          type="invoices"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
