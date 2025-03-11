<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { formatMoney } from "@/utils";

const { t } = useI18n();

interface Props {
  totalPrice: number;
  totalCommission: number;
  averagePrice: number;
  averageCommission: number;
}

defineProps<Props>();
</script>

<template>
  <div class="mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ t('Unit Details') }}</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('Total Price') }}</h3>
        <p class="text-lg font-bold text-slate-900">{{ formatMoney(totalPrice) }}</p>
      </div>
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('Total Commission') }}</h3>
        <p class="text-lg font-bold text-slate-900">{{ formatMoney(totalCommission) }}</p>
      </div>
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('Average Price') }}</h3>
        <p class="text-lg font-bold text-slate-900">{{ formatMoney(averagePrice) }}</p>
      </div>
      <div class="p-3 bg-slate-50 rounded">
        <h3 class="text-sm font-semibold text-slate-800">{{ t('Average Commission') }}</h3>
        <p class="text-lg font-bold text-slate-900">{{ formatMoney(averageCommission) }}</p>
      </div>
    </div>
  </div>
</template> 