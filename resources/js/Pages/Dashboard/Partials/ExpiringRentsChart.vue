<template>
  <div class="w-full comparison-card bg-white py-3 relative">
    <div style="width: 100%; height: 380px" class="relative py-1">
      <VueApexCharts
        ref="chartRef"
        width="100%"
        height="100%"
        type="donut"
        :options="chartConfig.options"
        :series="chartConfig.series"
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

import formatMoney from "@/utils/formatMoney";

defineProps({
  title: {
    type: String,
    default: "bar",
  },
  description: {
    type: String,
    default: "bar",
  },
  type: {
    type: String,
    default: "bar",
  },
  headerInfo: {
    type: Object,
    required: true,
  },
  chart: {
    type: Object,
    required: true,
  },
  icon: {
    type: String,
    default: "home",
  },
});

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
    labels: ["Expire this month", "Next month", "Withing 3 months"],
  },
  series: [7, 4, 5],
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
