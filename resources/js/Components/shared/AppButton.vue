<template>
  <AtButton
    class="inline-block font-bold transition border rounded-md min-w-max"
    :class="[typeClasses]"
  >
    <slot />
  </AtButton>
</template>

<script setup lang="ts">
import { computed } from "vue";
// @ts-ignore
import { AtButton } from "atmosphere-ui";

const variants: Record<string, string> = {
  primary: "bg-primary text-white",
  secondary: "bg-secondary text-gray-600",
  error: "bg-error/80 text-white",
  inverse: "border-primary bg-primary/10 text-primary hover:bg-primary hover:text-white",
};

const props = defineProps({
  variant: {
    type: String,
    default: "primary",
  },
});

const typeClasses = computed(() => {
  return variants[props.variant] || variants.primary;
});
</script>
