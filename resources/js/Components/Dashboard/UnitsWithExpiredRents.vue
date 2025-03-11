<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import { formatMoney } from "@/utils/formatMoney";

const { t } = useI18n();

interface Unit {
  id: number;
  name: string;
  property_name: string;
  client_name: string;
  current_tenant?: string;
  rent_status: string;
  move_out_at: string;
  end_date: string;
  status: string;
  last_invoice_date?: string;
  last_invoice_amount?: number;
  last_invoice_debt?: number;
  last_invoice_status?: string;
}

const props = defineProps<{
  units: Unit[];
}>();

const expandedUnits = ref<number[]>([]);
const totalUnits = computed(() => props.units.length);

const toggleUnit = (unitId: number) => {
  const index = expandedUnits.value.indexOf(unitId);
  if (index === -1) {
    expandedUnits.value.push(unitId);
  } else {
    expandedUnits.value.splice(index, 1);
  }
};

const isExpanded = (unitId: number) => expandedUnits.value.includes(unitId);
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm p-4 w-full">
    <div class="flex items-center justify-between mb-4 cursor-pointer">
      <div>
        <h3 class="text-lg font-semibold text-red-600">{{ t('Units Requiring Action') }}</h3>
        <p class="text-sm text-gray-600">
          {{ t('Units with expired or cancelled rents still marked as rented') }}
        </p>
      </div>
      <div class="text-2xl font-bold text-red-700">{{ totalUnits }}</div>
    </div>

    <div v-if="totalUnits > 0" class="space-y-2">
      <div v-for="unit in units" :key="unit.id" 
           class="border border-red-200 rounded-lg overflow-hidden">
        <!-- Header - Always visible -->
        <div @click="toggleUnit(unit.id)"
             class="p-3 bg-red-50 flex justify-between items-center cursor-pointer hover:bg-red-100 transition-colors">
          <div class="flex items-center space-x-3">
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-yellow-100 text-yellow-800': unit.rent_status === 'EXPIRED',
                    'bg-red-100 text-red-800': unit.rent_status === 'CANCELLED'
                  }">
              {{ unit.rent_status }}
            </span>
            <span class="font-semibold text-gray-900">{{ unit.name }}</span>
          </div>
          <button class="text-gray-500 hover:text-gray-700 transition-colors">
            <i class="fas" :class="isExpanded(unit.id) ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
          </button>
        </div>
        
        <!-- Expandable Details -->
        <div v-show="isExpanded(unit.id)" 
             class="p-3 border-t border-red-200 bg-white">
          <div class="space-y-2">
            <p class="text-sm text-gray-600">
              <span class="font-medium">{{ t('Property') }}:</span> {{ unit.property_name }}
            </p>
            <p class="text-sm text-gray-600">
              <span class="font-medium">{{ t('Unit status') }}:</span>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                      'bg-green-100 text-green-800': unit.status === 'AVAILABLE',
                      'bg-blue-100 text-blue-800': unit.status === 'RENTED',
                      'bg-yellow-100 text-yellow-800': unit.status === 'MAINTENANCE',
                      'bg-gray-100 text-gray-800': unit.status === 'BUILDING'
                    }">
                {{ unit.status }}
              </span>
            </p>
            <div class="flex flex-col space-y-1">
              <p class="text-sm text-gray-600">
                <span class="font-medium">{{ t('Last tenant') }}:</span>
                {{ unit.client_name }} {{ unit.client_id }}
              </p>
            </div>
            <div class="flex flex-col space-y-1 border-t border-gray-100 pt-2">
              <h4 class="text-sm font-medium text-gray-700">{{ t('Last Invoice') }}</h4>
              <div v-if="unit.last_invoice_date" class="grid grid-cols-2 gap-2 text-sm">
                <p class="text-gray-600">
                  <span class="font-medium">{{ t('Due Date') }}:</span> {{ unit.last_invoice_date }}
                </p>
                <p class="text-gray-600">
                  <span class="font-medium">{{ t('Status') }}:</span>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ml-1"
                        :class="{
                          'bg-green-100 text-green-800': unit.last_invoice_status === 'PAID',
                          'bg-yellow-100 text-yellow-800': unit.last_invoice_status === 'PARTIALLY_PAID',
                          'bg-red-100 text-red-800': unit.last_invoice_status === 'UNPAID'
                        }">
                    {{ unit.last_invoice_status }}
                  </span>
                </p>
                <p class="text-gray-600">
                  <span class="font-medium">{{ t('Amount') }}:</span> {{ formatMoney(unit.last_invoice_amount) }}
                </p>
                <p class="text-gray-600">
                  <span class="font-medium">{{ t('Debt') }}:</span> 
                  <span :class="{'text-red-600': unit.last_invoice_debt > 0}">
                    {{ formatMoney(unit.last_invoice_debt) }}
                  </span>
                </p>
              </div>
              <p v-else class="text-sm text-gray-500 italic">{{ t('No invoices found') }}</p>
            </div>
            <p class="text-sm text-gray-600">
              <span class="font-medium">{{ t('Rent status') }}:</span>
              <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                    :class="{
                      'bg-yellow-100 text-yellow-800': unit.rent_status === 'EXPIRED',
                      'bg-red-100 text-red-800': unit.rent_status === 'CANCELLED'
                    }">
                {{ unit.rent_status }} {{ unit.rent_id }}
              </span>
            </p>
            <p class="text-sm text-gray-600">
              <span class="font-medium">{{ t('Ended') }}:</span> {{ unit.move_out_at || unit.end_date }}
            </p>
            <div class="flex justify-end mt-3">
              <Link :href="`/properties/units/${unit.id}/edit`"
                    class="inline-flex items-center px-3 py-1 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary-dark">
                {{ t('Update Status') }}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-4 text-gray-500">
      {{ t('No units require action at this time') }}
    </div>
  </div>
</template>