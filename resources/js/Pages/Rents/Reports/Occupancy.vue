<script lang="ts" setup>
import { reactive, watch, ref, computed } from "vue";
// @ts-ignore
import { AtDatePager } from "atmosphere-ui";
import { router } from "@inertiajs/vue3";
import { toRefs } from "@vueuse/shared";

import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";

import { IInvoice } from "@/Modules/invoicing/entities";
import { useServerSearch } from "@/utils/useServerSearch";
import { useResponsive } from "@/utils/useResponsive";
import { format } from "date-fns";
import { es } from "date-fns/locale";
import { formatMoney } from "@/utils";

const props = defineProps({
  invoices: {
    type: Object,
  },
  type: {
    type: String,
  },
  outstanding: {
    type: Number,
  },
  paid: {
    type: Number,
  },
  total: {
    type: Number,
  },
  lateDays: {
    type: Number,
  },
  properties: {
    type: Array,
    default() {
      return [];
    },
  },
  owners: {
    type: Array,
    default() {
      return [];
    },
  },
  businessData: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
  },
  section: {
    type: String,
  },
  serverSearchOptions: Object,
});

interface IFilter {
  [key: string]: null | string | Record<string, string>;
}

const filters = reactive<IFilter>({
  owner: null,
  property: null,
  section: props.section,
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc: Record<string, string | undefined>, [filterName, filter]) => {
        acc[filterName] = filter?.value ?? filter;
        return acc;
      },
      {}
    );

    router.get(
      location.pathname,
      {
        filters: selectedFilters,
      },
      { preserveState: true }
    );
  },
  { deep: true }
);

const { serverSearchOptions } = toRefs(props);
const { executeSearchWithDelay, updateSearch, state: pageState } = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/rent-reports/occupancy?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const isLoading = ref(false);
router.on("start", (event) => {
  if (event.detail.visit.method !== "delete") {
    isLoading.value = true;
  }
});

router.on("finish", () => {
  isLoading.value = false;
});

const { isMobile } = useResponsive();

const selectedMonthName = computed(() => {
  try {
    return format(pageState.dates.startDate, "MMMM, yyyy", {
      locale: es,
    });
  } catch (err) {
    return pageState.dates.startDate;
  }
});

const propertyStats = computed(() => {
  if (!props.invoices) return [];
  
  return Object.entries(props.invoices).map(([ownerName, details]: [string, any]) => {
    const properties = details.properties;
    return Object.entries(properties).map(([propertyId, propertyUnits]: [string, any]) => {
      const units = propertyUnits;
      const totalUnits = units.length;
      
      const stats = {
        // Payment Status
        paid: units.filter((unit: any) => unit.rent_status === 'PAID').length,
        unpaid: units.filter((unit: any) => unit.rent_status === 'LATE' || unit.rent_status === 'PARTIALLY_PAID').length,
        available: units.filter((unit: any) => !unit.rent_id).length,
        retained: units.filter((unit: any) => unit.rent_id && unit.rent_status === 'LATE').length,
        
        // Unit Status
        building: units.filter((unit: any) => unit.current_status === 'BUILDING').length,
        maintenance: units.filter((unit: any) => unit.current_status === 'MAINTENANCE').length,
        rented: units.filter((unit: any) => unit.current_status === 'RENTED').length,
        
        // Rent Status
        active: units.filter((unit: any) => unit.rent_status === 'ACTIVE').length,
        late: units.filter((unit: any) => unit.rent_status === 'LATE').length,
        grace: units.filter((unit: any) => unit.rent_status === 'GRACE').length,
        cancelled: units.filter((unit: any) => unit.rent_status === 'CANCELLED').length,
        expired: units.filter((unit: any) => unit.rent_status === 'EXPIRED').length,

        // Unit Details
        totalPrice: units.reduce((sum: number, unit: any) => sum + (parseFloat(unit.price) || 0), 0),
        totalCommission: units.reduce((sum: number, unit: any) => sum + (parseFloat(unit.commission) || 0), 0),
        averagePrice: units.reduce((sum: number, unit: any) => sum + (parseFloat(unit.price) || 0), 0) / totalUnits,
        averageCommission: units.reduce((sum: number, unit: any) => sum + (parseFloat(unit.commission) || 0), 0) / totalUnits,
        
        // Unit Types
        byBedrooms: units.reduce((acc: Record<string, number>, unit: any) => {
          const beds = unit.bedrooms || 'studio';
          acc[beds] = (acc[beds] || 0) + 1;
          return acc;
        }, {}),
        
        byBathrooms: units.reduce((acc: Record<string, number>, unit: any) => {
          const baths = unit.bathrooms || '0';
          acc[baths] = (acc[baths] || 0) + 1;
          return acc;
        }, {}),
        
        // Amenities
        amenities: units.reduce((acc: Record<string, number>, unit: any) => {
          const unitAmenities = JSON.parse(unit.amenities || '[]');
          unitAmenities.forEach((amenity: string) => {
            acc[amenity] = (acc[amenity] || 0) + 1;
          });
          return acc;
        }, {})
      };

      return {
        ownerName,
        propertyId,
        propertyName: units[0]?.property_name || 'Unknown Property',
        totalUnits,
        ...stats,
        occupancyRate: ((totalUnits - stats.available) / totalUnits) * 100,
        maintenanceRate: (stats.maintenance / totalUnits) * 100,
        buildingRate: (stats.building / totalUnits) * 100,
        revenueRate: (stats.totalPrice / totalUnits) * (stats.occupancyRate / 100)
      };
    });
  }).flat();
});

const totalStats = computed(() => {
  if (!propertyStats.value.length) return null;
  
  return propertyStats.value.reduce((acc: any, curr: any) => {
    acc.totalUnits += curr.totalUnits;
    acc.paid += curr.paid;
    acc.unpaid += curr.unpaid;
    acc.available += curr.available;
    acc.retained += curr.retained;
    acc.building += curr.building;
    acc.maintenance += curr.maintenance;
    acc.rented += curr.rented;
    acc.active += curr.active;
    acc.late += curr.late;
    acc.grace += curr.grace;
    acc.cancelled += curr.cancelled;
    acc.expired += curr.expired;
    acc.totalPrice += curr.totalPrice;
    acc.totalCommission += curr.totalCommission;
    return acc;
  }, {
    totalUnits: 0,
    paid: 0,
    unpaid: 0,
    available: 0,
    retained: 0,
    building: 0,
    maintenance: 0,
    rented: 0,
    active: 0,
    late: 0,
    grace: 0,
    cancelled: 0,
    expired: 0,
    totalPrice: 0,
    totalCommission: 0
  });
});
</script>

<template>
  <AppLayout :title="'Occupancy Report - ' + selectedMonthName">
    <template #title v-if="isMobile">
      <AtDatePager
        class="h-12 ml-4 border-none bg-base-lvl-1 text-body w-44"
        v-model:startDate="pageState.dates.startDate"
        v-model:endDate="pageState.dates.endDate"
        @change="executeSearchWithDelay()"
        controlsClass="bg-transparent text-body hover:bg-base-lvl-1"
        next-mode="month"
      >
        <span class="capitalize"> {{ selectedMonthName }} </span>
      </AtDatePager>
    </template>
    <template #header>
      <PropertySectionNav>
        <template #actions v-if="!isMobile">
          <AtDatePager
            class="w-full h-12 border-none bg-base-lvl-1 text-body"
            v-model:startDate="pageState.dates.startDate"
            v-model:endDate="pageState.dates.endDate"
            @change="executeSearchWithDelay()"
            controlsClass="bg-transparent text-body hover:bg-base-lvl-1"
            next-mode="month"
          />
        </template>
      </PropertySectionNav>
    </template>

    <div class="pt-16 mx-auto md:py-10 sm:px-6 lg:px-8">
      <!-- Total Statistics -->
      <div v-if="totalStats" class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-4">
        <div class="p-4 bg-green-100 rounded-lg">
          <h3 class="text-lg font-semibold text-green-800">Paid Units</h3>
          <p class="text-2xl font-bold text-green-900">{{ totalStats.paid }}</p>
        </div>
        <div class="p-4 bg-red-100 rounded-lg">
          <h3 class="text-lg font-semibold text-red-800">Unpaid Units</h3>
          <p class="text-2xl font-bold text-red-900">{{ totalStats.unpaid }}</p>
        </div>
        <div class="p-4 bg-blue-100 rounded-lg">
          <h3 class="text-lg font-semibold text-blue-800">Available Units</h3>
          <p class="text-2xl font-bold text-blue-900">{{ totalStats.available }}</p>
        </div>
        <div class="p-4 bg-yellow-100 rounded-lg">
          <h3 class="text-lg font-semibold text-yellow-800">Retained Units</h3>
          <p class="text-2xl font-bold text-yellow-900">{{ totalStats.retained }}</p>
        </div>
      </div>

      <!-- Property Statistics -->
      <div class="space-y-6">
        <div v-for="stat in propertyStats" :key="stat.propertyId" class="p-4 bg-white rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">{{ stat.propertyName }}</h2>
            <span class="text-sm text-gray-600">Owner: {{ stat.ownerName }}</span>
          </div>
          
          <!-- Payment Status -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Payment Status</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <div class="p-3 bg-green-50 rounded">
                <h3 class="text-sm font-semibold text-green-800">Paid</h3>
                <p class="text-lg font-bold text-green-900">{{ stat.paid }}</p>
              </div>
              <div class="p-3 bg-red-50 rounded">
                <h3 class="text-sm font-semibold text-red-800">Unpaid</h3>
                <p class="text-lg font-bold text-red-900">{{ stat.unpaid }}</p>
              </div>
              <div class="p-3 bg-blue-50 rounded">
                <h3 class="text-sm font-semibold text-blue-800">Available</h3>
                <p class="text-lg font-bold text-blue-900">{{ stat.available }}</p>
              </div>
              <div class="p-3 bg-yellow-50 rounded">
                <h3 class="text-sm font-semibold text-yellow-800">Retained</h3>
                <p class="text-lg font-bold text-yellow-900">{{ stat.retained }}</p>
              </div>
            </div>
          </div>

          <!-- Unit Status -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Unit Status</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <div class="p-3 bg-purple-50 rounded">
                <h3 class="text-sm font-semibold text-purple-800">Building</h3>
                <p class="text-lg font-bold text-purple-900">{{ stat.building }}</p>
              </div>
              <div class="p-3 bg-orange-50 rounded">
                <h3 class="text-sm font-semibold text-orange-800">Maintenance</h3>
                <p class="text-lg font-bold text-orange-900">{{ stat.maintenance }}</p>
              </div>
              <div class="p-3 bg-indigo-50 rounded">
                <h3 class="text-sm font-semibold text-indigo-800">Rented</h3>
                <p class="text-lg font-bold text-indigo-900">{{ stat.rented }}</p>
              </div>
              <div class="p-3 bg-teal-50 rounded">
                <h3 class="text-sm font-semibold text-teal-800">Available</h3>
                <p class="text-lg font-bold text-teal-900">{{ stat.available }}</p>
              </div>
            </div>
          </div>

          <!-- Unit Details -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Unit Details</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">Total Price</h3>
                <p class="text-lg font-bold text-slate-900">{{ formatMoney(stat.totalPrice) }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">Total Commission</h3>
                <p class="text-lg font-bold text-slate-900">{{ formatMoney(stat.totalCommission) }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">Average Price</h3>
                <p class="text-lg font-bold text-slate-900">{{ formatMoney(stat.averagePrice) }}</p>
              </div>
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">Average Commission</h3>
                <p class="text-lg font-bold text-slate-900">{{ formatMoney(stat.averageCommission) }}</p>
              </div>
            </div>
          </div>

          <!-- Unit Distribution -->
          <div class="mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Unit Distribution</h3>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">By Bedrooms</h3>
                <div class="space-y-1">
                  <div v-for="(count, beds) in stat.byBedrooms" :key="beds" class="flex justify-between">
                    <span>{{ beds }} beds</span>
                    <span class="font-semibold">{{ count }}</span>
                  </div>
                </div>
              </div>
              <div class="p-3 bg-slate-50 rounded">
                <h3 class="text-sm font-semibold text-slate-800">By Bathrooms</h3>
                <div class="space-y-1">
                  <div v-for="(count, baths) in stat.byBathrooms" :key="baths" class="flex justify-between">
                    <span>{{ baths }} baths</span>
                    <span class="font-semibold">{{ count }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Amenities -->
          <div class="mb-4" v-if="Object.keys(stat.amenities).length">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Amenities</h3>
            <div class="flex flex-wrap gap-2">
              <div v-for="(count, amenity) in stat.amenities" :key="amenity" 
                   class="px-2 py-1 bg-slate-100 rounded-full text-sm">
                {{ amenity }} ({{ count }})
              </div>
            </div>
          </div>
          
          <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600">
              <span>Total Units: {{ stat.totalUnits }}</span>
              <div class="space-x-4">
                <span>Occupancy Rate: {{ stat.occupancyRate.toFixed(1) }}%</span>
                <span>Maintenance Rate: {{ stat.maintenanceRate.toFixed(1) }}%</span>
                <span>Building Rate: {{ stat.buildingRate.toFixed(1) }}%</span>
                <span>Revenue Rate: {{ formatMoney(stat.revenueRate) }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style lang="scss" scoped>
.body-section {
  background: white;
  padding: 15px;
}

.el-table th {
  font-weight: bolder;
  color: #222 !important;
}

.section-actions {
  display: flex;

  .app-search__container {
    width: 80%;
    margin-right: 15px;
  }

  .action-buttons {
    width: 20%;
    display: flex;

    button {
      margin-left: auto;
    }
  }
}
</style>

<style lang="scss">
@media (max-width: 1024px) {
  .general-stats .text-3xl {
    font-size: 1em;
  }
}
</style>
