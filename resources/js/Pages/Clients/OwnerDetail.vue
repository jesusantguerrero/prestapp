<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import ClientTemplate from "./Partials/ClientTemplate.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import ClientForm from "./Partials/ClientForm.vue";

import { formatMoney } from "@/utils";
import { IClientSaved } from "@/Modules/clients/clientEntity";

export interface Props {
  clients: IClientSaved;
  currentTab: string;
  outstanding: number;
  deposits: number;
  daysLate: number;
  type: string;
  leases: Record<string, any>[];
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
    :tabs="{
      '': 'Detalles',
      transactions: 'Transacciones',
      properties: 'Propiedades',
    }"
  >
    <article
      v-if="currentTab == 'transactions'"
      class="px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"
    >
      <InvoiceCard v-for="invoice in props.clients.invoices" :invoice="invoice" />
    </article>

    <article
      v-else-if="currentTab == 'properties'"
      class="px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"
    >
      <UnitTitle
        class="px-4 py-2 mt-4 bg-white rounded-md cursor-pointer hover:bg-white"
        :title="property.name"
        :owner-name="property.address"
        :owner-link="''"
        :tenant-name="''"
        v-for="property in props.clients.properties"
        @click="router.visit(`/properties/${property.id}`)"
      />
    </article>

    <article v-else class="overflow-hidden rounded-md shadow-md">
      <ClientForm :form-data="clients" :disabled="true" type="owner" />
    </article>

    <template #options>
      <slot name="options">
        <section class="flex flex-col space-y-4">
          <UnitTitle
            v-for="lease in leases"
            class="flex-col px-4 py-2 mt-4 bg-white rounded-md cursor-pointer hover:bg-white"
            :title="lease.address"
            :owner-name="lease.client_name"
            :tenant-name="formatMoney(lease.amount)"
            @click="router.visit(`/rents/${lease.id}`)"
          />
        </section>
      </slot>
    </template>
  </ClientTemplate>
</template>
