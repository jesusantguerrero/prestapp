<script setup lang="ts">
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";

import { onMounted, ref, computed } from "vue";
import cols from "./cols";
import BaseTable from "@/Components/shared/BaseTable.vue";
import { getRangeParams } from "@/utils";
import InstallmentTable from "./Partials/InstallmentTable.vue";

const props = withDefaults(
  defineProps<{
    ranges: any[];
    title?: string;
    method?: string;
    endpoint: string;
    dateField: string;
    defaultRange?: string;
  }>(),
  {
    endpoint: "/api/repayments?filter[payment_status]=~paid&",
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
    dateField: "due_date",
    method: "forward",
    title: "Proximas coutas",
  }
);

const selectedRange = ref(props.defaultRange ?? "1D");

const isSelected = (label: string) => {
  return selectedRange.value == label;
};

const selectedRangeValue = computed(() => {
  return props.ranges.find((range) => range.label === selectedRange.value).value;
});

const payments = ref([]);

const fetchRepayments = () => {
  const rangeParams = getRangeParams(
    props.dateField,
    selectedRangeValue.value,
    props.method
  );
  axios.get(`${props.endpoint}${rangeParams}`).then(({ data }) => {
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
  <WelcomeWidget class="mt-4" :message="title">
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
      <slot name="content" :list="payments">
        <InstallmentTable :installments="payments" :hidden-cols="['balance']" />
      </slot>
    </template>
  </WelcomeWidget>
</template>
