<script setup lang="ts">
import { computed } from "vue";
import { router } from "@inertiajs/vue3";

import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { IClientSaved } from "@/Modules/clients/clientEntity";
import ClientTemplate from "./Partials/ClientTemplate.vue";
import { clientInteractions } from "@/Modules/clients/clientInteractions";

export interface Props {
  clients: IClientSaved;
  currentTab: string;
  outstanding: number;
  deposits: number;
  daysLate: number;
  type: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});
</script>

<template>
  <ClientTemplate :clients="clients" :type="type" :current-tab="currentTab">
    <article
      v-if="currentTab == 'transactions'"
      class="px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"
    >
      <InvoiceCard v-for="invoice in props.clients.invoices" :invoice="invoice">
        <template #header-actions>
          <button
            v-if="invoice.status !== 'paid'"
            class="mr-2"
            @click="clientInteractions.generateOwnerDistribution(clients.id, invoice.id)"
          >
            Re-generar
          </button>
        </template>
      </InvoiceCard>
    </article>
  </ClientTemplate>
</template>
