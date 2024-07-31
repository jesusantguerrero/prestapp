<script setup lang="ts">
import { router } from "@inertiajs/core";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { AtBackgroundIconCard } from "atmosphere-ui";
import { ref } from "vue";
import { config } from "@/config";

import AppButton from "@/Components/shared/AppButton.vue";
import IncomeSummaryWidget from "./Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "@/Components/WelcomeWidget.vue";
import SectionFooterCard from "./Partials/SectionFooterCard.vue";
import FastAccessOptions from "./Partials/FastAccessOptions.vue";

import { formatMoney } from "@/utils/formatMoney";
import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { useResponsive } from "@/utils/useResponsive";

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
  expiringRents: {
    type: Object,
    default() {
      return {
        expired: 0,
        in_month: 0,
        within_three_months: 0,
      };
    },
  },
  rentStats: {
    type: Object,
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

const unitStats = [
  {
    label: t("Total units"),
    value: props.rentStats?.total || 0,
  },
  {
    label: t("Rented"),
    icon: "fa-money",
    value: `${props.rentStats?.rented || 0}`,
  },
  {
    label: t("Available"),
    icon: "fa-money",
    value: `${props.rentStats?.available || 0}`,
  },
];

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
const summaryType = ref("cash-flow");
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
import BudgetProgress from "@/Components/BudgetProgress.vue";

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
        <WelcomeWidget :message="$t('Performance of the month')" class="shadow-sm">
          <template #content>
            <section class="flex py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2">
              <SectionFooterCard
                :title="$t('Gross earnings')"
                :value="formatMoney(paidCommissions.totalInPeriod)"
                class="w-full"
              >
                <template #footer>
                  <p class="flex items-center text-xs text-success md:text-sm" rounded>
                    <IMdiArrowUpThick />
                    <span class="font-bold">
                      {{ formatMoney(accounts.cash_and_bank?.at(0)?.income ?? 0) }}
                      {{ !isMobile ? $t("Inflow") : "" }}
                    </span>
                  </p>
                  <p class="flex items-center text-xs text-error/70 md:text-sm" rounded>
                    <IMdiArrowDownThick />
                    <span class="font-bold">
                      {{ formatMoney(accounts.cash_and_bank?.at(0)?.outcome ?? 0) }}
                      {{ !isMobile ? $t("Outflow") : "" }}
                    </span>
                  </p>
                </template>
              </SectionFooterCard>
              <SectionFooterCard
                :title="$t('Pending balance')"
                :value="`${formatMoney(stats.outstanding)} (${formatMoney(
                  stats.outstanding_in_month
                )})`"
                class="md:pl-6 w-full"
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
                      {{ formatMoney(stats.overdue) }} {{ $t("Late") }}
                    </span>
                  </Link>
                </template>
              </SectionFooterCard>
            </section>
          </template>
        </WelcomeWidget>
        <Link
          href="/property-reports"
          class="flex items-center justify-center w-full h-10 mt-4 transition-all rounded-md shadow-sm hover:text-primary hover:font-bold bg-base-lvl-3 md:mt-4"
        >
          {{ $t("Pending owner draws") }}
          <IMdiChevronRight />
          <span> {{ pendingDraws }} </span>
        </Link>
      </div>
      <WelcomeWidget
        :message="$t('Fast access')"
        class="w-full mt-4 md:block md:mt-0 md:w-3/12"
      >
        <template #content>
          <FastAccessOptions />
        </template>
      </WelcomeWidget>
    </section>
    <section class="flex flex-col mt-8 lg:space-x-4 lg:flex-row">
      <section class="order-2 mt-4 lg:w-9/12 lg:mt-0 lg:order-1">
          <WelcomeWidget
            :message="$t('month performance')"
            class="order-2 mt-4 lg:mt-0 lg:order-1"
          >
            <template #actions>
              <section class="flex space-x-2 w-full justify-end">
                <button
                  @click="summaryType = 'gains'"
                  class="bg-base-lvl-2 capitalize py-1 rounded-3xl text-body-1 px-4 border border-transparent"
                  :class="{
                    'bg-primary/10 border-primary  text-primary': summaryType == 'gains',
                  }"
                >
                  {{ $t("earnings") }}
                </button>
                <button
                  @click="summaryType = 'cash-flow'"
                  class="bg-base-lvl-2 capitalize py-1 rounded-3xl text-body-1 px-4 border border-transparent"
                  :class="{
                    'bg-primary/10 border-primary  text-primary':
                      summaryType == 'cash-flow',
                  }"
                >
                  {{ $t("cashflow") }}
                </button>
              </section>
            </template>
            <template #content>
              <IncomeSummaryWidget
              v-if="summaryType == 'cash-flow'"
              :chart="comparisonRevenue"
              :style="{ height: '300px' }"
              :labels="getMonthsOfYear()"
            />
              <ChartBar
                v-else
                class="bg-white rounded-lg overflow-hidden"
                title="Ganancias"
                description="Ganancias por comisiones en el año"
                :chart="interestPerformance"
                height="260px"
                :headerInfo="interestPerformance.headers"
              />
            </template>
          </WelcomeWidget>

      </section>

      <article class="order-1 md:space-y-2 lg:w-3/12 lg:order-2">
        <div
          class="justify-between block mb-2 space-y-2 dashboard-bank-accounts"
        >

        <WidgetPropertiesStats
          :total="rentStats?.total"
          :description="$t('Properties')"
          :unit-stats="unitStats"
        />
        <!-- <BudgetProgress
        :goal="totals?.total"
        :current="totals?.paid"
        class="h-2.5 text-white rounded-md"
        :progress-class="['bg-primary', 'bg-primary/20']"
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
      </BudgetProgress> -->
          <AtBackgroundIconCard
            class="md:h-32 border-2 cursor-pointer text-secondary bg-secondary/10 border-secondary/20"
            icon="fas fa-wallet"
            :value="formatMoney(props.realState.balance | 0)"
            :title="t('Real estate account')"
            icon-class="text-secondary opacity-40"
          />
        </div>
        <AppButton variant="secondary" class="w-full" @click="openLoanAccount()">
          {{ $t("Add funds") }}
        </AppButton>
      </article>
    </section>
    <section class="md:flex md:space-x-4">
      <article class="md:w-5/12 mt-4">
        <WelcomeWidget
          :message="$t('Expiring rents')"
          class="text-body-1 shadow-sm"
          :action-label="$t('See details')"
          action-link="/rent-renewals/"
        >
          <template #content>
            <ExpiringRentsChart :stats="expiringRents" :style="{ height: '390px' }" />
          </template>
      </WelcomeWidget>
      </article>
    <RentsWidget class="md:w-7/12 mt-4 shadow-sm bg-base-lvl-3" />
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
