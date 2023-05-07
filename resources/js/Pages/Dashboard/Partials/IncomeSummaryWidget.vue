<script lang="ts" setup>
import { formatMoney } from "@/utils";
import { computed } from "vue";

interface Props {
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

const props = withDefaults(defineProps<Props>(), {
  sectionTotalField: "total",
  type: "line",
});

const options = computed(() => {
  return {
    ...props.chart.options,
    ...(props.labels?.length
      ? {
          xaxis: {
            categories: props.labels,
            position: "bottom",
            axisBorder: {
              show: false,
            },
            axisTicks: {
              show: false,
            },
          },
          yaxis: {
            labels: {
              show: true,
              formatter(val: number) {
                return formatMoney(val);
              },
            },
          },
          plotOptions: {
            bar: {
              dataLabels: {
                position: "top", // top, center, bottom
              },
            },
          },
          dataLabels: {
            enabled: false,
            formatter: function (val: number) {
              return val ? formatMoney(val) : 0;
            },
            offsetY: -20,
            style: {
              fontSize: "12px",
              colors: ["#304758"],
            },
          },
        }
      : {}),
  };
});
</script>

<template>
  <div
    class="flex flex-col h-full overflow-hidden bg-white divide-x-2 lg:flex-row sm:rounded-lg"
  >
    <article class="p-5 h-full" :class="[sections ? 'lg:w-9/12' : 'w-full']">
      <header>
        <h4 class="font-bold">{{ title }}</h4>
        <small>{{ description }}</small>
      </header>
      <div class="w-full h-full mx-auto pb-10">
        <apexchart
          ref="apexchart"
          width="100%"
          height="100%"
          :type="type"
          class="mx-auto"
          :options="options"
          :series="chart.series"
        />
      </div>
    </article>
    <article class="p-5 lg:w-3/12" v-if="sections">
      <header>
        <h4 class="font-bold">Reportes</h4>
        <small>Avg. de mes</small>
      </header>
      <section class="mt-4 space-y-2">
        <div v-for="section in sections" class="p-2 bg-gray-100 rounded-md">
          <h4 class="text-xs">{{ section.alias ?? section.display_id }}</h4>
          <p class="font-bold">{{ formatMoney(section[sectionTotalField]) }}</p>
        </div>
      </section>
    </article>
  </div>
</template>
