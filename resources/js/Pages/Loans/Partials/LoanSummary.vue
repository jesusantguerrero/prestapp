<script setup lang="ts">
// @ts-ignore
import { ref, computed } from "vue";
import { AtField } from "atmosphere-ui";
import { useElementBounding } from "@vueuse/core";

// @ts-ignore
import IconCoins from "@/Components/icons/IconCoins.vue";

import { formatMoney } from "@/utils";

interface Props {
  cards: {
    label: string;
    value: number | string;
  }[];
  payment: number;
  totalInterest: number;
  totalDebt: number;
}

withDefaults(defineProps<Props>(), {
  payment: 0,
  totalDebt: 0,
  totalInterest: 0,
});

const containerRef = ref();
const { width } = useElementBounding(containerRef);

const classes = computed(() => {
  return width.value < 397 ? "flex-col space-y-2" : "";
});
</script>

<template>
  <section
    ref="containerRef"
    :class="classes"
    class="relative flex justify-between w-full px-4 font-bold border rounded-md bg-primary/5 border-primary/20"
  >
    <div
      class="h-8 w-8 rounded-full items-center flex justify-center cursor-pointer hover:bg-primary hover:text-white transition-colors text-primary bg-[#F6FBFE] border border-primary/20 absolute top-6 -left-4"
    >
      <IconCoins />
    </div>
    <AtField :label="card.label" v-for="(card, index) in cards" :key="index">
      <span class="text-primary">
        {{ card.value }}
      </span>
    </AtField>
  </section>
</template>
