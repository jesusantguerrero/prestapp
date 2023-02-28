<template>
  <div class="">
    <p class="font-bold">{{ label }}</p>
    <div>
      {{ client.fullName }}
      <span v-if="client.phone">- {{ client.phone || "000-000-0000" }}</span>
    </div>
    <div v-if="client.address || client.address_details">
      {{ client.address || client.address_details }}
    </div>
    <div v-if="client.email">{{ client.email }}</div>
  </div>
</template>

<script lang="ts" setup>
defineProps<{
  client: Record<string, string>;
  label?: string;
}>();
</script>
