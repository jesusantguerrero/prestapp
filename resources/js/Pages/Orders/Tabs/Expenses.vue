<script setup lang="ts">
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";
import RentTemplate from "../Partials/RentTemplate.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";

import { IRent } from "@/Modules/properties/propertyEntity";
import EmptyResults from "@/Components/EmptyResults.vue";

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
        <section class="">
          <EmptyResults
            v-if="!rents.rent_expenses?.length"
            message="No hay gastos realizados para esta propiedad"
          />
          <template v-else>
            <InvoiceCard
              v-for="invoice in rents.rent_expenses"
              :key="invoice.id"
              :invoice="invoice"
              type="invoices"
            />
          </template>
        </section>
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
