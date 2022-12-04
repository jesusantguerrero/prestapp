<script setup>
import ChartComparison from './ChartComparison.vue';

defineProps({
    chart: Object,
    headerInfo: Object
})
</script>

<template>
  <div
    class="flex flex-col overflow-hidden bg-white border divide-x-2 rounded-md shadow-xl lg:flex-row sm:rounded-lg"
  >
    <article class="p-5 lg:w-9/12">
      <header>
        <h4 class="font-bold">Total Income</h4>
        <small>Yearly report income</small>
        <div class="w-full mx-auto" height="auto">
            <apexchart
                ref="apexchart"
                width="100%"
                height="100%"
                type="line"
                class="mx-auto"
                :options="chart.options"
                :series="chart.series"
              />
        </div>
        <!-- <ChartComparison
            comparison-title="Revenue"
            ref="ComparisonRevenue"
            :chart="chart"
            :headerInfo="headerInfo"
            :actionInfo="''"
        /> -->
      </header>
    </article>
    <article class="p-5 lg:w-3/12">
      <header>
        <h4 class="font-bold">Report</h4>
        <small>Monthly Avg.</small>
      </header>
      <section class="mt-4 space-y-2">
        <div v-for="items in [1, 2, 3]" class="p-2 bg-gray-100 rounded-md">
          <h4>Income</h4>
          <p class="font-bold">42,845.00</p>
        </div>
      </section>
    </article>
  </div>
</template>
