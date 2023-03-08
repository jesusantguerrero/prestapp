<script setup lang="ts">
import { router } from "@inertiajs/core";
// @ts-ignore
import { AtBackgroundIconCard, AtButton } from "atmosphere-ui";
import AppButton from "@/Components/shared/AppButton.vue";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "./Partials/WelcomeWidget.vue";
import SectionFooterCard from "./SectionFooterCard.vue";

import { formatMoney } from "@/utils/formatMoney";
import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import DashboardTemplate from "./Partials/DashboardTemplate.vue";
import FastAccessOptions from "./Partials/FastAccessOptions.vue";
import { Link } from "@inertiajs/vue3";

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
  paidCommissions: {
    type: Object,
    required: true,
  },
  dailyBox: {
    type: Object,
    required: true,
  },
  realState: {
    type: Object,
    required: true,
  },
  pendingDraws: {
    type: Number,
  },
});

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
  <DashboardTemplate :user="user">
    <section class="w-full md:flex md:space-x-4">
      <div class="flex flex-col justify-between w-full md:w-9/12">
        <WelcomeWidget message="Hola, " :username="user.name" class="shadow-sm">
          <template #content>
            <section
              class="grid-rows-1 py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2"
            >
              <SectionFooterCard
                title="Ganancias netas"
                :value="formatMoney(paidCommissions.balance)"
              >
                <template #footer>
                  <p class="flex items-center text-xs text-success md:text-base" rounded>
                    <IMdiArrowUpThick />
                    <span class="font-bold">
                      {{ formatMoney(accounts.assets.income) }} Recibido
                    </span>
                  </p>
                  <p class="flex items-center text-xs text-error/70 md:text-base" rounded>
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
        <Link
          href="/properties/management-tools"
          class="flex items-center hover:text-primary hover:font-bold transition-all justify-center w-full h-10 mt-4 rounded-md shadow-sm bg-base-lvl-3 md:mt-4"
        >
          Distribución a propietarios pendientes
          <IMdiChevronRight />
          <span> {{ pendingDraws }} </span>
        </Link>
      </div>
      <WelcomeWidget
        message="Accesos Rapidos"
        class="hidden md:block w-full mt-4 md:mt-0 md:w-3/12"
      >
        <template #content>
          <FastAccessOptions />
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
        <AppButton
          variant="secondary"
          class="w-full"
          @click="
            openTransactionModal({
              mode: 'TRANSFER',
            })
          "
        >
          Agregar fondos
        </AppButton>
      </article>
    </section>
  </DashboardTemplate>
</template>
