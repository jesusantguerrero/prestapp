<script setup lang="ts">
import SectionNav from "@/Components/SectionNav.vue";
import ContractCardMini from "@/Components/templates/ContractCardMini.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import NextPaymentsWidget from "@/Pages/Loans/NextPaymentsWidget.vue";
import { formatDate } from "@/utils";
import { endOfMonth, startOfMonth } from "date-fns";
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const selectedTab = ref("overdue");
const tabs = {
  overdue: {
    label: t("Expired rents"),
  },
  month: {
    label: t("Expiring this month"),
  },
  next: {
    label: t("Expire within 3 months"),
  },
};

const monthEndpoint = computed(() => {
  return `/api/invoices?filter[due_date]=${formatDate(
    startOfMonth(new Date()),
    "yyyy-MM-dd"
  )}~${formatDate(endOfMonth(new Date()), "yyyy-MM-dd")}&sort=due_date`;
});

const expiredEndpoint = computed(() => {
  return `/api/rents?filter[end_date]=<${formatDate(
    startOfMonth(new Date()),
    "yyyy-MM-dd"
  )}&sort=end_date`;
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
      :endpoint="expiredEndpoint"
      method="back"
      default-range="All"
      date-field="payment_date"
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
      <template v-slot:content="{ list: contracts }">
        <article class="py-4 space-y-4 my-2 h-[380px] overflow-auto ic-scroller">
          <template v-if="contracts.length">
            <ContractCardMini
              class="border-b mb-2 py-4"
              v-for="contract in contracts"
              :contract="contract"
            />
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
