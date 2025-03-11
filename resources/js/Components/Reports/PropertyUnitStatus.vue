<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  building: number;
  maintenance: number;
  rented: number;
  available: number;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ t('Unit Status') }}</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
      <div class="p-3 bg-purple-50 rounded">
        <h3 class="text-sm font-semibold text-purple-800">{{ t('Building') }}</h3>
        <p class="text-lg font-bold text-purple-900">{{ building }}</p>
      </div>
      <div class="p-3 bg-orange-50 rounded">
        <h3 class="text-sm font-semibold text-orange-800">{{ t('Maintenance') }}</h3>
        <p class="text-lg font-bold text-orange-900">{{ maintenance }}</p>
      </div>
      <div class="p-3 bg-indigo-50 rounded">
        <h3 class="text-sm font-semibold text-indigo-800">{{ t('Rented') }}</h3>
        <p class="text-lg font-bold text-indigo-900">{{ rented }}</p>
      </div>
      <div class="p-3 bg-teal-50 rounded">
        <h3 class="text-sm font-semibold text-teal-800">{{ t('Available') }}</h3>
        <p class="text-lg font-bold text-teal-900">{{ available }}</p>
      </div>
    </div>
  </div>
</template> 