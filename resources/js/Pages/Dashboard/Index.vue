<script setup>
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtBackgroundIconCard, AtButton, AtDashlide } from "atmosphere-ui";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "./Partials/WelcomeWidget.vue";

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
        onboarding: {
            type: Array,
            default() {
                return []
            }
        }
});

const welcomeCards = [{
        label: 'Clientes con Prestamos',
        value: 2000,
        icon: 'fa-users'
    }, {

        label: 'Capital Prestado',
        icon: 'fa-money',
        value: 2000
    }, {

        label: 'Interes Ganado',
        icon: 'fa-wallet',
        value: 2000
    }, {
        label: 'Total Interes pagado',
        value: 2000,
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
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="p-5 mx-auto text-gray-500 sm:px-6 lg:px-8">
        <WelcomeWidget
            message="Bienvenido a PrestApp"
            :username="user.name"
            :cards="welcomeCards"
        />
      <section class="flex mt-8 space-x-4">
        <IncomeSummaryWidget class="w-8/12" />

        <article class="w-5/12 space-y-5">
          <AtBackgroundIconCard
            class="text-white bg-blue-400 h-36"
            icon="fas fa-wallet"
            value="20,000"
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
                value="20,000"
                title="Caja"
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
          </AtDashlide>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
