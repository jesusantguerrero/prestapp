<script setup>
import { formatDate, formatMoney } from "@/utils";
import { ElDropdown, ElIcon } from "element-plus";
import InvoicePaymentOptions from "./InvoicePaymentOptions.vue";

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
      <p class="flex text-green-500">
        {{ formatMoney(invoice.total) }}
        <ElDropdown v-if="actions" @command="$emit('action', $event)">
          <button class="px-5 rounded-md hover:bg-base-lvl-2">
            <i class="fa fa-ellipsis-h" />
          </button>
          <template #dropdown>
            <ElDropdownMenu>
              <ElDropdownItem
                :command="actionName"
                v-for="(action, actionName) in actions"
              >
                {{ action.label }}
              </ElDropdownItem>
            </ElDropdownMenu>
          </template>
        </ElDropdown>
      </p>
      <span>
        {{ invoice.status }}
      </span>
      <InvoicePaymentOptions :invoice="invoice" />
    </section>
  </article>
</template>
