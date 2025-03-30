<script setup lang="ts">
import { router } from "@inertiajs/core";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { computed, ref } from "vue";
import { config } from "@/config";

import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "@/Components/WelcomeWidget.vue";

import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { useResponsive } from "@/utils/useResponsive";
import UnitsWithExpiredRents from "@/Components/Dashboard/UnitsWithExpiredRents.vue";

const { t } = useI18n();

interface IStatDetails {
  total: number;
  income?: number;
  outcome?: number;
}

interface IExpiringRents {
  expired: number;
  in_month: number;
  within_three_months: number;
}

interface IWelcomeWidgetProps {
  message: string;
  username?: string;
  cards?: any[];
  actionLabel?: string;
  actionLink?: string;
  rounded?: boolean;
  size?: string;
  class?: string;
}

interface IIncomeSummaryProps {
  chart: {
    series: any[];
    options: Record<string, any>;
  };
  labels?: string[];
  type: string;
  title: string;
  description: string;
  sections?: any[];
  sectionTotalField: string;
}
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
  totals: {
    type: Object,
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
  rentsStats: {
    type: Number,
  },
  expiringRents: {
    type: Object as () => IExpiringRents,
    default() {
      return {
        expired: 0,
        in_month: 0,
        within_three_months: 0,
      };
    },
  },
  propertyStats: {
    type: Object,
  },
  unitsRequiringAction: {
    type: Array,
    default: () => [],
  },
});

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
    colors: ["#80CDFE", "#fa6b88"],
  },
  series: [
    {
      name: t("Inflow"),
      data: props.revenue.map((item: IStatDetails) => item.income ?? 0),
    },
    {
      name: t("Outflow"),
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

const { isMobile } = useResponsive();

const unitStats = computed(() => {
  const units = props.propertyStats?.units;

  return [
    {
      label: t("Total units"),
      value: Number(units?.rented) + Number(units?.available) + Number(units?.building) + Number(units?.maintenance) || 0,
    },
    {
      label: t("Rented"),
      icon: "fa-money",
      value: `${units?.rented || 0}`,
    },
    {
      label: t("Available"),
      icon: "fa-money",
      value: `${units?.available || 0}`,
    },
  ];
});


const expirationStats = computed(() => {

  return [
    {
      label: t("Expiring this month"),
      icon: "fa-money",
      value: props.expiringRents.in_month,
    },
    {
      label: t("Expire within 3 months"),
      icon: "fa-money",
      value: props.expiringRents.within_three_months,
    },
  ];
});

const getMonthsOfYear = (locale = "es-ES") => {
  const startDate = startOfYear(new Date());
  return [...Array(12).keys()].map((monthIndex) => {
    return new Intl.DateTimeFormat(locale, {
      month: "short",
    }).format(startOfMonth(addMonths(startDate, monthIndex)));
  });
};

const currentMonth = new Date().getMonth();
const interestPerformance = {
  headers: {
    gapName: "Year",
    month: props.paidCommissions?.months.at(currentMonth).income,
    avg: props.paidCommissions?.avg,
    current: props.paidCommissions?.year,
  },
  options: {
    chart: {
      id: "commissions",
      type: "bar",
    },
    stroke: {
      curve: "smooth",
    },
    xaxis: {
      categories: [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
      ],
    },
    colors: [config.colors.highlight, config.colors.info],
  },
  series: [
    {
      name: t("Interest profit"),
      data: props.paidCommissions?.months.map(
        (item: Record<string, any>) => item.income ?? 0
      ),
    },
  ],
};
const summaryType = ref("gains");
</script>

<script lang="ts">
import DashboardTemplate from "./Partials/DashboardTemplate.vue";
import OnboardingSection from "./Partials/OnboardingSection.vue";
import { inject } from "vue";
import ExpiringRentsChart from "./Partials/ExpiringRentsChart.vue";
import RentsWidget from "./Partials/RentsWidget.vue";
import WidgetPropertiesStats from "./Partials/WidgetPropertiesStats.vue";
import { addMonths, startOfMonth, startOfYear } from "date-fns";
import ChartBar from "./Partials/ChartBar.vue";
import ComissionSummaryWidget from "./Partials/ComissionSummaryWidget.vue";
import FastAccessOptionsDashboard from "./Partials/FastAccessOptionsDashboard.vue";
import WidgetRentStats from "./Partials/WidgetRentStats.vue";

export default {
  layout: DashboardTemplate,
  components: { OnboardingSection },
};
</script>

<template>
  <main>
    <OnboardingSection v-auto-animate v-if="false" :steps="onBoardSteps"
      :title="$t('Explore {appName}', { appName: config.appName })"
      :description="$t('Initial steps to setup your system')" @close="isOnboardingOpen = !isOnboardingOpen" />
    <section class="w-full space-y-4">
      <UnitsWithExpiredRents :units="unitsRequiringAction" v-if="unitsRequiringAction.length" />
      <section class="md:flex md:space-x-4">
        <section class="flex flex-col justify-between w-full md:w-9/12"
          <FastAccessOptionsDashboard display="row" />
          <ComissionSummaryWidget :summary-type="summaryType" :paidCommissions="paidCommissions" :accounts="accounts"
            :stats="stats" :is-mobile="isMobile" />
          <Link href="/property-reports"
            class="flex items-center justify-center w-full h-10 mt-4 transition-all rounded-md shadow-sm hover:text-primary hover:font-bold bg-base-lvl-3 md:mt-4">
          {{ $t("Pending owner draws") }}
          <IMdiChevronRight />
          <span> {{ pendingDraws }} </span>
          </Link>
        </section>
        <section class="order-1 md:space-y-2 lg:w-3/12 lg:order-2">
          <article class="w-full">
            <div class="justify-between block mb-2 space-y-2 dashboard-bank-accounts">
              <WidgetPropertiesStats :total="propertyStats?.properties" :description="$t('Properties')"
                :unit-stats="unitStats" />
              <WidgetRentStats :total="rentsStats" :description="$t('Rents')" :unit-stats="expirationStats" />
            </div>
          </article>
        </section>
      </section>
    </section>
    <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
      <section class="order-2 mt-4 w-full lg:mt-0 lg:order-1">
        <WelcomeWidget :message="$t('month performance')" class="order-2 mt-4 lg:mt-0 lg:order-1" rounded
          size="default">
          <template #actions>
            <section class="flex space-x-2 w-full justify-end">
              <button @click="summaryType = 'gains'"
                class="bg-base-lvl-2 capitalize py-1 rounded-3xl text-body-1 px-4 border border-transparent" :class="{
                  'bg-primary/10 border-primary  text-primary': summaryType == 'gains',
                }">
                {{ $t("earnings") }}
              </button>
              <button @click="summaryType = 'cash-flow'"
                class="bg-base-lvl-2 capitalize py-1 rounded-3xl text-body-1 px-4 border border-transparent" :class="{
                  'bg-primary/10 border-primary  text-primary':
                    summaryType == 'cash-flow',
                }">
                {{ $t("cashflow") }}
              </button>
            </section>
          </template>
          <template #content>
            <IncomeSummaryWidget v-if="summaryType == 'cash-flow'" :chart="comparisonRevenue"
              :style="{ height: '300px' }" :labels="getMonthsOfYear()" type="line" :title="$t('Revenue Comparison')"
              :description="$t('Monthly revenue comparison')" sectionTotalField="total" />
            <ChartBar v-else class="bg-white rounded-lg overflow-hidden" title="Ganancias"
              description="Ganancias por comisiones en el año" :chart="interestPerformance" height="260px"
              :headerInfo="interestPerformance.headers" />
          </template>
        </WelcomeWidget>
      </section>
    </section>
    <section class="w-full mt-4">
      <RentsWidget class="shadow-sm bg-base-lvl-3" />
    </section>
  </main>
</template>

<style lang="scss">
@media (max-width: 1024px) {
  .dashboard-bank-accounts .text-3xl {
    font-size: 1em;
  }
}
</style>
