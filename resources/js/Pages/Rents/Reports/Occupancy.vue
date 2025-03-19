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
import PropertyPaymentStatus from "@/Components/Reports/PropertyPaymentStatus.vue";
import PropertyUnitStatus from "@/Components/Reports/PropertyUnitStatus.vue";
import PropertyUnitDetails from "@/Components/Reports/PropertyUnitDetails.vue";
import PropertyUnitDistribution from "@/Components/Reports/PropertyUnitDistribution.vue";
import PropertyAmenities from "@/Components/Reports/PropertyAmenities.vue";
import { useI18n } from "vue-i18n";

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
  available: {
    type: Number,
  },
  rented: {
    type: Number,
  },
  unpaid: {
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

const { t } = useI18n();

const propertyStats = computed(() => {
  if (!props.invoices) return [];

  console.log(props.invoices);
  
  return Object.entries(props.invoices).map(([ownerName, properties]: [string, any]) => {
    return Object.entries(properties).map(([propertyId, propertyData]: [string, any]) => {
      return {
        ownerName: propertyData.ownerName,
        propertyId: propertyData.propertyId,
        propertyName: propertyData.propertyName,
        totalUnits: propertyData.totalUnits,
        
        // Payment Status
        paid: propertyData.paid,
        unpaid: propertyData.unpaid,
        available: propertyData.available,
        rented: propertyData.rented,
        realRented: propertyData.realRented,
        
        // Unit Status
        building: propertyData.building,
        maintenance: propertyData.maintenance,
        
        // Rent Status
        active: propertyData.active,
        late: propertyData.late,
        grace: propertyData.grace,
        cancelled: propertyData.cancelled,
        expired: propertyData.expired,
        
        // Unit Details
        totalPrice: propertyData.totalPrice,
        totalCommission: propertyData.totalCommission,
        averagePrice: propertyData.averagePrice,
        averageCommission: propertyData.averageCommission,
        
        // Unit Types
        byBedrooms: propertyData.byBedrooms,
        byBathrooms: propertyData.byBathrooms,
        
        // Amenities
        amenities: propertyData.amenities,
        
        // Rates
        occupancyRate: propertyData.occupancyRate,
        maintenanceRate: propertyData.maintenanceRate,
        buildingRate: propertyData.buildingRate,
        revenueRate: propertyData.revenueRate
      };
    });
  }).flat();
});
</script>

<template>
  <AppLayout :title="'Occupancy Report - ' + selectedMonthName">
    <template #title v-if="isMobile">
      <BaseSelect
             class="min-w-max"
             size="large"
              v-model="pageState.filters.owner"
             endpoint="/api/clients?filter[is_owner]=1"
             :placeholder="$t('select an owner')"
             label="display_name"
             track-by="id"
           />
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
      <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2">
        <div class="p-4 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800">Total Units</h3>
          
          <p class="text-3xl font-bold text-gray-900 mb-4">{{ total }}</p>
          
          <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col">
              <span class="text-sm font-medium text-green-600">Paid Units</span>
              <span class="text-xl font-semibold text-green-700">{{ paid }}</span>
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-medium text-red-600">Unpaid Units</span>
              <span class="text-xl font-semibold text-red-700">{{ unpaid }}</span>
            </div>
          </div>
          
          <div class="mt-3 bg-gray-100 rounded-full h-2 overflow-hidden">
            <div class="h-full bg-green-500" 
                 :style="{ width: ((paid / total) * 100) + '%' }"></div>
          </div>
        </div>

        <div class="p-4 bg-white rounded-lg shadow-sm">
          <h3 class="text-lg font-semibold text-gray-800">Unit Status</h3>
          <p class="text-3xl font-bold text-gray-900 mb-4">{{ total }}</p>
          
          <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col">
              <span class="text-sm font-medium text-yellow-600">Rented Units</span>
              <span class="text-xl font-semibold text-yellow-700">{{ rented }}</span>
            </div>
            <div class="flex flex-col">
              <span class="text-sm font-medium text-blue-600">Available Units</span>
              <span class="text-xl font-semibold text-blue-700">{{ available }}</span>
            </div>
          </div>
          
          <div class="mt-3 bg-gray-100 rounded-full h-2 overflow-hidden">
            <div class="h-full bg-yellow-500" 
                 :style="{ width: ((rented / total) * 100) + '%' }"></div>
          </div>
        </div>
      </div>

      <!-- Property Statistics -->
      <div class="space-y-6">
        <div v-for="stat in propertyStats" :key="stat.propertyId" class="p-4 bg-white rounded-lg shadow">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">{{ stat.propertyName }}</h2>
            <span class="text-sm text-gray-600">{{ $t('Owner') }}: {{ stat.ownerName }}</span>
          </div>
          
          <PropertyPaymentStatus
            :paid="stat.paid"
            :unpaid="stat.unpaid"
            :available="stat.available"
            :rented="stat.rented"
          />

          <PropertyUnitStatus
            :building="stat.building"
            :maintenance="stat.maintenance"
            :rented="stat.rented"
            :available="stat.available"
          />

          <PropertyUnitDetails
            :total-price="stat.totalPrice"
            :total-commission="stat.totalCommission"
            :average-price="stat.averagePrice"
            :average-commission="stat.averageCommission"
          />

          <PropertyUnitDistribution
            :by-bedrooms="stat.byBedrooms"
            :by-bathrooms="stat.byBathrooms"
          />

          <PropertyAmenities
            :amenities="stat.amenities"
          />
          
          <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600">
              <span>{{ t('Total Units') }}: {{ stat.totalUnits }}</span>
              <div class="space-x-4">
                <span>{{ t('Occupancy Rate') }}: {{ stat.occupancyRate.toFixed(1) }}%</span>
                <span>{{ t('Maintenance Rate') }}: {{ stat.maintenanceRate.toFixed(1) }}%</span>
                <span>{{ t('Building Rate') }}: {{ stat.buildingRate.toFixed(1) }}%</span>
                <span>{{ t('Revenue Rate') }}: {{ formatMoney(stat.revenueRate) }}</span>
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
