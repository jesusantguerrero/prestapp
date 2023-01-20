<script setup lang="ts">
import { ILoanWithAgreements } from "@/Modules/loans/loanEntity";
import LoanTemplate from "./Partials/LoanTemplate.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";

export interface Props {
  loans: ILoanWithAgreements;
  currentTab: string;
  stats: Object;
}

defineProps<Props>();
</script>

<template>
  <LoanTemplate :loans="loans" :current-tab="currentTab" :stats="stats">
    <section class="mt-12 px-4 overflow-hidden bg-white rounded-md shadow-md">
      <InvoiceTable :invoice-data="loans.agreements" accounts-endpoint="/loan-accounts" />
    </section>
  </LoanTemplate>
</template>
