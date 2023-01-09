<script setup lang="ts">
import { AtButton, AtTable } from "atmosphere-ui";
import InvoicePaymentOptions from "@/Components/templates/InvoicePaymentOptions.vue";

import cols from "./cols";
import { formatDate, formatMoney } from "@/utils";
import { Link } from "@inertiajs/vue3";
import { getStatus, getStatusColor, getStatusIcon } from "@/Modules/invoicing/constants";

defineProps({
  invoiceData: {
    type: Array,
  },
});
</script>

<template>
  <AtTable
    :cols="cols"
    :tableData="invoiceData"
    :show-prepend="true"
    class="mt-10 bg-base-lvl-3"
  >
    <template v-slot:date="{ scope: { row } }">
      <div>
        <div class="font-bold text-blue-400">{{ formatDate(row.date) }}</div>
      </div>
    </template>

    <template v-slot:concept="{ scope: { row } }">
      <Link
        :href="`/${row.type == 'INVOICE' ? 'invoices' : 'bills'}/${row.id}`"
        class="text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-sm"
      >
        {{ row.concept }}
        <span class="font-bold text-gray-300"> {{ row.series }} #{{ row.number }} </span>
      </Link>
      <Link
        class="text-sm text-body-1 mt-2"
        :href="`/clients/${row.client_id || row.contact_id}`"
      >
        <i class="fa fa-user text-xs" />
        {{ row.client_name }}
      </Link>
    </template>

    <template v-slot:status="{ scope: { row } }">
      <div class="font-bold capitalize text-sm" :class="getStatusColor(row.status)">
        <i :class="getStatusIcon(row.status)" />
        {{ getStatus(row.status) }}
      </div>
    </template>

    <template v-slot:category="{ scope: { row } }">
      <div class="font-bold capitalize text-primary">
        {{ row.category }}
      </div>
      <p class="text-sm">
        {{ row.account_name }}
      </p>
    </template>

    <template v-slot:total="{ scope: { row } }">
      <div class="font-bold">
        {{ formatMoney(row.total) }}
        <p class="font-bold" :class="[row.debt > 0 ? 'text-red-500' : 'text-green-500']">
          {{ formatMoney(row.debt) }}
        </p>
      </div>
    </template>

    <template v-slot:actions="{ scope: { row } }">
      <div class="flex items-center space-x-2">
        <AtButton
          @click="$inertia.visit(`${state.sectionName}/${row.id}/edit`)"
          class="w-8 h-8 text-gray-400 rounded-full hover:text-green-400"
        >
          <i class="fa fa-edit"></i>
        </AtButton>
        <AtButton class="w-8 h-8 text-gray-400 rounded-full hover:text-red-400">
          <i class="fa fa-trash"></i>
        </AtButton>
        <InvoicePaymentOptions :invoice="row" />
      </div>
    </template>
  </AtTable>
</template>
