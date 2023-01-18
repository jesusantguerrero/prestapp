<script setup lang="ts">
import { computed } from "vue";

import { ILoanWithInstallments } from "@/Modules/loans/loanEntity";
import LoanTemplate from "./Partials/LoanTemplate.vue";

export interface Props {
  loans: ILoanWithInstallments;
  currentTab: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const clientName = computed(() => props.loans.client?.fullName);
</script>

<template>
  <LoanTemplate :loans="loans" :current-tab="currentTab">
    <sections>
      <span> Cliente: {{ clientName }} </span>
      <p>
        Monto Prestado:
        {{ loans.amount }}
      </p>
      <p>
        Fecha Primer Pago:
        {{ loans.first_installment_date }}
      </p>
      <p>
        Estatus:
        {{ loans.payment_status }}
      </p>
    </sections>
  </LoanTemplate>
</template>
