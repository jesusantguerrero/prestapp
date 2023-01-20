<script setup lang="ts">
import { ILoanWithPayments } from "@/Modules/loans/loanEntity";
import LoanTemplate from "./Partials/LoanTemplate.vue";
import { formatMoney } from "@/utils";

export interface Props {
  loans: ILoanWithPayments;
  currentTab: string;
  stats: Object;
}

defineProps<Props>();
</script>

<template>
  <LoanTemplate :loans="loans" :current-tab="currentTab" :stats="stats">
    <section class="mt-12 px-4 overflow-hidden bg-white rounded-md shadow-md">
      <div v-for="payment in loans.payment_documents" class="text-sm py-4">
        Pagado
        <span class="font-bold text-green-500">
          {{ formatMoney(payment.amount) }}
        </span>
        en
        <span class="font-bold text-primary">
          {{ payment.payment_date }}
        </span>
        <a
          :href="`/loans/${loans.id}/payments/${payment.id}/print`"
          target="_blank"
          rel="noopener noreferrer"
        >
          Recibo
        </a>
      </div>
    </section>
  </LoanTemplate>
</template>
