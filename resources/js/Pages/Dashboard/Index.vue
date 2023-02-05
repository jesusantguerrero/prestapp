<script setup lang="ts">
import { router } from "@inertiajs/core";

import AppLayout from "@/Components/templates/AppLayout.vue";
// @ts-ignore
import { AtBackgroundIconCard, AtButton } from "atmosphere-ui";
import AppButton from "@/Components/shared/AppButton.vue";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "./Partials/WelcomeWidget.vue";
import SectionFooterCard from "./SectionFooterCard.vue";

import { formatMoney } from "@/utils/formatMoney";
import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";

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
  accounts: {
    type: Object,
    default() {
      return {};
    },
  },
  stats: {
    type: Object,
    default() {
      return {};
    },
  },
  bank: {
    type: Number,
  },
  dailyBox: {
    type: Object,
    required: true,
  },
  realState: {
    type: Object,
    required: true,
  },
});

const { openModal } = useToggleModal("contact");
const welcomeCards = [
  {
    label: "Crear un contacto",
    icon: "contact",
    action() {
      openModal({
        data: { type: "lender " },
        isOpen: true,
      });
    },
  },
  {
    label: "Crear un prestamo",
    icon: "money",
    action() {
      router.visit("/loans/create");
    },
  },
  {
    label: "Agregar propiedad",
    icon: "home",
    action() {
      router.visit("/properties/create");
    },
  },
  {
    label: "Crear un contrato",
    icon: "document",
    action() {
      router.visit("/rents/create");
    },
  },
];

interface IStatDetails {
  total: number;
}

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
      data: props.revenue.previousYear.values.map((item: IStatDetails) => item.total),
    },
    {
      name: "current year",
      data: props.revenue.currentYear.values.map((item: IStatDetails) => item.total),
    },
  ],
};

const { openTransactionModal } = useTransactionModal();
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="p-5 mx-auto text-gray-500 sm:px-6 lg:px-8">
      <section class="w-full md:flex md:space-x-4">
        <div class="flex flex-col justify-between w-full md:w-9/12">
          <WelcomeWidget message="Hola, " :username="user.name" class="shadow-sm">
            <template #content>
              <section
                class="grid-rows-1 py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2"
              >
                <SectionFooterCard
                  title="Ganancias netas"
                  :value="
                    formatMoney(accounts.assets.total + accounts.liabilities?.total)
                  "
                >
                  <template #footer>
                    <p
                      class="flex items-center text-xs text-success md:text-base"
                      rounded
                    >
                      <IMdiArrowUpThick />
                      <span class="font-bold">
                        {{ formatMoney(accounts.assets.income) }} Recibido
                      </span>
                    </p>
                    <p
                      class="flex items-center text-xs text-error/70 md:text-base"
                      rounded
                    >
                      <IMdiArrowDownThick />
                      <span class="font-bold">
                        {{ formatMoney(accounts.assets.outcome) }} Gastado
                      </span>
                    </p>
                  </template>
                </SectionFooterCard>
                <SectionFooterCard
                  title="Balance pendiente"
                  :value="formatMoney(stats.outstanding)"
                  class="md:pl-6"
                >
                  <template #footer class="flex">
                    <AtButton
                      class="flex items-center px-2 -ml-6 text-xs md:text-base text-error/70 hover:bg-error/10"
                      rounded
                    >
                      <IMdiFileDocumentAlertOutline class="mr-2" />
                      <span class="font-bold">
                        {{ formatMoney(stats.overdue) }} En mora
                      </span>
                    </AtButton>
                    <AtButton
                      rounded
                      class="flex items-center text-xs md:text-base text-primary hover:bg-primary/10"
                    >
                      <IIcSharpPayment class="mr-2" /> Recibir Pago
                    </AtButton>
                  </template>
                </SectionFooterCard>
              </section>
            </template>
          </WelcomeWidget>
          <div
            class="flex flex-col items-center justify-center w-full h-10 mt-4 rounded-md shadow-sm bg-base-lvl-3 md:mt-4"
          >
            Distribución a propietarios pendientes
          </div>
        </div>
        <WelcomeWidget message="Accesos Rapidos" class="w-full mt-4 md:mt-0 md:w-3/12">
          <template #content>
            <div class="grid grid-cols-2 py-2">
              <button
                v-for="card in welcomeCards"
                class="flex flex-col items-center justify-center w-full py-3 text-center transition-all ease-in bg-white border-2 border-transparent rounded-lg hover:border-primary group text-primary"
                @click="card.action()"
              >
                <IMdiUserOutline class="text-4xl" v-if="card.icon == 'contact'" />
                <IMdiMoney class="text-4xl" v-if="card.icon == 'money'" />
                <IMdiHomeCityOutline class="text-4xl" v-if="card.icon == 'home'" />
                <IMdiFileDocument class="text-4xl" v-if="card.icon == 'document'" />

                <p class="text-sm font-bold text-body group-hover:text-primary">
                  {{ card.label }}
                </p>
              </button>
            </div>
          </template>
        </WelcomeWidget>
      </section>
      <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
        <IncomeSummaryWidget
          class="order-2 mt-4 lg:w-9/12 lg:mt-0 lg:order-1"
          :chart="comparisonRevenue"
          :style="{ height: '310px' }"
        />
        <article class="order-1 space-y-2 lg:w-3/12 lg:order-2">
          <AtBackgroundIconCard
            class="h-32 border-2 cursor-pointer text-primary bg-primary/10 border-primary/20"
            icon="fas fa-wallet"
            :value="formatMoney(props.dailyBox?.balance | 0)"
            title="Cuenta de Prestamos"
          />
          <AtBackgroundIconCard
            class="h-32 border-2 cursor-pointer text-secondary bg-secondary/10 border-secondary/20"
            icon="fas fa-wallet"
            :value="formatMoney(props.realState.balance | 0)"
            title="Cuenta Inmobiliaria"
          />
          <AppButton variant="secondary" class="w-full" @click="openTransactionModal()">
            Agregar fondos
          </AppButton>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
