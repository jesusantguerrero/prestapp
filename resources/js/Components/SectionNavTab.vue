<script setup lang="ts">
import { computed } from "vue";

const props = defineProps({
  keepActiveState: {
    type: Boolean,
    default: true,
  },
  icon: {
    type: String,
  },
  value: {
    type: String,
  },
  currentValue: {
    type: String,
  },
  label: {
    type: String,
  },
  isActiveFunction: {
    type: Function,
  },
  tabClass: {
    type: String,
    default: "",
  },
  selectedClass: {
    type: String,
    default: "border-primary text-primary",
  },
});

const isActive = computed(() => {
  const regex = new RegExp(props.value ?? "");
  const currentPath = window.location.pathname;

  if (props.value?.startsWith("/")) {
    return props.isActiveFunction
      ? props.isActiveFunction(currentPath)
      : regex.test(currentPath);
  }
  return props.value == props.currentValue;
});
</script>

<template>
  <button
    type="button"
    class="inline-flex items-center px-3 py-3 text-sm leading-4 transition border-b-2 hover:bg-base-lvl-2 hover:text-body/80 focus:outline-none active:outline-none"
    :class="[isActive ? selectedClass : 'text-body font-medium border-transparent']"
    v-bind="$attrs"
  >
    <slot name="icon">
      <i class="fa mr-2" :class="icon" v-if="icon" />
    </slot>
    <slot> {{ label }}</slot>
  </button>
</template>
