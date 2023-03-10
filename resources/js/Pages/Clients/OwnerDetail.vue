<script setup lang="ts">
import { IClientSaved } from "@/Modules/clients/clientEntity";
import ClientTemplate from "./Partials/ClientTemplate.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import { ClientProfile } from "./Partials/ClientProfile";
import ClientForm from "./Partials/ClientForm.vue";

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
    }"
  >
    <article
      v-if="currentTab == 'transactions'"
      class="px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"
    >
      <InvoiceCard v-for="invoice in props.clients.invoices" :invoice="invoice" />
    </article>

    <article v-else class="shadow-md rounded-md overflow-hidden">
      <ClientForm :form-data="clients" :disabled="true" type="owner" />
    </article>

    <template #options>
      <slot name="options">
        <section class="space-y-4 flex flex-col">
          <EmptyAddTool>
            <ClientProfile
              v-for="lease in leases"
              :name="lease.client_name"
              type="owner"
              :id="lease.client_id"
            />
          </EmptyAddTool>
          <UnitTitle
            tenant-name=" "
            v-for="lease in leases"
            class="mt-4 hover:bg-white cursor-pointer px-4 py-2 bg-white rounded-md flex-col"
            :title="lease.address"
            :owner-name="lease.client_name"
          />
        </section>
      </slot>
    </template>
  </ClientTemplate>
</template>
