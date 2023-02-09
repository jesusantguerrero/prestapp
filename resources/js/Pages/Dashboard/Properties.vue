<script lang="ts" setup>
import { router } from "@inertiajs/core";

import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import BudgetProgress from "@/Components/BudgetProgress.vue";
import IncomeSummaryWidget from "@/Pages/Dashboard/Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";
import DashboardTemplate from "./Partials/DashboardTemplate.vue";

import { formatMoney } from "@/utils/formatMoney";

const props = defineProps({
  user: {
    type: Object,
  },
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
  stats: {
    type: Object,
  },
  cashOnHand: {
    type: Object,
  },
  totals: {
    type: Object,
  },
  accounts: {
    type: Array,
  },
  nextInvoices: {
    type: Array,
  },
  paidCommissions: {
    type: Number,
  },
});

const propertyStats = [
  {
    label: "Total propiedades",
    value: props.stats?.total || 0,
    icon: "fa-users",
  },
  {
    label: "Alquiladas/Libres",
    icon: "fa-money",
    value: `${props.stats?.rented || 0} / ${props.stats?.available || 0}`,
  },
  {
    label: "Comisiones pagadas/mes",
    value: formatMoney(props.paidCommissions || 0),
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
    colors: ["#fa6b88", "#80CDFE"],
  },
  series: [
    {
      name: "previous year",
      data: props.revenue.previousYear.values.map(
        (item: Record<string, any>) => item.total
      ),
    },
    {
      name: "current year",
      data: props.revenue.currentYear.values.map(
        (item: Record<string, any>) => item.total
      ),
    },
  ],
};
</script>

<template>
  <DashboardTemplate :user="user">
    <header class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
      <WelcomeWidget
        message="Estadisticas de propiedades"
        class="text-body-1 w-full md:w-7/12 shadow-md"
        :cards="propertyStats"
      />

      <WelcomeWidget
        message="Pagos de renta"
        class="text-body-1 w-full md:w-5/12 shadow-md"
        :cards="propertyStats"
        action-label="Ver estado de rentas"
        action-link="/rents"
      >
        <template #content>
          <section class="py-4">
            <BudgetProgress
              :goal="totals?.total"
              :current="totals?.paid"
              class="h-2.5 text-white rounded-md"
              :progress-class="['bg-primary-shade-2', 'bg-primary-shade-1']"
              :show-labels="false"
            >
              <template v-slot:before="{ progress }">
                <header class="mb-1 font-bold flex justify-between">
                  <span>
                    {{ formatMoney(totals?.paid) }} of {{ formatMoney(totals?.total) }}
                  </span>
                  <span class="text-primary">{{ progress }}% </span>
                </header>
              </template>

              <template v-slot:after="{ progress }">
                <div class="justify-between w-full flex mt-1">
                  <p v-if="totals?.outstandingInvoices">
                    <span
                      class="font-bold"
                      :class="[
                        totals?.outstandingInvoices ? 'text-success' : 'text-error',
                      ]"
                    >
                      {{ totals?.outstandingInvoices }}
                    </span>
                    Pagos de renta atrasados
                  </p>
                </div>
              </template>
            </BudgetProgress>
          </section>
        </template>
      </WelcomeWidget>
    </header>

    <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
      <section class="lg:w-7/12 space-y-4">
        <IncomeSummaryWidget
          class="order-2 mt-4 lg:w-full lg:mt-0 lg:order-1"
          :style="{ height: '350px' }"
          :chart="comparisonRevenue"
          :headerInfo="comparisonRevenue.headers"
          :sections="accounts"
        />

        <article class="rounded-md bg-base-lvl-3 shadow-md">
          <header class="flex justify-between px-5 py-2 text-body-1">
            <h4 class="text-xl font-bold">Proximos pagos</h4>
            <AppButton
              variant="inverse"
              @click="router.visit(route('properties.create'))"
            >
              Agregar Contrato
            </AppButton>
          </header>
          <section class="px-5 space-y-4">
            <InvoiceCard v-for="invoice in nextInvoices" :invoice="invoice" />
          </section>
        </article>
      </section>

      <article class="order-1 space-y-5 lg:w-5/12 lg:order-2">
        <WelcomeWidget
          message="Distribucion a propierarios"
          class="text-body-1 w-full shadow-md"
          :cards="propertyStats"
        />

        <IncomeSummaryWidget
          class="order-2 mt-4 lg:w-full lg:mt-0 lg:order-1"
          :style="{ height: '230px' }"
          :chart="comparisonRevenue"
          :headerInfo="comparisonRevenue.headers"
        />

        <WelcomeWidget
          message="Unidades recientes"
          class="text-body-1 w-full shadow-md"
          :cards="propertyStats"
        >
          <template #content>
            <div class="rounded-md h-44 w-full bg-base-lvl-2 p-4 mb-4">
              <h4 class="font-bold">DOP 5000</h4>
              <p class="mt-4"><IconMarker /> <span>Address</span></p>
              <p class="space-x-4 mt-2">
                <span>3 Dormitorios</span><span>1 Baño</span><span>300 mts</span>
              </p>
            </div>
          </template>
        </WelcomeWidget>
      </article>
    </section>
  </DashboardTemplate>
</template>
