<script setup lang="ts">
import { useI18n } from "vue-i18n";

const { t } = useI18n();

interface Props {
  byBedrooms: Record<string, number>;
  byBathrooms: Record<string, number>;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ t('Unit Distribution') }}</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('By Bedrooms') }}</h3>
        <div class="space-y-1">
          <div v-for="(count, beds) in byBedrooms" :key="beds" class="flex justify-between">
            <span>{{ beds }} {{ t('beds') }}</span>
            <span class="font-semibold">{{ count }}</span>
          </div>
        </div>
      </div>
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('By Bathrooms') }}</h3>
        <div class="space-y-1">
          <div v-for="(count, baths) in byBathrooms" :key="baths" class="flex justify-between">
            <span>{{ baths }} {{ t('baths') }}</span>
            <span class="font-semibold">{{ count }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 