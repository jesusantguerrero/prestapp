<script setup lang="ts">
import { ILoanWithPayments } from "@/Modules/loans/loanEntity";
import LoanTemplate from "./Partials/LoanTemplate.vue";
import { formatDate, formatMoney } from "@/utils";

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
      <header class="mb-4">
        <h4 class="my-2 mb-0 font-bold text-secondary">Pagos de prestamo</h4>
        <small class="text-body-1"
          >Este prestamo ha recibido {{ loans.payment_documents.length }} pagos</small
        >
      </header>
      <article
        v-for="payment in loans.payment_documents"
        class="text-sm flex text-body justify-between py-4 shadow-sm px-4 rounded-md border mb-2"
      >
        <section class="flex space-x-3 items-center">
          <IMdiDocument class="text-xl" />
          <div>
            <h5>{{ payment.concept }}</h5>
            <small>
              Pagado en
              <span class="font-bold text-primary">
                {{ formatDate(payment.payment_date) }}
              </span>
            </small>
          </div>
        </section>
        <section>
          <p class="font-bold text-xl text-green-500 flex space-x-3 items-center">
            <span>
              {{ formatMoney(payment.amount) }}
            </span>
            <a
              :href="`/loans/${loans.id}/payments/${payment.id}/print`"
              target="_blank"
              rel="noopener noreferrer"
              class="text-secondary px-3 py-1 rounded-md border border-base-lvl-1 flex text-sm bg-base-lvl-2"
            >
              <IMdiReceipt />
              <p>Recibo</p>
            </a>
          </p>
        </section>
      </article>
    </section>
  </LoanTemplate>
</template>
