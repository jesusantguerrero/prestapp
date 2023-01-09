<script setup>
import { AtBackgroundIconCard, AtButton, AtTable } from "atmosphere-ui";
import { router } from "@inertiajs/core";

import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import BudgetProgress from "@/Components/BudgetProgress.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import IncomeSummaryWidget from "../Dashboard/Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "../Dashboard/Partials/WelcomeWidget.vue";

import cols from "./cols";
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
  propertiesTotal: {
    type: Number,
  },
  propertiesAvailable: {
    type: Number,
  },
  propertiesRented: {
    type: Number,
  },
  cashOnHand: {
    type: Object,
  },
  nextInvoices: {
    type: Array,
  },
});

const propertyStats = [
  {
    label: "Total propiedades",
    value: props.propertiesTotal || 0,
    icon: "fa-users",
  },
  {
    label: "Alquiladas/Libres",
    icon: "fa-money",
    value: `${props.propertiesRented || 0} / ${props.propertiesAvailable || 0}`,
  },
  {
    label: "Comision esperada/mes",
    icon: "fa-wallet",
    value: formatMoney(props.expectedCommission),
  },
  {
    label: "Comisiones pagadas/mes",
    value: formatMoney(props.paidCommission),
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
      data: props.revenue.previousYear.values.map((item) => item.total),
    },
    {
      name: "current year",
      data: props.revenue.currentYear.values.map((item) => item.total),
    },
  ],
};
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton variant="inverse" @click="router.visit(route('properties.create'))">
            Agregar Propiedad
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <header class="flex space-x-4">
        <WelcomeWidget
          message="Estadisticas de propiedades"
          class="text-body-1 w-8/12"
          :cards="propertyStats"
        />

        <WelcomeWidget
          message="Rent Payments"
          class="text-body-1 w-4/12"
          :cards="propertyStats"
          action-label="Ver estado de rentas"
          action-link="/rents"
        >
          <template #content>
            <section class="py-4">
              <BudgetProgress
                :goal="13_540"
                :current="12_140"
                class="h-2.5 text-white rounded-md"
                :progress-class="['bg-primary', 'bg-primary/5']"
                :show-labels="false"
              >
                <template v-slot:before="{ progress }">
                  <header class="mb-1 font-bold flex justify-between">
                    <span> {{ formatMoney(12_140) }} of {{ formatMoney(13_540) }} </span>
                    <span class="text-primary">{{ progress }}% </span>
                  </header>
                </template>

                <template v-slot:after="{ progress }">
                  <div class="justify-between w-full flex mt-1">
                    <p>
                      <span class="text-error font-bold">
                        {{ 1 }}
                      </span>
                      pagos de renta atrasados
                    </p>
                  </div>
                </template>
              </BudgetProgress>
            </section>
          </template>
        </WelcomeWidget>
      </header>

      <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
        <article class="rounded-md bg-base-lvl-3 lg:w-7/12">
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

        <article class="order-1 space-y-5 lg:w-5/12 lg:order-2">
          <AtBackgroundIconCard
            class="text-primary bg-base-lvl-3 h-36"
            icon="fas fa-wallet"
            :value="formatMoney(props.cashOnHand.balance | 0)"
            title="Balance en cuenta de renta"
          >
            <template #action>
              <AtButton
                class="rounded-md bg-base-lvl-3"
                @click="isTransferModalOpen = true"
              >
                Add Transaction
              </AtButton>
            </template>
          </AtBackgroundIconCard>

          <IncomeSummaryWidget
            class="order-2 mt-4 lg:w-full lg:mt-0 lg:order-1"
            :chart="comparisonRevenue"
            :headerInfo="comparisonRevenue.headers"
          />
        </article>
      </section>
    </main>
  </AppLayout>
</template>
