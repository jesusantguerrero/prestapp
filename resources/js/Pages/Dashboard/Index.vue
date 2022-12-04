<script setup>
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtBackgroundIconCard, AtButton, AtDashlide } from "atmosphere-ui";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "./Partials/WelcomeWidget.vue";
import { formatMoney } from "@/utils/formatMoney";

const props = defineProps({
    revenue: {
        type: Object,
        default() {
            return {
                previousYear: {
                    values: []
                },
                currentYear: {
                    values: []
                }
            }
        }
    },
    user: {
        type: Object,
        required: true,
    },
    bank: {
        type: Number
    },
    dailyBox: {
        type: Number
    },
    cashOnHand: {
        type: Number
    },
    nextInvoices: {
        type: Array,
    },
    debtors: {
        type: Array,
    },
});

const welcomeCards = [{
        label: 'Clientes con Prestamos',
        value: props.loanClients,
        icon: 'fa-users'
    }, {

        label: 'Capital Prestado',
        icon: 'fa-money',
        value: props.loanCapital
    }, {

        label: 'Interes Ganado',
        icon: 'fa-wallet',
        value: props.loanInterestProfit
    }, {
        label: 'Total Interes pagado',
        value: props.loanInterestProfit,
        accent: true,
    }
]

const slideOptions = [
  {
    name: "caja",
    title: "Caja Chica",
  },
  {
    name: "pagos",
    title: "Pagos",
  },
  {
    name: "ganancias",
    title: "Ingresos",
  },
  {
    name: "debtors",
    title: "Deudores",
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
        colors: ["#fa6b88", "#80CDFE"],
    },
    series: [
        {
            name: "previous year",
            data: props.revenue.previousYear.values.map(item => item.total),
        },
        {
            name: "current year",
            data: props.revenue.currentYear.values.map(item => item.total),
        },
    ],
};
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="p-5 mx-auto text-gray-500 sm:px-6 lg:px-8">
        <WelcomeWidget
            message="Bienvenido a PrestApp"
            :username="user.name"
            :cards="welcomeCards"
        />
      <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
        <IncomeSummaryWidget class="order-2 mt-4 lg:w-8/12 lg:mt-0 lg:order-1"
            :chart="comparisonRevenue"
            :headerInfo="comparisonRevenue.headers"
        />
        <article class="order-1 space-y-5 lg:w-5/12 lg:order-2">
          <AtBackgroundIconCard
            class="text-white bg-blue-400 h-36"
            icon="fas fa-wallet"
            :value="formatMoney(props.cashOnHand.balance | 0)"
            title="Cartera de Prestamos"
          >
            <template #action>
              <AtButton
                class="bg-blue-500 rounded-md"
                @click="isTransferModalOpen = true"
              >
                Add Transaction
              </AtButton>
            </template>
          </AtBackgroundIconCard>

          <AtDashlide class="h-full rounded-md" :slides="slideOptions">
            <template #caja>
              <AtBackgroundIconCard
                class="w-full h-full text-gray-400 rounded-t-none rounded-b-none"
                icon="fas fa-wallet"
                :value="formatMoney(props.dailyBox.balance)"
                title="Caja Chica"
              />
            </template>

            <template #ganancias>
              <AtBackgroundIconCard
                class="w-full h-full text-blue-400 rounded-t-none rounded-b-none"
                icon="fas fa-dollar-sign"
                value="5,000"
                title="Interes Ganado"
              />
            </template>

            <template #debtors>
                {{ debtors }}
            </template>

            <template #pagos>
                {{ nextInvoices }}
            </template>
          </AtDashlide>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
