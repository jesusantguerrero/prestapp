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
      <h4 class="font-bold md:text-lg text-xs">{{ title }}</h4>
      <ValueComponent class="md:text-4xl font-bold" :href="valueLink">
        {{ value }}
      </ValueComponent>
    </section>
    <footer class="md:flex md:space-x-4 mt-auto">
      <slot name="footer" />
    </footer>
  </article>
</template>
