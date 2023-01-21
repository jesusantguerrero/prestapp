<template>
  <AppButtonTab
    rounded
    type="button"
    class="relative text-body-1/80 bg-base-lvl-1 hover:bg-primary/10 transition border-base-lvl-1 hover:border-primary rounded-full"
  >
    <slot />
  </AppButtonTab>
</template>

<script setup>
import AppButtonTab from "./AppButtonTab.vue";
</script>
