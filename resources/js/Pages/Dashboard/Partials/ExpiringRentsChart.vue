<template>
  <div class="w-full comparison-card bg-white py-3 relative">
    <div style="width: 100%; height: 380px" class="relative py-1">
      <VueApexCharts
        ref="chartRef"
        width="100%"
        height="100%"
        type="donut"
        :options="chartConfig.options"
        :series="chartConfig.series.map((value) => Number(value))"
      />
      <IClarityContractLine class="absolute text-4xl report-icon-center" />
      <section class="flex absolute w-full bottom-28">
        <article v-for="item in legend" class="w-full text-center">
          <header>
            <span class="font-bold"> {{ item.value }}</span>
          </header>
          <p>{{ item.label }}</p>
        </article>
      </section>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { useI18n } from "vue-i18n";
import VueApexCharts from "vue3-apexcharts";
import { computed, ref } from "vue";

const props = defineProps<{
  stats: {
    expired: number;
    in_month: number;
    within_three_months: number;
  };
}>();

const labels: Record<string, string> = {
  expired: "Expired",
  in_month: "Expiring this month",
  within_three_months: "Expire within 3 months",
};

const { t } = useI18n();
const chartRef = ref();
const chartConfig = {
  options: {
    chart: {
      id: "vuechart-example",
      type: "donut",
      offsetY: -20,
      sparkline: {
        enabled: true,
      },
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
    plotOptions: {
      pie: {
        startAngle: -90,
        endAngle: 90,
        offsetY: 10,
      },
    },
    grid: {
      padding: {
        bottom: -80,
      },
    },
    labels: Object.keys(props.stats).map((item) => t(labels[item])),
  },
  series: Object.values(props.stats),
};

const legend = computed(() => {
  return chartConfig.options.labels.map((label, index) => {
    return {
      label: label,
      value: chartConfig.series[index],
      color: "",
    };
  });
});
</script>

<style lang="scss">
.report-icon-center {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -140%);
}
</style>
