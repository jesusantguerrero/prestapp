<script setup lang="ts">
import SectionNav from "@/Components/SectionNav.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import NextPaymentsWidget from "@/Pages/Loans/NextPaymentsWidget.vue";
import { formatDate } from "@/utils";
import { endOfMonth, startOfMonth } from "date-fns";
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const selectedTab = ref("month");
const tabs = {
  month: {
    label: t("This month invoices"),
  },
  next: {
    label: t("Next invoices"),
  },
  overdue: {
    label: t("Overdue invoices"),
  },
};

const monthEndpoint = computed(() => {
  return `/api/invoices?filter[due_date]=${formatDate(
    startOfMonth(new Date()),
    "yyyy-MM-dd"
  )}~${formatDate(endOfMonth(new Date()), "yyyy-MM-dd")}&sort=due_date`;
});
</script>

<template>
  <section class="rounded-md overflow-hidden">
    <NextPaymentsWidget
      v-if="selectedTab == 'month'"
      :endpoint="monthEndpoint"
      :ranges="[]"
    >
      <template #title>
        <SectionNav
          class="bg-base-lvl-3 w-full"
          selected-class="border-primary font-bold text-primary"
          v-model="selectedTab"
          :sections="tabs"
        />
      </template>
      <template v-slot:content="{ list: nextInvoices }">
        <article class="py-4 space-y-4 my-2 h-[380px] overflow-auto ic-scroller">
          <template v-if="nextInvoices.length">
            <InvoiceCard v-for="invoice in nextInvoices" :invoice="invoice" />
          </template>
          <section
            v-else
            class="flex text-body-1 flex-col justify-center items-center w-full"
          >
            <IMdiNoteOff class="text-8xl" />
            <p class="mt-8">{{ $t("There's no invoices for this month") }}</p>
          </section>
        </article>
      </template>
    </NextPaymentsWidget>
    <NextPaymentsWidget
      v-else-if="selectedTab == 'next'"
      endpoint="/api/invoices?filter[status]=~paid&"
    >
      <template #title>
        <SectionNav
          class="bg-base-lvl-3 w-full"
          selected-class="border-primary font-bold text-primary"
          v-model="selectedTab"
          :sections="tabs"
        />
      </template>
      <template v-slot:content="{ list: nextInvoices }">
        <article class="py-4 space-y-4 my-2 h-[380px] overflow-auto ic-scroller">
          <template v-if="nextInvoices.length">
            <InvoiceCard v-for="invoice in nextInvoices" :invoice="invoice" />
          </template>
          <section
            v-else
            class="flex text-body-1 flex-col justify-center items-center w-full"
          >
            <IMdiNoteOff class="text-8xl" />
            <p class="mt-8">No hay pagos realizados en este rango de fechas</p>
          </section>
        </article>
      </template>
    </NextPaymentsWidget>
    <NextPaymentsWidget
      v-else
      title="Cuotas atrasadas"
      endpoint="/api/invoices?filter[status]=overdue&sort=-due_date"
      method="back"
      default-range="All"
      date-field="payment_date"
      :ranges="[
        { label: 'All', value: null },
        { label: '90D', value: [90, 0] },
        { label: '30D', value: [30, 0] },
        { label: '7D', value: [7, 0] },
        { label: '1D', value: [1, 1] },
      ]"
    >
      <template #title>
        <SectionNav
          class="bg-base-lvl-3 w-full"
          selected-class="border-primary font-bold text-primary"
          v-model="selectedTab"
          :sections="tabs"
        />
      </template>
      <template v-slot:content="{ list: nextInvoices }">
        <article class="py-4 space-y-4 my-2 h-[380px] overflow-auto ic-scroller">
          <template v-if="nextInvoices.length">
            <InvoiceCard v-for="invoice in nextInvoices" :invoice="invoice" />
          </template>
          <section
            v-else
            class="flex text-body-1 flex-col justify-center items-center w-full"
          >
            <IMdiNoteOff class="text-8xl" />
            <p class="mt-8">No hay pagos realizados en este rango de fechas</p>
          </section>
        </article>
      </template>
    </NextPaymentsWidget>
  </section>
</template>
