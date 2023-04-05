<template>
  <AppButtonTab
    rounded
    type="button"
    class="relative transition rounded-full text-body-1/80 bg-base-lvl-1 hover:bg-primary/10 border-base-lvl-1 hover:border-primary"
  >
    <slot />
  </AppButtonTab>
</template>

<script lang="ts" setup>
import AppButtonTab from "./AppButtonTab.vue";
</script>
