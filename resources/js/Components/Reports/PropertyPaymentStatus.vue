<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  paid: number;
  unpaid: number;
  available: number;
  rented: number;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ t('Payment Status') }}</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
      <div class="p-3 bg-green-50 rounded">
        <h3 class="text-sm font-semibold text-green-800">{{ t('Paid') }}</h3>
        <p class="text-lg font-bold text-green-900">{{ paid }}</p>
      </div>
      <div class="p-3 bg-red-50 rounded">
        <h3 class="text-sm font-semibold text-red-800">{{ t('Unpaid') }}</h3>
        <p class="text-lg font-bold text-red-900">{{ unpaid }}</p>
      </div>
      <div class="p-3 bg-yellow-50 rounded">
        <h3 class="text-sm font-semibold text-yellow-800">{{ t('Rented') }}</h3>
        <p class="text-lg font-bold text-yellow-900">{{ rented }}</p>
      </div>
      <div class="p-3 bg-blue-50 rounded">
        <h3 class="text-sm font-semibold text-blue-800">{{ t('Available') }}</h3>
        <p class="text-lg font-bold text-blue-900">{{ available }}</p>
      </div>
    </div>
  </div>
</template> 