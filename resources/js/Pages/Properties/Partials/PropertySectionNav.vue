<script lang="ts" setup>
import { getSectionMenu, MODULES } from "@/Modules/_app";
import { computed } from "vue";
import SectionNav from "@/Components/SectionNav.vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const menu = computed(() => {
  return getSectionMenu(MODULES.PROPERTY, t);
});
</script>

<template>
  <SectionNav :sections="menu">
    <template #actions>
      <slot name="actions" />
    </template>
  </SectionNav>
</template>
