<script lang="ts" setup>
import JetDropdown from "@/Components/Dropdown.vue";
import FastAccessOptions from "@/Pages/Dashboard/Partials/FastAccessOptions.vue";
</script>

<template>
  <JetDropdown align="right" width="500" v-model:open="isOpen">
    <template #trigger>
      <slot> </slot>
    </template>

    <template #content>
      <FastAccessOptions :extended="true" class="px-4" />
    </template>
  </JetDropdown>
</template>
