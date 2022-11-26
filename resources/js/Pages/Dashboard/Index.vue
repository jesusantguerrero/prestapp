<script setup>
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtBackgroundIconCard, AtButton, AtDashlide } from "atmosphere-ui";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";

const slideOptions = [
  {
    name: "caja",
    title: "Small Box",
  },
  {
    name: "pagos",
    title: "Next Payments",
  },
  {
    name: "ganancias",
    title: "Incomes",
  },
  {
    name: "debtors",
    title: "Debtors",
  },
];
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 text-gray-500">
      <section class="flex space-x-4">
        <IncomeSummaryWidget class="w-8/12" />
        <article class="w-5/12 space-y-5">
          <AtBackgroundIconCard
            class="text-white bg-blue-400 h-36"
            icon="fas fa-wallet"
            value="20,000"
            title="Capital Prestamo"
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
