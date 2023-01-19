<script setup lang="ts">
// @ts-ignore
import { ref, computed } from "vue";
import { AtField } from "atmosphere-ui";
import { useElementBounding } from "@vueuse/core";

// @ts-ignore
import IconCoins from "@/Components/icons/IconCoins.vue";

import { formatMoney } from "@/utils";

interface Props {
  payment: number;
  totalInterest: number;
  totalDebt: number;
}

defineProps<Props>();

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
    class="rounded-md flex w-full justify-between bg-primary/5 border-primary/20 font-bold border px-4 relative"
  >
    <div
      class="h-8 w-8 rounded-full items-center flex justify-center cursor-pointer hover:bg-primary hover:text-white transition-colors text-primary bg-[#F6FBFE] border border-primary/20 absolute top-6 -left-4"
    >
      <IconCoins />
    </div>
    <AtField label="Monto Cuotas">
      <span class="text-primary">
        {{ formatMoney(payment) }}
      </span>
    </AtField>
    <AtField label="Interes a pagar">
      <span class="text-error">
        {{ formatMoney(totalInterest) }}
      </span>
    </AtField>
    <AtField label="Total a pagar">
      <span class="text-secondary">
        {{ formatMoney(totalDebt) }}
      </span>
    </AtField>
  </section>
</template>
