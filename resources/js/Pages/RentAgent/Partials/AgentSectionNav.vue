<script lang="ts" setup>
import { getSectionMenu, MODULES } from "@/Modules/_app";
import { computed } from "vue";
import SectionNav from "@/Components/SectionNav.vue";

const menu = computed(() => {
  return getSectionMenu(MODULES.AGENT);
});
</script>

<template>
  <SectionNav :sections="menu">
    <template #actions>
      <slot name="actions" />
    </template>
  </SectionNav>
</template>
