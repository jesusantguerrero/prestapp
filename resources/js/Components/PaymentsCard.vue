<script setup lang="ts">
import { formatDate, formatMoney } from "@/utils";
import { computed } from "vue";

export interface Props {
  payment: Record<string, any>;
  type?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "loans",
});

const printUrl = computed(() => {
  return props.type == "loans"
    ? `/loans/${props.payment.resource?.id ?? props.payment.payable.loan_id}/payments/${
        props.payment.id
      }/print`
    : `/invoices/${props.payment.payable.id}/payments/${props.payment.id}/print`;
});
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
          :href="printUrl"
          target="_blank"
          rel="noopener noreferrer"
          class="text-secondary px-3 py-1 rounded-md border border-base-lvl-1 flex text-sm bg-base-lvl-2"
        >
          <IMdiReceipt />
          <p>Recibo</p>
        </a>
      </p>
      <span class="w-full text-right inline-block md:inline">{{
        payment.resource?.client_name ??
        payment.payable.client_name ??
        payment.payable?.client?.fullName
      }}</span>
    </section>
  </article>
</template>
