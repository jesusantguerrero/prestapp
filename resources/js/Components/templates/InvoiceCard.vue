<script lang="ts" setup>
import { formatDate, formatMoney } from "@/utils";

import InvoicePaymentOptions from "./InvoicePaymentOptions.vue";

import { getStatus, getStatusIcon } from "@/Modules/invoicing/constants";
import { IInvoice } from "@/Modules/invoicing/entities";

withDefaults(
  defineProps<{
    invoice: IInvoice;
    actions?: Record<string, any>;
    allowEdit: boolean;
    hideActions: boolean;
    externalActions?: Record<string, any>;
  }>(),
  {
    allowEdit: false,
  }
);
</script>

<template>
  <article class="flex justify-between text-sm text-body-1">
    <header>
      <h4 class="flex flex-col font-bold">
        {{ invoice.concept }}
        <span class="font-bold text-primary">
          {{ formatDate(invoice.due_date) }}
        </span>
      </h4>
      <p class="text-body-1/80">{{ invoice.description }}</p>
    </header>
    <section class="font-bold text-right">
      <p class="flex space-x-2 justify-end items-center">
        <slot name="header-actions" />
        <span class="text-success">
          {{ formatMoney(invoice.total) }}
        </span>
      </p>
      <div class="flex items-center">
        <span class="w-32">
          <i :class="getStatusIcon(invoice.status)" /> {{ getStatus(invoice.status) }}
        </span>
        <InvoicePaymentOptions
          v-if="!hideActions"
          :invoice="invoice"
          :allow-edit="allowEdit"
          @edit="$emit('edit')"
          :external-actions="externalActions"
        />
      </div>
    </section>
  </article>
</template>
