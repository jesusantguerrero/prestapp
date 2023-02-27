<template>
  <div class="">
    <p class="font-bold">{{ label }}</p>
    <div>{{ business.business_name }}</div>
    <div v-if="businessAddress">{{ businessAddress }}</div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from "vue";

const props = defineProps<{
  business: Record<string, string>;
  label?: string;
}>();

const businessAddress = computed(() => {
  return `${props.business.business_street}, ${props.business.business_city}, ${props.business.business_state}, ${props.business.business_country}`;
});
</script>
