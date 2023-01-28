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
      <section class="w-full flex space-x-4">
        <div class="w-9/12 flex flex-col justify-between">
          <WelcomeWidget message="Hola, " :username="user.name">
            <template #content>
              <section class="grid grid-cols-2 grid-rows-1 py-4 divide-x-2">
                <SectionFooterCard
                  title="Ganancias netas"
                  :value="formatMoney(accounts.income - accounts.expenses)"
                >
                  <template #footer>
                    <p class="flex text-success items-center" rounded>
                      <IMdiArrowUpThick />
                      <span class="font-bold">
                        {{ formatMoney(accounts.income) }} Recibido
                      </span>
                    </p>
                    <p class="flex text-error/70 items-center" rounded>
                      <IMdiArrowDownThick />
                      <span class="font-bold">
                        {{ formatMoney(accounts.expenses) }} Gastado
                      </span>
                    </p>
                  </template>
                </SectionFooterCard>
                <SectionFooterCard
                  title="Balance pendiente"
                  :value="formatMoney(stats.outstanding)"
                  class="pl-6"
                >
                  <template #footer class="flex">
                    <AtButton
                      class="flex text-error/70 hover:bg-error/10 items-center -ml-6 px-2"
                      rounded
                    >
                      <IMdiFileDocumentAlertOutline class="mr-2" />
                      <span class="font-bold">
                        {{ formatMoney(stats.overdue) }} En mora
                      </span>
                    </AtButton>
                    <AtButton
                      rounded
                      class="flex items-center text-primary hover:bg-primary/10"
                    >
                      <IIcSharpPayment class="mr-2" /> Recibir Pago
                    </AtButton>
                  </template>
                </SectionFooterCard>
              </section>
            </template>
          </WelcomeWidget>
          <div
            class="rounded-md bg-base-lvl-3 w-full mt-auto h-10 items-center justify-center flex flex-col"
          >
            Facturas a Dueños pendientes
          </div>
        </div>
        <WelcomeWidget message="Accesos Rapidos" class="w-3/12">
          <template #content>
            <div class="grid grid-cols-2 py-2">
              <button
                v-for="card in welcomeCards"
                class="w-full hover:border-primary border-2 transition-all ease-in group border-transparent py-3 rounded-lg flex flex-col text-center bg-white text-primary justify-center items-center"
                @click="card.action()"
              >
                <IMdiUserOutline class="text-4xl" v-if="card.icon == 'contact'" />
                <IMdiMoney class="text-4xl" v-if="card.icon == 'money'" />
                <IMdiHomeCityOutline class="text-4xl" v-if="card.icon == 'home'" />
                <IMdiFileDocument class="text-4xl" v-if="card.icon == 'document'" />

                <p class="text-sm text-body font-bold group-hover:text-primary">
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
            class="text-primary bg-primary/10 border-primary/20 border-2 h-32 cursor-pointer"
            icon="fas fa-wallet"
            :value="formatMoney(props.dailyBox?.balance | 0)"
            title="Cuenta de Prestamos"
          />
          <AtBackgroundIconCard
            class="text-secondary bg-secondary/10 border-secondary/20 border-2 h-32 cursor-pointer"
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
