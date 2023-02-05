<script setup lang="ts">
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";

import { onMounted, ref, computed } from "vue";
import cols from "./cols";
import BaseTable from "@/Components/shared/BaseTable.vue";
import { getRangeParams } from "@/utils";

const props = withDefaults(
  defineProps<{
    ranges: any[];
  }>(),
  {
    ranges: [
      {
        label: "1D",
        value: [0, 1],
      },
      {
        label: "7D",
        value: [0, 7],
      },
      {
        label: "30D",
        value: [0, 30],
      },
      {
        label: "90D",
        value: [0, 90],
      },
    ],
  }
);

const selectedRange = ref("1D");

const isSelected = (label: string) => {
  return selectedRange.value == label;
};

const selectedRangeValue = computed(() => {
  return props.ranges.find((range) => range.label === selectedRange.value).value;
});

const payments = ref([]);

const fetchRepayments = () => {
  const rangeParams = getRangeParams("due_date", selectedRangeValue.value, "forward");
  axios.get(`/api/repayments?filter[status]=$paid&${rangeParams}`).then(({ data }) => {
    payments.value = data.data;
  });
};

const onRangeChanged = (rangeLabel: string) => {
  selectedRange.value = rangeLabel;
  fetchRepayments();
};

onMounted(() => {
  fetchRepayments();
});
</script>

<template>
  <WelcomeWidget class="mt-4" message="Proximas cuotas">
    <template #actions>
      <section class="flex text-xs space-x-2 text-body-1">
        <span
          v-for="option in ranges"
          role="button"
          class="rounded-3xl bg-base-lvl-2 py-1 px-4"
          @click="onRangeChanged(option.label)"
          :class="isSelected(option.label) && 'text-primary font-bold bg-primary/10'"
        >
          {{ option.label }}</span
        >
      </section>
    </template>
    <template #content>
      <section>
        <BaseTable :cols="cols" :table-data="payments" />
      </section>
    </template>
  </WelcomeWidget>
</template>
