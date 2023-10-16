<template>
  <div class="client-card">
    <p class="font-bold capitalize">{{ label }}</p>
    <section class="text-body min-w-fit">
      <div class="whitespace-nowrap">
        {{ client.fullName }}
        <span v-if="client.phone">- {{ client.phone || "000-000-0000" }}</span>
      </div>
      <div v-if="client.address || client.address_details">
        {{ client.address || client.address_details }}
      </div>
      <div v-if="client.email">{{ client.email }}</div>
    </section>
  </div>
</template>

<script lang="ts" setup>
defineProps<{
  client: Record<string, string>;
  label?: string;
}>();
</script>
