<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import InvoiceDetails from "./InvoiceDetails.vue";
import AppButton from "../shared/AppButton.vue";

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
  unit: Unit;
}>();


const updateUnitStatusForm = useForm({
  status: props.unit.status == 'AVAILABLE' ? 'RENTED' : 'AVAILABLE',
})

const updateUnitStatus = () => {
  updateUnitStatusForm.put(`/properties/${props.unit.property_id}/units/${props.unit.id}/update-status`, {
    onSuccess() {
      router.reload();
    }
  })
}
</script>

<template>
  <div class="border border-red-200 rounded-lg overflow-hidden">
    <!-- Header - Always visible -->
    <div
         class="p-3 bg-red-50 flex justify-between items-center">
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
    </div>
    
    <!-- Expandable Details -->
    <div class="p-3 border-t border-red-200 bg-white">
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
            <span class="font-medium">{{ t('Current tenant') }}:</span>
            <span :class="{'text-red-600': !unit.current_tenant_name}">
              {{ unit.current_tenant_name || t('No current tenant') }}
            </span>
          </p>
          <p class="text-sm text-gray-600">
            <span class="font-medium">{{ t('Last tenant') }}:</span>
            {{ unit.last_tenant_name }}
          </p>
        </div>
        <InvoiceDetails
          :date="unit.last_invoice_date"
          :status="unit.last_invoice_status"
          :amount="unit.last_invoice_amount"
          :debt="unit.last_invoice_debt"
        />
        <p class="text-sm text-gray-600">
          <span class="font-medium">{{ t('Rent status') }}:</span>
          <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                :class="{
                  'bg-yellow-100 text-yellow-800': unit.rent_status === 'EXPIRED',
                  'bg-red-100 text-red-800': unit.rent_status === 'CANCELLED'
                }">
            {{ unit.rent_status }}
          </span>
        </p>
        <p class="text-sm text-gray-600">
          <span class="font-medium">{{ t('Ended') }}:</span> {{ unit.move_out_at || unit.end_date }}
        </p>
        <div class="flex justify-end mt-3">
          <AppButton
            @click="updateUnitStatus"
            :processing="updateUnitStatusForm.processing">
            {{ t('Update Status') }}
          </AppButton>
        </div>
      </div>
    </div>
  </div>
</template> 