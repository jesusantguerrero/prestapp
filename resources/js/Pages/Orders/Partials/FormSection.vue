<script setup lang="ts">
interface Props {
  title?: string;
  sectionClass?: string;
}

withDefaults(defineProps<Props>(), {
  sectionClass: "grid grid-cols-2 gap-2",
});
</script>

<template>
  <section>
    <h1 class="font-bold text-primary">{{ title }}</h1>
    <section :class="sectionClass" class="-mt-4">
      <slot />
    </section>
  </section>
</template>

