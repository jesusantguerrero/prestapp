<script lang="ts" setup>
import { IClient } from "@/Modules/clients/clientEntity";
import { IInvoice } from "@/Modules/invoicing/entities";
import { computed, inject, ref } from "vue";
import Simple from "./Simple.vue";
import SimpleMultiTheme from "./SimpleMultiTheme.vue";
import { getInvoiceLayout } from "./index";

defineEmits(["signature-clicked"]);
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
const BaseInvoice = computed(() => {
  const templates = {
    renting: Simple,
    "shein-store": SimpleMultiTheme,
  };

  return templates[appProfileName.value] ?? "renting";
});

const layoutTheme = computed(() => {
  return getInvoiceLayout("store");
});
</script>

<template>
  <BaseInvoice
    v-bind="$props"
    :layout-theme="layoutTheme"
    @signature-clicked="$emit('signature-clicked')"
  />
</template>
