<template>
  <div class="property-report">
    <div class="property-header mb-6">
      <h3 class="text-xl font-bold">{{ property?.name }} - Property Report</h3>
      <p class="text-gray-600">{{ property?.address }}</p>
    </div>

    <!-- Total Statistics -->
    <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-4">
      <div class="p-4 bg-green-100 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800">Total Units</h3>
        <p class="text-2xl font-bold text-green-900">{{ property?.total_units || 0 }}</p>
      </div>

      <div class="p-4 bg-green-100 rounded-lg">
        <h3 class="text-lg font-semibold text-green-800">Paid Units</h3>
        <p class="text-2xl font-bold text-green-900">{{ property?.paid_units || 0 }}</p>
      </div>

      <div class="p-4 bg-red-100 rounded-lg">
        <h3 class="text-lg font-semibold text-red-800">Unpaid Units</h3>
        <p class="text-2xl font-bold text-red-900">{{ property?.unpaid_units || 0 }}</p>
      </div>

      <div class="p-4 bg-blue-100 rounded-lg">
        <h3 class="text-lg font-semibold text-blue-800">Available Units</h3>
        <p class="text-2xl font-bold text-blue-900">{{ property?.available_units || 0 }}</p>
      </div>
    </div>

    <!-- Unit Status -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Unit Status</h3>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="p-4 bg-purple-50 rounded-lg">
          <h3 class="text-sm font-semibold text-purple-800">Building</h3>
          <p class="text-xl font-bold text-purple-900">{{ property?.building_units || 0 }}</p>
        </div>
        <div class="p-4 bg-orange-50 rounded-lg">
          <h3 class="text-sm font-semibold text-orange-800">Maintenance</h3>
          <p class="text-xl font-bold text-orange-900">{{ property?.maintenance_units || 0 }}</p>
        </div>
        <div class="p-4 bg-indigo-50 rounded-lg">
          <h3 class="text-sm font-semibold text-indigo-800">Rented</h3>
          <p class="text-xl font-bold text-indigo-900">{{ property?.rented_units || 0 }}</p>
        </div>
        <div class="p-4 bg-teal-50 rounded-lg">
          <h3 class="text-sm font-semibold text-teal-800">Available</h3>
          <p class="text-xl font-bold text-teal-900">{{ property?.available_units || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Financial Summary -->
    <div class="mb-8">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Financial Summary</h3>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="p-4 bg-slate-50 rounded-lg">
          <h3 class="text-sm font-semibold text-slate-800">Total Collected</h3>
          <p class="text-xl font-bold text-slate-900">{{ formatMoney(property?.total_collected || 0) }}</p>
        </div>
        <div class="p-4 bg-slate-50 rounded-lg">
          <h3 class="text-sm font-semibold text-slate-800">Outstanding Balance</h3>
          <p class="text-xl font-bold text-slate-900">{{ formatMoney(property?.outstanding_balance || 0) }}</p>
        </div>
        <div class="p-4 bg-slate-50 rounded-lg">
          <h3 class="text-sm font-semibold text-slate-800">Total Expected</h3>
          <p class="text-xl font-bold text-slate-900">{{ formatMoney(property?.total_expected || 0) }}</p>
        </div>
      </div>
    </div>

    <!-- Current Unit Details -->
    <div class="mb-8" v-if="unit">
      <h3 class="text-lg font-semibold text-gray-700 mb-4">Current Unit Details</h3>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="p-4 bg-white border rounded-lg">
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Unit Number:</span>
              <span class="font-medium">{{ unit?.name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span class="font-medium">{{ unit?.status }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Monthly Rent:</span>
              <span class="font-medium">{{ formatMoney(rent?.amount) }}</span>
            </div>
          </div>
        </div>
        <div class="p-4 bg-white border rounded-lg">
          <div class="space-y-3">
            <div class="flex justify-between">
              <span class="text-gray-600">Start Date:</span>
              <span class="font-medium">{{ formatDate(rent?.date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">End Date:</span>
              <span class="font-medium">{{ formatDate(rent?.end_date) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Payment Status:</span>
              <span class="font-medium">{{ rent?.payment_status || 'N/A' }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Metrics -->
    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
      <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
        <div>
          <h4 class="text-sm text-gray-600">Occupancy Rate</h4>
          <p class="text-lg font-semibold">{{ property?.occupancy_rate?.toFixed(1) || 0 }}%</p>
        </div>
        <div>
          <h4 class="text-sm text-gray-600">Collection Rate</h4>
          <p class="text-lg font-semibold">{{ property?.collection_rate?.toFixed(1) || 0 }}%</p>
        </div>
        <div>
          <h4 class="text-sm text-gray-600">Maintenance Rate</h4>
          <p class="text-lg font-semibold">{{ property?.maintenance_rate?.toFixed(1) || 0 }}%</p>
        </div>
        <div>
          <h4 class="text-sm text-gray-600">Revenue Rate</h4>
          <p class="text-lg font-semibold">{{ formatMoney(property?.revenue_rate || 0) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'
import { formatMoney } from '@/utils/formatMoney'
import {  formatDate } from '@/utils'

const props = defineProps({
  property: {
    type: Object,
    required: true
  },
  unit: {
    type: Object,
    required: true
  },
  rent: {
    type: Object,
    required: true
  }
})
</script>

<style scoped>
.property-report {
  @apply p-6 bg-white rounded-lg shadow print:shadow-none;
}

@media print {
  .property-report {
    page-break-inside: avoid;
  }
}
</style> 