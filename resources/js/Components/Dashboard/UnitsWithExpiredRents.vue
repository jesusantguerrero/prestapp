<script setup lang="ts">
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import UnitDetailsCard from "./UnitDetailsCard.vue";

const { t } = useI18n();

interface Unit {
    id: number;
    name: string;
    property_name: string;
    last_tenant_name: string;
    current_tenant_name?: string;
    expired_rent_id: number;
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

const totalUnits = computed(() => props.units.length);

const isExpanded = ref(false);

const toggleExpanded = () => {
    isExpanded.value = !isExpanded.value;
};
</script>

<template>
    <div class="bg-white rounded-lg shadow-sm w-full">
        <div class="flex items-center justify-between p-4 cursor-pointer hover:bg-red-100 transition-colors" @click="toggleExpanded">
            <div>
                <h3 class="text-lg font-semibold text-red-600">{{ t('Units Requiring Action') }}</h3>
                <p class="text-sm text-gray-600">
                    {{ t('Units with expired or cancelled rents still marked as rented') }}
                </p>
            </div>
            <section class="flex items-center gap-2">
                <div class="text-2xl font-bold text-red-700">{{ totalUnits }}</div>
                <button class="text-gray-500 hover:text-gray-700 transition-colors">
                    <i class="fas" :class="isExpanded ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                </button>
            </section>
        </div>

        <section v-show="isExpanded" class="p-4">
            <div v-if="totalUnits > 0" class="space-y-2">
                <UnitDetailsCard v-for="unit in units" :key="unit.id" :unit="unit" />
            </div>

            <div v-else class="text-center py-4 text-gray-500">
                {{ t('No units require action at this time') }}
            </div>
        </section>
    </div>
</template>