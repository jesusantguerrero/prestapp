<script setup>
import AppLayout from "../../Components/templates/AppLayout.vue";
import WelcomeWidget from "../Dashboard/Partials/WelcomeWidget.vue";
import { formatMoney } from "@/utils/formatMoney";
import IncomeSummaryWidget from "../Dashboard/Partials/IncomeSummaryWidget.vue";
import { AtBackgroundIconCard, AtButton, AtTable } from "atmosphere-ui";
import cols from "./cols";
import AppButton from "../../Components/shared/AppButton.vue";
import { router } from "@inertiajs/core";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";

const props = defineProps({
  user: {
    type: Object,
  },
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
  propertiesTotal: {
    type: Number
  },
  propertiesAvailable: {
    type: Number
  },
  propertiesRented: {
    type: Number
  },
  cashOnHand: {
    type: Object
  }
})

const propertyStats = [{
        label: 'Total propiedades',
        value: props.propertiesTotal || 0,
        icon: 'fa-users'
    }, {

        label: 'Alquiladas/Libres',
        icon: 'fa-money',
        value: `${props.propertiesRented || 0} / ${props.propertiesAvailable || 0}`
    }, {

        label: 'Comision esperada/mes',
        icon: 'fa-wallet',
        value: formatMoney(props.expectedCommission)
    }, {
        label: 'Comisiones pagadas/mes',
        value: formatMoney(props.paidCommission),
        accent: true,
    }
]

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
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav> 
          <template #actions>
            <AppButton variant="inverse" @click="router.visit(route('properties.create'))">Agregar Propiedad</AppButton>
          </template>
        </PropertySectionNav>
    </template>
    
    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <WelcomeWidget
          message="Estadisticas de propiedades"
          class="text-body-1"
          :cards="propertyStats"
      />

      <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
        <article class="rounded-md bg-base-lvl-3 lg:w-7/12">
          <header class="flex justify-between px-5 py-2 text-body-1">
            <h4 class="text-xl font-bold">Proximos pagos</h4>
            <AppButton variant="inverse" @click="router.visit(route('properties.create'))">Agregar Contrato</AppButton>
          </header>
          <AtTable :cols="cols" :table-data="[]" />
        </article>

        
        <article class="order-1 space-y-5 lg:w-5/12 lg:order-2">
          <AtBackgroundIconCard
            class="text-primary bg-base-lvl-3 h-36"
            icon="fas fa-wallet"
            :value="formatMoney(props.cashOnHand.balance | 0)"
            title="Pagos de alquileres"
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

          <IncomeSummaryWidget class="order-2 mt-4 lg:w-full lg:mt-0 lg:order-1"
            :chart="comparisonRevenue"
            :headerInfo="comparisonRevenue.headers"
          />
        </article>
      </section>
    </main>
  </AppLayout>
</template>
