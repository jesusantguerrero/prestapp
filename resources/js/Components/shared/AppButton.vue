<template>
  <AtButton
    class="inline-block font-bold items-center flex transition border rounded-md min-w-max"
    :class="[typeClasses]"
  >
    <component :is="icon" v-if="icon" />
    <slot v-if="!processing" />
    <span v-else>
      <IEosIconsThreeDotsLoading />
    </span>
  </AtButton>
</template>

<script setup lang="ts">
import { computed } from "vue";
// @ts-ignore
import { AtButton } from "atmosphere-ui";

const variants: Record<string, string> = {
  primary: "bg-primary text-white",
  secondary: "bg-secondary border-secondary text-white",
  success: "bg-success text-white",
  error: "bg-error/80 text-white",
  neutral: "bg-base-lvl-2 text-body-1 border-transparent",
  inverse: "border-primary bg-primary/10 text-primary hover:bg-primary hover:text-white",
  "inverse-secondary":
    "border-secondary bg-secondary/10 text-secondary hover:bg-secondary hover:text-white",
};

const props = defineProps({
  variant: {
    type: String,
    default: "primary",
  },
  icon: {
    type: Object,
  },
  processing: {
    type: Boolean,
  },
});

const typeClasses = computed(() => {
  return variants[props.variant] || variants.primary;
});
</script>
