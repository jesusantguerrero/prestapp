<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

interface Props {
  title: string;
  value: string | number;
  valueLink?: string;
}

const props = defineProps<Props>();

const ValueComponent = computed(() => {
  return props.valueLink ? Link : "h3";
});
</script>

<template>
  <article class="grid grid-rows-section-footer-card">
    <section class="my-auto">
      <h4 class="font-bold text-lg">{{ title }}</h4>
      <ValueComponent class="text-xl lg:text-3xl font-bold" :href="valueLink">
        {{ value }}
      </ValueComponent>
    </section>
    <footer class="flex space-x-4 mt-auto">
      <slot name="footer" />
    </footer>
  </article>
</template>
