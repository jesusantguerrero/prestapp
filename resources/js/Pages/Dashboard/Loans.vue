<script setup lang="ts">
import { AtBackgroundIconCard, AtButton } from "atmosphere-ui";

import IncomeSummaryWidget from "@/Pages/Dashboard/Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";
import NextPaymentsWidget from "@/Pages/Loans/NextPaymentsWidget.vue";
import DashboardTemplate from "./Partials/DashboardTemplate.vue";
import ChartBar from "./Partials/ChartBar.vue";

import { formatMoney } from "@/utils/formatMoney";
import PaymentsCard from "@/Components/PaymentsCard.vue";
import { ref } from "vue";
import { config } from "@/config";
import { formatDate } from "@/utils";
import RepaymentWidget from "./Partials/RepaymentWidget.vue";

const props = defineProps({
  revenue: {
    type: Object,
    default() {
      return {
        previousYear: {
          values: [],
        },
        currentYear: {
          values: [],
        },
      };
    },
  },
  user: {
    type: Object,
    required: true,
  },
  activeLoanClients: {
    type: Number,
  },
  loanCapital: {
    type: Number,
  },
  loanExpectedInterest: {
    type: Number,
  },
  loanPaidInterest: {
    type: Number,
  },
  paidInterest: {
    type: Array,
  },
  bank: {
    type: Number,
  },
});

const welcomeCards = [
  {
    label: "Clientes con Prestamos",
    value: props.activeLoanClients,
    icon: "fa-users",
  },
  {
    label: "Capital Prestado",
    icon: "fa-money",
    value: formatMoney(props.loanCapital ?? 0),
  },
  {
    label: "Interes Esperado",
    icon: "fa-wallet",
    value: formatMoney(props.loanExpectedInterest ?? 0),
  },
  {
    label: "Total Interes pagado",
    value: formatMoney(props.loanPaidInterest ?? 0),
    accent: true,
  },
];

const comparisonRevenue = {
  headers: {
    gapName: "Year",
    previous: props.revenue.previousYear.total,
    current: props.revenue.currentYear.total,
  },
  options: {
    chart: {
      id: "vuechart-example",
    },
    stroke: {
      curve: "smooth",
    },
    dropShadow: {
      enabled: true,
      top: 3,
      left: 0,
      blur: 1,
      opacity: 0.5,
    },
    colors: [config.colors.highlight, config.colors.info],
  },
  series: [
    {
      name: "Año anterior",
      data: props.revenue.previousYear.values.map((item) => item.total),
    },
    {
      name: "Este año",
      data: props.revenue.currentYear.values.map((item) => item.total),
    },
  ],
};

const interestPerformance = {
  headers: {
    gapName: "Year",
    month: props.paidInterest.months.at(-1).outcome,
    avg: props.paidInterest.avg,
    current: props.paidInterest?.year,
  },
  options: {
    chart: {
      id: "vuechart-example",
    },
    stroke: {
      curve: "smooth",
    },
    dropShadow: {
      enabled: true,
      top: 3,
      left: 0,
      blur: 1,
      opacity: 0.5,
    },
    colors: [config.colors.highlight, config.colors.info],
  },
  series: [
    {
      name: "Ganancias intereses",
      data: props.paidInterest.months.map((item) => item.outcome),
    },
  ],
};

const summaryType = ref("cash-flow");
</script>

<template>
  <DashboardTemplate :user="user">
    <WelcomeWidget
      message="Bienvenido a ICLoan"
      :username="user.name"
      :cards="welcomeCards"
    />
    <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
      <article class="lg:w-8/12">
        <WelcomeWidget
          message="Rendimiento del mes"
          class="order-2 mt-4 lg:mt-0 lg:order-1"
        >
          <template #actions>
            <section class="flex space-x-2">
              <button
                @click="summaryType = 'gains'"
                class="bg-base-lvl-2 rounded-3xl text-body-1 px-4"
              >
                Ganancias
              </button>
              <button
                @click="summaryType = 'cash-flow'"
                class="bg-base-lvl-2 rounded-3xl text-body-1 px-4"
              >
                Cash
              </button>
            </section>
          </template>
          <template #content>
            <IncomeSummaryWidget
              v-if="summaryType == 'cash-flow'"
              title="Flujo de efectivo"
              description="Flujo de efectivo en el año"
              :style="{ height: '300px' }"
              :chart="comparisonRevenue"
              :headerInfo="comparisonRevenue.headers"
            />
            <ChartBar
              v-else
              title="Ganancias"
              description="Ganancias por intereses en el año"
              :chart="interestPerformance"
              :headerInfo="interestPerformance.headers"
            />
          </template>
        </WelcomeWidget>

        <RepaymentWidget class="mt-4" />
      </article>

      <article class="order-1 space-y-5 lg:w-5/12 lg:order-2">
        <AtBackgroundIconCard
          class="text-white bg-secondary h-36"
          icon="fas fa-wallet"
          :value="formatMoney(props.bank.balance | 0)"
          title="Balance cuenta prestamos"
        >
          <template #action>
            <AtButton
              class="bg-secondary/60 rounded-md"
              @click="isTransferModalOpen = true"
            >
              Agregar fondos
            </AtButton>
          </template>
        </AtBackgroundIconCard>
        <NextPaymentsWidget
          title="Pagos por periodo"
          endpoint="/api/loan-payments?"
          method="back"
          default-range="7D"
          date-field="payment_date"
          class="rounded-md border"
          :ranges="[
            { label: '1D', value: [1, 1] },
            { label: '7D', value: [7, 0] },
            { label: '30D', value: [30, 0] },
            { label: '90D', value: [90, 0] },
          ]"
        >
          <template v-slot:content="{ list }">
            <div class="py-4 my-2 h-[380px] overflow-auto ic-scroller">
              <PaymentsCard v-for="payment in list" :payment="payment" />
            </div>
          </template>
        </NextPaymentsWidget>
      </article>
    </section>
  </DashboardTemplate>
</template>
