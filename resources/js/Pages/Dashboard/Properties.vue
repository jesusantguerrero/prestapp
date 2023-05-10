<script lang="ts">
import { computed } from "vue";
import { addMonths, startOfYear, startOfMonth } from "date-fns";
import { useI18n } from "vue-i18n";
import { ref } from "vue";

import DashboardTemplate from "./Partials/DashboardTemplate.vue";
import WidgetPropertiesStats from "./Partials/WidgetPropertiesStats.vue";

export default {
  layout: DashboardTemplate,
  components: { WidgetPropertiesStats },
};
</script>

<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";

import BudgetProgress from "@/Components/BudgetProgress.vue";
import IncomeSummaryWidget from "@/Pages/Dashboard/Partials/IncomeSummaryWidget.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";
import ChartBar from "./Partials/ChartBar.vue";
import PropertyInvoiceWidget from "./Partials/PropertyInvoiceWidget.vue";
import NextPaymentsWidget from "../Loans/NextPaymentsWidget.vue";
import PaymentsCard from "@/Components/PaymentsCard.vue";
import RentsWidget from "./Partials/RentsWidget.vue";
import ExpiringRentsChart from "./Partials/ExpiringRentsChart.vue";

import { formatMoney } from "@/utils/formatMoney";
import { config } from "@/config";

const getMonthsOfYear = (locale = "es-ES") => {
  const startDate = startOfYear(new Date());
  return [...Array(12).keys()].map((monthIndex) => {
    return new Intl.DateTimeFormat(locale, {
      month: "short",
    }).format(startOfMonth(addMonths(startDate, monthIndex)));
  });
};

const { t } = useI18n();
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
  stats: {
    type: Object,
  },
  ownerStats: {
    type: Object,
  },
  paidInterest: {
    type: Array,
  },
  paidCommissions: {
    type: Object,
  },
  pendingDraws: {
    type: Object,
  },
  totals: {
    type: Object,
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
});

const unitStats = [
  {
    label: "Total unidades",
    value: props.stats?.total || 0,
  },
  {
    label: "Alquiladas",
    icon: "fa-money",
    value: `${props.stats?.rented || 0}`,
  },
  {
    label: "Libres",
    icon: "fa-money",
    value: `${props.stats?.available || 0}`,
  },
];

const ownerStats = computed(() => [
  {
    label: "Propietarios",
    value: props.ownerStats?.total || 0,
  },
  {
    label: "Pagado mes",
    value: formatMoney(props.ownerStats?.paid || 0),
  },
]);

const comparisonRevenue = {
  headers: {
    gapName: "Year",
    previous: props.revenue?.total,
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
    colors: ["#68B7F2", "#80CDFE"],
  },
  series: [
    {
      name: t("Income"),
      data: props.revenue.map((item: Record<string, any>) => item.income ?? 0),
    },
    {
      name: t("Expense"),
      data: props.revenue.map((item: Record<string, any>) => item.outcome ?? 0),
    },
  ],
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
      name: "Ganancias intereses",
      data: props.paidCommissions?.months.map(
        (item: Record<string, any>) => item.income ?? 0
      ),
    },
  ],
};

const summaryType = ref("cash-flow");
</script>

<template>
  <main>
    <header class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">
      <WelcomeWidget
        message="Estadisticas de propiedades"
        class="text-body-1 w-full md:w-7/12 shadow-md"
        size="small"
        :cards="unitStats"
      >
        <template #append>
          <section class="py-4">
            <BudgetProgress
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
            </BudgetProgress>
          </section>
        </template>
      </WelcomeWidget>

      <div class="text-body-1 w-full md:w-5/12">
        <WelcomeWidget
          message="Distribucion a propietarios"
          class="text-body-1 w-full shadow-md"
          size="small"
          :cards="ownerStats"
        />
        <Link
          href="/property-reports"
          class="flex items-center cursor-pointer h-14 hover:text-primary hover:font-bold transition-all justify-center w-full mt-4 rounded-md bg-white shadow-md md:mt-4"
        >
          Distribución a propietarios pendientes
          <IMdiChevronRight />
          <span> {{ pendingDraws }} </span>
        </Link>
      </div>
    </header>

    <section class="mt-8 mb-24">
      <section class="flex lg:space-x-4 flex-col w-full lg:flex-row">
        <article class="space-y-5 lg:w-7/12">
          <WelcomeWidget
            message="Rendimiento del mes"
            class="order-2 mt-4 lg:mt-0 lg:order-1"
          >
            <template #actions>
              <section class="flex space-x-2">
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
                title="Flujo de efectivo"
                description="Movimiento de efectivo del año por mes en cuenta de inmobiliaria"
                class="order-2 mt-4 lg:w-full lg:mt-0 lg:order-1"
                type="bar"
                :style="{ height: '350px' }"
                :labels="getMonthsOfYear()"
                :chart="comparisonRevenue"
                :headerInfo="comparisonRevenue.headers"
              />
              <ChartBar
                v-else
                class="bg-white rounded-lg overflow-hidden"
                title="Ganancias"
                description="Ganancias por comisiones en el año"
                :chart="interestPerformance"
                :headerInfo="interestPerformance.headers"
              />
            </template>
          </WelcomeWidget>
        </article>
        <section class="lg:w-5/12 space-y-4">
          <WelcomeWidget
            :message="$t('Expiring rents')"
            class="text-body-1 w-full shadow-md"
            :action-label="$t('See details')"
            action-link="/reports/expiring-rents"
          >
            <template #content>
              <ExpiringRentsChart :stats="expiringRents" :style="{ height: '350px' }" />
            </template>
          </WelcomeWidget>
          <WidgetPropertiesStats
            :total="stats?.total"
            :description="$t('Properties')"
            :unit-stats="unitStats"
          />
        </section>
      </section>
      <section class="flex mt-4 space-x-4">
        <PropertyInvoiceWidget class="w-full md:w-7/12" />
        <NextPaymentsWidget
          title="Pagos por periodo"
          endpoint="/api/rent-payments?"
          method="back"
          default-range="7D"
          date-field="payment_date"
          class="rounded-md border w-full md:w-5/12"
          section="invoices"
          :ranges="[
            { label: '1D', value: [1, 1], tooltip: 'Hoy' },
            { label: '7D', value: [7, 0], tooltip: '7 Dias' },
            { label: '1M', value: [30, 0], tooltip: '30 Dias' },
            { label: '3M', value: [90, 0], tooltip: '3 Meses' },
          ]"
        >
          <template #beforeRange>
            <Link href="/payment-center?tab=payments">Todos</Link>
          </template>
          <template v-slot:content="{ list }">
            <article class="py-4 my-2 h-[380px] overflow-auto ic-scroller">
              <template v-if="list.length">
                <PaymentsCard
                  v-for="payment in list"
                  :payment="payment"
                  type="invoices"
                />
              </template>
              <section
                v-else
                class="flex text-body-1 flex-col justify-center items-center w-full"
              >
                <IMdiNoteOff class="text-8xl" />
                <p class="mt-8">No hay pagos realizados en este rango de fechas</p>
              </section>
            </article>
          </template>
        </NextPaymentsWidget>
      </section>
      <WelcomeWidget
        message="Unidades recientes"
        class="text-body-1 w-full shadow-md"
        :cards="unitStats"
        v-if="false"
      >
        <template #content>
          <div class="rounded-md h-44 w-full bg-base-lvl-2 p-4 mb-4">
            <h4 class="font-bold">DOP 5000</h4>
            <p class="mt-4"><IconMarker /> <span>Address</span></p>
            <p class="space-x-4 mt-2">
              <span>3 Dormitorios</span><span>1 Baño</span><span>300 mts</span>
            </p>
          </div>
        </template>
      </WelcomeWidget>
      <RentsWidget class="w-full mt-4" />
    </section>
  </main>
</template>
