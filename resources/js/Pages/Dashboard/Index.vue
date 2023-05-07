<script setup lang="ts">
import { router } from "@inertiajs/core";
// @ts-ignore
import { AtBackgroundIconCard } from "atmosphere-ui";
import AppButton from "@/Components/shared/AppButton.vue";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "./Partials/WelcomeWidget.vue";
import SectionFooterCard from "./Partials/SectionFooterCard.vue";

import { formatMoney } from "@/utils/formatMoney";
import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import FastAccessOptions from "./Partials/FastAccessOptions.vue";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToggleModal } from "@/Modules/_app/useToggleModal";

const { t } = useI18n();

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
  isTeamApproved: {
    type: Boolean,
  },
});

interface IStatDetails {
  total: number;
}

const comparisonRevenue = {
  headers: {
    gapName: "Year",
    previous: props.revenue?.total,
    current: props.revenue?.total,
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
      name: t("Entradas"),
      data: props.revenue.map((item: IStatDetails) => item.income ?? 0),
    },
    {
      name: t("Salidas"),
      data: props.revenue.map((item: IStatDetails) => item.outcome ?? 0),
    },
  ],
};

const { openTransactionModal } = useTransactionModal();
const { openModal } = useToggleModal("contact");

const openLoanAccount = () => {
  openTransactionModal({
    mode: "TRANSFER",
    hideTypeSelector: true,
    transactionData: {
      account_id: "opening_balance_capital",
      counter_account_id: "loan_business",
    },
  });
};

const onBoardSteps = [
  {
    title: "Add business config",
    description: "Before using the app you should setup your business configureation",
    action: {
      label: "Setup config",
      variant: "primary",
      event: () => {
        router.visit("/settings/business");
      },
    },
  },
  {
    title: "Create your contacts",
    description:
      "Your contacts: Borrowers, Tenants and Owners are an essential part of the system. you can create them from here",
    action: {
      label: "Create contact",
      variant: "neutral",
      event: () => {
        openModal();
      },
    },
  },
  {
    title: "Create your portfolio",
    description:
      "Properties and Units are the heart of the system, you can assign them to owners, create rents, invoices age generated against them",
    action: {
      label: "Add property",
      variant: "neutral",
      event: () => {
        router.visit("/properties/create");
      },
    },
  },
  {
    title: "Open your balances",
    description:
      "If you are going to borrow money you first need to open your account for loans",
    action: {
      label: "Set opening balance",
      variant: "neutral",
      event: () => {
        openLoanAccount();
      },
    },
  },
  {
    title: "Create a loan",
    description:
      "Add loans, register installment payments, set up penalties within the Loan module",
    action: {
      label: "Add loan",
      variant: "neutral",
      event: () => {
        openLoanAccount();
      },
    },
  },
];

const isOnboardingOpen = inject("isOnboardingOpen");
</script>

<script lang="ts">
import DashboardTemplate from "./Partials/DashboardTemplate.vue";
import OnboardingSection from "./Partials/OnboardingSection.vue";
import { inject } from "vue";

export default {
  layout: DashboardTemplate,
  components: { OnboardingSection },
};
</script>

<template>
  <main>
    <OnboardingSection
      v-auto-animate
      v-if="isOnboardingOpen"
      :steps="onBoardSteps"
      :title="$t('Explore ICLoan')"
      :description="$t('Initial steps to setup your system')"
      @close="isOnboardingOpen = !isOnboardingOpen"
    />
    <section class="w-full md:flex md:space-x-4">
      <div class="flex flex-col justify-between w-full md:w-9/12">
        <WelcomeWidget
          :message="`${$t('Welcome')},`"
          :username="user.name"
          class="shadow-sm"
        >
          <template #content>
            <section
              class="grid-rows-1 py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2"
            >
              <SectionFooterCard
                title="Ganancias netas"
                :value="formatMoney(paidCommissions.totalInPeriod)"
              >
                <template #footer>
                  <p class="flex items-center text-xs text-success md:text-sm" rounded>
                    <IMdiArrowUpThick />
                    <span class="font-bold">
                      {{ formatMoney(accounts.cash_and_bank?.at(0)?.income ?? 0) }}
                      Recibido
                    </span>
                  </p>
                  <p class="flex items-center text-xs text-error/70 md:text-sm" rounded>
                    <IMdiArrowDownThick />
                    <span class="font-bold">
                      {{ formatMoney(accounts.cash_and_bank?.at(0)?.outcome ?? 0) }}
                      Gastado
                    </span>
                  </p>
                </template>
              </SectionFooterCard>
              <SectionFooterCard
                title="Balance pendiente"
                :value="`${formatMoney(stats.outstanding)} (${formatMoney(
                  stats.outstanding_in_month
                )})`"
                class="md:pl-6"
                value-link="/property-reports?filters[owner]=&filters[property]=&filters[section]=invoices"
              >
                <template #footer class="flex">
                  <Link
                    class="flex items-center px-2 -ml-6 text-xs md:text-sm text-error/70 hover:bg-error/10"
                    rounded
                    href="/property-reports?filters[owner]=&filters[property]=&filters[section]=invoices&filters[status]=overdue"
                  >
                    <IMdiFileDocumentAlertOutline class="mr-2" />
                    <span class="font-bold">
                      {{ formatMoney(stats.overdue) }} En mora
                    </span>
                  </Link>
                  <!-- <AtButton
                    rounded
                    class="flex items-center text-xs md:text-base text-primary hover:bg-primary/10"
                  >
                    <IIcSharpPayment class="mr-2" /> Recibir Pago
                  </AtButton> -->
                </template>
              </SectionFooterCard>
            </section>
          </template>
        </WelcomeWidget>
        <Link
          href="/property-reports"
          class="flex items-center justify-center w-full h-10 mt-4 transition-all rounded-md shadow-sm hover:text-primary hover:font-bold bg-base-lvl-3 md:mt-4"
        >
          Distribución a propietarios pendientes
          <IMdiChevronRight />
          <span> {{ pendingDraws }} </span>
        </Link>
      </div>
      <WelcomeWidget
        message="Accesos Rapidos"
        class="hidden w-full mt-4 md:block md:mt-0 md:w-3/12"
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
          @click="openLoanAccount()"
        />
        <AtBackgroundIconCard
          class="h-32 border-2 cursor-pointer text-secondary bg-secondary/10 border-secondary/20"
          icon="fas fa-wallet"
          :value="formatMoney(props.realState.balance | 0)"
          title="Cuenta Inmobiliaria"
        />
        <AppButton variant="secondary" class="w-full" @click="openLoanAccount()">
          Agregar fondos
        </AppButton>
      </article>
    </section>
  </main>
</template>
