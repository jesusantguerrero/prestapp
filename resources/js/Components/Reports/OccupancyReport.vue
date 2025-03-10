<script setup lang="ts">
import { defineProps, computed } from 'vue'
import { formatDate, formatMoney } from '@/utils'

interface Unit {
  id: number
  unit_name: string
  property_name: string
  address: string
  client_name: string | null
  current_status: string
  rent_status: string | null
  date: string | null
  end_date: string | null
  total_in_month: string
  invoice_month: string | null
}

interface Report {
  title: string
  description: string
  startDate: string
  endDate: string
  type: string
  content: {
    type: string
    data: Unit[]
  }
}

const props = defineProps<{
  report: Report
}>()

// Calculate summary statistics
const summaryStats = computed(() => {
  const units = props.report.content.data
  const totalUnits = units.length
  const rentedUnits = units.filter(u => u.current_status === 'RENTED').length
  const occupiedUnits = units.filter(u => ['RENTED', 'MAINTENANCE'].includes(u.current_status)).length
  const totalAmount = units.reduce((sum, unit) => sum + parseFloat(unit.total_in_month || '0'), 0)
  const occupancyRate = totalUnits ? (rentedUnits / totalUnits * 100).toFixed(1) : '0'

  return {
    totalUnits,
    rentedUnits,
    occupiedUnits,
    totalAmount,
    occupancyRate
  }
})

const getStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'RENTED': 'bg-green-100 text-green-800',
    'AVAILABLE': 'bg-blue-100 text-blue-800',
    'MAINTENANCE': 'bg-yellow-100 text-yellow-800',
    'BUILDING': 'bg-purple-100 text-purple-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getRentStatusClass = (status: string) => {
  const classes: Record<string, string> = {
    'ACTIVE': 'bg-green-100 text-green-800',
    'PENDING': 'bg-yellow-100 text-yellow-800',
    'EXPIRED': 'bg-red-100 text-red-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getInvoiceStatusClass = (status: string | null) => {
  const classes: Record<string, string> = {
    'paid': 'bg-green-100 text-green-800',
    'pending': 'bg-yellow-100 text-yellow-800',
    'overdue': 'bg-red-100 text-red-800',
    'none': 'bg-gray-100 text-gray-500'
  }
  return classes[status || 'none'] || 'bg-gray-100 text-gray-500'
}

const getInvoiceStatus = (invoice_month: string | null) => {
  if (!invoice_month) return 'none'
  const today = new Date()
  const invoiceDate = new Date(invoice_month)
  
  if (invoiceDate > today) return 'pending'
  if (invoiceDate < today) return 'overdue'
  return 'pending'
}

const formatDateSafe = (date: string | null) => {
  if (!date) return '-'
  return formatDate(date)
}

const formatNumberSafe = (value: number | string | null | undefined): string => {
  if (value === null || value === undefined || isNaN(Number(value))) return '0'
  return value.toString()
}

const formatPercentage = (value: number | string | null | undefined): string => {
  if (value === null || value === undefined || isNaN(Number(value))) return '0.0'
  return Number(value).toFixed(1)
}
</script>

<template>
  <div class="occupancy-report bg-white rounded-lg shadow-sm p-6">
    <div class="report-header mb-6">
      <h2 class="text-2xl font-bold text-gray-800">{{ report.title }}</h2>
      <p class="text-gray-600 mt-1">{{ report.description }}</p>
      <div class="text-sm text-gray-500 mt-2">
        Periodo: {{ formatDateSafe(report.startDate) }} - {{ formatDateSafe(report.endDate) }}
      </div>
    </div>

    <!-- Summary Statistics -->
    <div class="summary-stats mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Units & Occupancy -->
        <div class="stat-card bg-blue-50 rounded-lg p-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-medium text-blue-600">Unidades Totales</h3>
              <p class="mt-0.5 text-xl font-semibold text-blue-900">{{ summaryStats.totalUnits }}</p>
            </div>
            <div class="text-blue-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Rented Units -->
        <div class="stat-card bg-green-50 rounded-lg p-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-medium text-green-600">Unidades Rentadas</h3>
              <p class="mt-0.5 text-xl font-semibold text-green-900">{{ summaryStats.rentedUnits }}</p>
            </div>
            <div class="text-green-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Occupancy Rate -->
        <div class="stat-card bg-purple-50 rounded-lg p-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-medium text-purple-600">Tasa de Ocupación</h3>
              <p class="mt-0.5 text-xl font-semibold text-purple-900">{{ summaryStats.occupancyRate }}%</p>
            </div>
            <div class="text-purple-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
            </div>
          </div>
        </div>

        <!-- Total Amount -->
        <div class="stat-card bg-yellow-50 rounded-lg p-3">
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-xs font-medium text-yellow-600">Total Recaudado</h3>
              <p class="mt-0.5 text-xl font-semibold text-yellow-900">{{ formatMoney(summaryStats.totalAmount) }}</p>
            </div>
            <div class="text-yellow-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Status Distribution Bar -->
      <div class="mt-6 p-4 bg-gray-50 rounded-lg">
        <h3 class="text-sm font-medium text-gray-700 mb-2">Distribución de Estados</h3>
        <div class="relative h-4 rounded-full overflow-hidden bg-gray-200">
          <div class="absolute top-0 left-0 h-full bg-green-500" 
               :style="{ width: formatPercentage(summaryStats.occupancyRate) + '%' }"
               :title="`${formatNumberSafe(summaryStats.rentedUnits)} unidades rentadas (${formatPercentage(summaryStats.occupancyRate)}%)`">
          </div>
          <div class="absolute top-0 h-full bg-yellow-500" 
               :style="{ 
                 left: formatPercentage(summaryStats.occupancyRate) + '%',
                 width: formatPercentage((summaryStats.occupiedUnits - summaryStats.rentedUnits) / summaryStats.totalUnits * 100) + '%'
               }"
               :title="`${formatNumberSafe(summaryStats.occupiedUnits - summaryStats.rentedUnits)} unidades en mantenimiento`">
          </div>
        </div>
        <div class="flex justify-between mt-2 text-xs text-gray-500">
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-green-500 mr-1"></div>
            <span>Rentadas ({{ formatPercentage(summaryStats.occupancyRate) }}%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-yellow-500 mr-1"></div>
            <span>Mantenimiento ({{ formatPercentage((summaryStats.occupiedUnits - summaryStats.rentedUnits) / summaryStats.totalUnits * 100) }}%)</span>
          </div>
          <div class="flex items-center">
            <div class="w-3 h-3 rounded-full bg-gray-200 mr-1"></div>
            <span>Disponible ({{ formatPercentage(100 - (summaryStats.occupiedUnits / summaryStats.totalUnits * 100)) }}%)</span>
          </div>
        </div>
      </div>
    </div>

    <div class="report-content">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Información de Unidad
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Estado
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Periodo
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Facturación
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="unit in report.content.data" :key="unit.id">
              <td class="px-6 py-4">
                <div class="flex flex-col space-y-2">
                  <div>
                    <div class="flex items-center space-x-2">
                      <div class="text-base font-semibold text-gray-900">{{ unit.unit_name || '-' }}</div>
                      <div class="text-sm text-gray-500">({{ unit.property_name || '-' }})</div>
                    </div>
                    <div class="text-sm text-gray-500 mt-0.5 flex items-center">
                      <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      {{ unit.address || '-' }}
                    </div>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-sm text-gray-600">
                      {{ unit.client_name || 'Sin inquilino' }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(unit.current_status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ $t(unit.current_status || 'NONE') }}
                </span>
                <span v-if="unit.rent_status" :class="getRentStatusClass(unit.rent_status)" class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ $t(unit.rent_status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <template v-if="unit.date">
                  {{ formatDateSafe(unit.date) }} - {{ formatDateSafe(unit.end_date) }}
                </template>
                <template v-else>
                  -
                </template>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-col">
                  <div class="flex items-center">
                    <span :class="getInvoiceStatusClass(getInvoiceStatus(unit.invoice_month))" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                      {{ $t(getInvoiceStatus(unit.invoice_month)) }}
                    </span>
                    <span class="ml-2 text-sm font-medium text-gray-900">{{ formatMoney(unit.total_in_month || 0) }}</span>
                  </div>
                  <div v-if="unit.invoice_month" class="text-xs text-gray-400 mt-1">
                    {{ formatDateSafe(unit.invoice_month) }}
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Print styles */
@media print {
  .occupancy-report {
    break-inside: avoid;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    background-color: white !important;
    box-shadow: none !important;
  }
  
  /* Force flex layout in print */
  .grid.grid-cols-1.md\:grid-cols-4 {
    display: flex !important;
    flex-direction: row !important;
    gap: 1rem !important;
    width: 100% !important;
  }

  .stat-card {
    break-inside: avoid;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    background-color: inherit !important;
    box-shadow: none !important;
    flex: 1 1 25% !important;
    min-width: 0 !important; /* Prevent flex items from overflowing */
  }

  /* Ensure text doesn't overflow in flex items */
  .stat-card > div {
    width: 100% !important;
  }

  .stat-card h3 {
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
  }

  /* Preserve background colors */
  .bg-blue-50 { background-color: rgb(239 246 255) !important; }
  .bg-green-50 { background-color: rgb(240 253 244) !important; }
  .bg-purple-50 { background-color: rgb(250 245 255) !important; }
  .bg-yellow-50 { background-color: rgb(254 252 232) !important; }
  .bg-gray-50 { background-color: rgb(249 250 251) !important; }

  /* Preserve text colors */
  .text-blue-600 { color: rgb(37 99 235) !important; }
  .text-green-600 { color: rgb(22 163 74) !important; }
  .text-purple-600 { color: rgb(147 51 234) !important; }
  .text-yellow-600 { color: rgb(202 138 4) !important; }
  .text-gray-600 { color: rgb(75 85 99) !important; }
  .text-gray-500 { color: rgb(107 114 128) !important; }
  .text-gray-400 { color: rgb(156 163 175) !important; }

  /* Preserve status badge colors */
  .bg-green-100 { background-color: rgb(220 252 231) !important; }
  .bg-blue-100 { background-color: rgb(219 234 254) !important; }
  .bg-yellow-100 { background-color: rgb(254 249 195) !important; }
  .bg-purple-100 { background-color: rgb(243 232 255) !important; }
  .bg-red-100 { background-color: rgb(254 226 226) !important; }
  .bg-gray-100 { background-color: rgb(243 244 246) !important; }

  .text-green-800 { color: rgb(22 101 52) !important; }
  .text-blue-800 { color: rgb(30 64 175) !important; }
  .text-yellow-800 { color: rgb(133 77 14) !important; }
  .text-purple-800 { color: rgb(107 33 168) !important; }
  .text-red-800 { color: rgb(153 27 27) !important; }
  .text-gray-800 { color: rgb(31 41 55) !important; }

  /* Preserve progress bar colors */
  .bg-green-500 { background-color: rgb(34 197 94) !important; }
  .bg-yellow-500 { background-color: rgb(234 179 8) !important; }
  .bg-gray-200 { background-color: rgb(229 231 235) !important; }

  /* Preserve table styles */
  table {
    border-collapse: collapse !important;
  }
  
  th, td {
    border-color: rgb(229 231 235) !important;
  }

  /* Remove hover effects in print */
  .stat-card:hover {
    transform: none !important;
    box-shadow: none !important;
  }
}

/* Screen styles */
.stat-card {
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}
</style> 