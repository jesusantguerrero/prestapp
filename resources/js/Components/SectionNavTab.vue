<template>
  <button
    type="button"
    class="inline-flex items-center px-3 py-3 text-sm font-medium leading-4 transition border-b-2 border-transparent hover:bg-base-lvl-2 hover:text-body/80 focus:outline-none"
    :class="[...tabClasses, isSelected ? selectedClass : 'text-body']"
    v-bind="$attrs"
  >
    <slot name="icon">
      <i class="fa mr-2" :class="icon" v-if="icon" />
    </slot>
    <slot />
  </button>
</template>

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
  isSelected: {
    type: Boolean,
    default: false,
  },
  tabClass: {
    type: String,
    default: "",
  },
  selectedClass: {
    type: String,
    default: "border-b-2 border-primary text-primary",
  },
});
const tabClasses = computed(() => {
  const activeStateClass = props.keepActiveState;

  return [activeStateClass];
});
</script>
