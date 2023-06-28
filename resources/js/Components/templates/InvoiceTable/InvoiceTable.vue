<script setup lang="ts">
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
// @ts-ignore
import { AtInput } from "atmosphere-ui";

import BaseTable from "@/Components/shared/BaseTable.vue";

import { getStatus, getStatusColor, getStatusIcon } from "@/Modules/invoicing/constants";
import InvoicePaymentOptions from "@/Components/templates/InvoicePaymentOptions.vue";
import cols from "./cols";
import { formatDate, formatMoney } from "@/utils";
import InvoiceCard from "../InvoiceCard.vue";

const props = defineProps({
  invoiceData: {
    type: Array,
  },
  accountsEndpoint: {
    type: String,
  },
  isLoading: {
    type: Boolean,
  },
  responsiveActions: {
    type: Object,
  },
});

const visibleData = computed(() => {
  return props.isLoading ? [{}, {}, {}, {}] : props.invoiceData;
});
</script>

<template>
  <BaseTable
    :cols="cols"
    :tableData="visibleData"
    :show-prepend="true"
    responsive
    class="bg-base-lvl-3"
  >
    <template v-slot:card="{ row: invoice }">
      <slot name="card" row="row">
        <InvoiceCard
          :invoice="invoice"
          class="mb-6 border-b py-6"
          :external-actions="responsiveActions"
        />
      </slot>
    </template>

    <template v-slot:date="{ scope: { row } }">
      <div v-if="!isLoading">
        <div class="font-bold text-blue-400">{{ formatDate(row.date) }}</div>
      </div>
      <ElSkeleton :rows="1" animated v-else />
    </template>

    <template v-slot:concept="{ scope: { row } }">
      <slot name="concept" :row="row">
        <section v-if="!isLoading">
          <p>
            <Link
              :href="`/${row.type == 'INVOICE' ? 'invoices' : 'bills'}/${row.id}`"
              class="text-blue-400 inline-flex capitalize border-b justify-between border-blue-400 border-dashed cursor-pointer text-sm"
            >
              <section>
                {{ row.concept }}
                <span class="font-bold text-gray-300">
                  {{ row.series }} #{{ row.number }}
                </span>
              </section>
            </Link>
          </p>
          <p>
            <Link
              class="text-sm text-body-1 mt-2"
              :href="`/clients/${row.client_id || row.contact_id}`"
            >
              <i class="fa fa-user text-xs" />
              {{ row.client_name }}
            </Link>
          </p>
          <p class="flex items-center">
            <span v-if="!row.isEditing">
              {{ row.description }}
            </span>
            <AtInput v-model="row.description" v-else />
            <span class="ml-2"> </span>
          </p>
        </section>
        <ElSkeleton :rows="1" animated v-else />
      </slot>
    </template>

    <template v-slot:status="{ scope: { row } }">
      <div
        class="font-bold capitalize text-sm"
        :class="getStatusColor(row.status)"
        v-if="!isLoading"
      >
        <i :class="getStatusIcon(row.status)" />
        {{ getStatus(row.status) }}
      </div>
      <ElSkeleton :rows="1" animated v-else />
    </template>

    <template v-slot:category="{ scope: { row } }">
      <div class="font-bold capitalize text-primary" v-if="!isLoading">
        {{ row.category }}
      </div>
      <p class="text-sm" v-if="!isLoading">
        {{ row.account_name }}
      </p>
      <ElSkeleton :rows="1" animated v-else />
    </template>

    <template v-slot:total="{ scope: { row } }">
      <div class="font-bold" v-if="!isLoading">
        {{ formatMoney(row.total) }}
        <p class="font-bold" :class="[row.debt > 0 ? 'text-red-500' : 'text-green-500']">
          {{ formatMoney(row.debt) }}
        </p>
      </div>
      <ElSkeleton :rows="1" animated v-else />
    </template>

    <template v-slot:actions="{ scope: { row } }">
      <slot name="actions" :row="row" v-if="!isLoading">
        <div class="flex items-center justify-end space-x-2">
          <InvoicePaymentOptions :invoice="row" :accounts-endpoint="accountsEndpoint" />
        </div>
      </slot>
      <ElSkeleton :rows="1" animated v-else />
    </template>
  </BaseTable>
</template>
