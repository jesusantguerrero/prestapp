<script setup lang="ts">
import { IClientSaved } from "@/Modules/clients/clientEntity";
import ClientTemplate from "./Partials/ClientTemplate.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import { formatDate, formatMoney } from "@/utils";
import { ClientProfile } from "./Partials/ClientProfile";
import ClientForm from "./Partials/ClientForm.vue";

export interface Props {
  clients: IClientSaved;
  currentTab: string;
  outstanding: number;
  deposits: number;
  daysLate: number;
  type: string;
  stats: Record<string, string>;
  contract: Record<string, any>;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});
</script>

<template>
  <ClientTemplate
    :clients="clients"
    :type="type"
    :current-tab="currentTab"
    :contract="contract"
    :stats="stats"
    :tabs="{
      '': 'Detalles',
      transactions: 'Transacciones',
    }"
  >
    <article
      v-if="currentTab == 'transactions'"
      class="px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"
    >
      <InvoiceCard v-for="invoice in props.clients.invoices" :invoice="invoice" />
    </article>

    <article v-else>
      <ClientForm :form-data="clients" :disabled="true" type="tenant" />
    </article>

    <template #options v-if="contract">
      <slot name="options">
        <section class="space-y-4 flex flex-col">
          <EmptyAddTool>
            <ClientProfile
              :name="contract.owner_name"
              type="owner"
              :id="contract.owner_id"
            />
          </EmptyAddTool>
          <UnitTitle
            class="mt-4 hover:bg-white cursor-pointer px-4 py-2 bg-white rounded-md flex-col"
            :title="contract.address"
            :owner-name="formatDate(contract.date)"
            :tenant-name="formatMoney(contract.amount)"
          />
        </section>
      </slot>
    </template>
  </ClientTemplate>
</template>
