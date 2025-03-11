<script setup lang="ts">
import { useI18n } from "vue-i18n";
import { formatMoney } from "@/utils/formatMoney";

const { t } = useI18n();

interface Props {
  date?: string;
  status?: string;
  amount?: number;
  debt?: number;
}

defineProps<Props>();
</script>

<template>
  <div class="flex flex-col space-y-1 border-t border-gray-100 pt-2">
    <h4 class="text-sm font-medium text-gray-700">{{ t('Last Invoice') }}</h4>
    <div v-if="date" class="grid grid-cols-2 gap-2 text-sm">
      <p class="text-gray-600">
        <span class="font-medium">{{ t('Due Date') }}:</span> {{ date }}
      </p>
      <p class="text-gray-600">
        <span class="font-medium">{{ t('Status') }}:</span>
        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-1"
              :class="{
                'bg-green-100 text-green-800': status === 'PAID',
                'bg-yellow-100 text-yellow-800': status === 'PARTIALLY_PAID',
                'bg-red-100 text-red-800': status === 'UNPAID'
              }">
          {{ status }}
        </span>
      </p>
      <p class="text-gray-600">
        <span class="font-medium">{{ t('Amount') }}:</span> {{ formatMoney(amount || 0) }}
      </p>
      <p class="text-gray-600">
        <span class="font-medium">{{ t('Debt') }}:</span> 
        <span :class="{'text-red-600': (debt || 0) > 0}">
          {{ formatMoney(debt || 0) }}
        </span>
      </p>
    </div>
    <p v-else class="text-sm text-gray-500 italic">{{ t('No invoices found') }}</p>
  </div>
</template> 