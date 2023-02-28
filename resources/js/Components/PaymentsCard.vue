<script setup lang="ts">
import { formatDate, formatMoney } from "@/utils";

export interface Props {
  payment: Record<string, any>;
}

defineProps<Props>();
</script>

<template>
  <article
    class="text-sm flex flex-col md:flex-row text-body justify-between py-4 shadow-sm px-4 rounded-md border mb-2"
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
      <p
        class="font-bold text-xl text-success flex mt-4 md:mt-0 justify-between space-x-3 items-center"
      >
        <span>
          {{ formatMoney(payment.amount) }}
        </span>
        <a
          :href="`/loans/${payment.resource?.id ?? payment.payable.loan_id}/payments/${
            payment.id
          }/print`"
          target="_blank"
          rel="noopener noreferrer"
          class="text-secondary px-3 py-1 rounded-md border border-base-lvl-1 flex text-sm bg-base-lvl-2"
        >
          <IMdiReceipt />
          <p>Recibo</p>
        </a>
      </p>
      <span class="w-full text-center inline-block md:inline">{{
        payment.resource?.client_name ?? payment.payable.clientName
      }}</span>
    </section>
  </article>
</template>