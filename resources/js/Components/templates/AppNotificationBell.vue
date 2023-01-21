<template>
  <AppButtonCircle type="button">
    <IMdiBell class="text-md" />
    <div
      v-if="notifications > 0"
      class="absolute bottom-0 right-0 w-4 h-4 text-xs text-white bg-error rounded-full shadow-md"
    >
      {{ notifications }}
    </div>
  </AppButtonCircle>
</template>

<script setup>
import AppButtonCircle from "@/Components/shared/AppButtonCircle.vue";

defineProps({
  notifications: {
    type: Number,
    default() {
      return 0;
    },
  },
});
</script>
