<script setup>
import { formatDate, formatMoney } from "@/utils";
import { ElDropdown, ElIcon } from "element-plus";

import InvoicePaymentOptions from "./InvoicePaymentOptions.vue";

import { getStatus, getStatusIcon } from "@/Modules/invoicing/constants";

defineProps({
  invoice: {
    type: Object,
    required: true,
  },
  actions: {
    type: Object,
  },
});
</script>

<template>
  <article class="flex justify-between text-sm text-body-1">
    <header>
      <h4 class="flex flex-col font-bold">
        {{ invoice.concept }} de {{ invoice.contact }}
        <span class="font-bold text-primary">
          {{ formatDate(invoice.due_date) }}
        </span>
      </h4>
      <p class="text-body-1/80">{{ invoice.description }}</p>
    </header>
    <section class="font-bold text-right">
      <p class="flex">
        <slot name="header-actions" />
        <span class="text-green-500">
          {{ formatMoney(invoice.total) }}
        </span>
      </p>
      <span>
        <i :class="getStatusIcon(invoice.status)" />
        {{ getStatus(invoice.status) }}
      </span>
      <InvoicePaymentOptions :invoice="invoice" />
    </section>
  </article>
</template>
