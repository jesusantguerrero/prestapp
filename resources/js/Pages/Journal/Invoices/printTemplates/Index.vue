<script lang="ts" setup>
import { IClient } from "@/Modules/clients/clientEntity";
import { IInvoice } from "@/Modules/invoicing/entities";
import { computed, inject, ref } from "vue";
import Simple from "./Simple.vue";
import SheinStore from "./SheinStore.vue";

withDefaults(
  defineProps<{
    imageUrl: string;
    type: string;
    user: Record<string, string>;
    businessData: Record<string, string>;
    products?: Record<string, string>[];
    clients?: IClient[];
    invoiceData: IInvoice;
  }>(),
  {
    type: "INVOICE",
    imageUrl: "/logo.png",
  }
);

const appProfileName = inject("appProfileName", ref());
const InvoiceTemplate = computed(() => {
  const templates = {
    renting: Simple,
    "shein-store": SheinStore,
  };

  return templates[appProfileName.value] ?? "renting";
});
</script>

<template>
  <InvoiceTemplate v-bind="$props" />
</template>
