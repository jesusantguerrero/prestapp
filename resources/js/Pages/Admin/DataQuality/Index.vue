<script setup lang="ts">
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import AppLayout from "@/Components/templates/AppLayout.vue";
import { formatDate } from "@/utils";

const { t } = useI18n();

interface ActiveRent {
  id: number;
  unit_name: string;
  property_name: string;
  status: string;
}

interface DuplicateClient {
  id: number;
  display_name: string;
  email: string;
  phone: string;
  dni: string;
  rents: ActiveRent[];
  can_be_deleted: boolean;
  similar_clients: {
    id: number;
    display_name: string;
    email: string;
    phone: string;
    dni: string;
    rents: ActiveRent[];
    can_be_deleted: boolean;
    similarity_scores: {
      name: number;
      email: number;
      phone: number;
      total: number;
    };
  }[];
}

interface RentHistory {
  id: number;
  unit_name: string;
  tenant_name: string;
  start_date: string;
  end_date: string;
  status: string;
}

interface DuplicateProperty {
  id: number;
  name: string;
  address: string;
  owner_id: number;
  owner_name: string;
  units_count: number;
  rent_history: RentHistory[];
  total_instances: number;
  similar_properties: {
    id: number;
    name: string;
    address: string;
    owner_id: number;
    owner_name: string;
    units_count: number;
    rent_history: RentHistory[];
    similarity_scores: {
      name: number;
      address: number;
      total: number;
    };
  }[];
}

interface InconsistentRent {
  id: number;
  unit_id: number;
  unit_name: string;
  property_name: string;
  tenant_name: string;
  start_date: string;
  end_date: string;
  status: string;
  issue_type: 'overlap' | 'gap' | 'duplicate';
  conflicting_rents?: {
    id: number;
    start_date: string;
    end_date: string;
    tenant_name: string;
  }[];
}

interface Props {
  duplicate_clients: DuplicateClient[];
  duplicate_properties: DuplicateProperty[];
  inconsistent_rents: InconsistentRent[];
}

const props = defineProps<Props>();

const activeTab = ref<'clients' | 'properties' | 'rents'>('clients');

function getRentStatusColor(status: string): string {
  switch (status) {
    case 'ACTIVE':
      return 'bg-green-100 text-green-800';
    case 'LATE':
      return 'bg-red-100 text-red-800';
    case 'PARTIALLY_PAID':
      return 'bg-yellow-100 text-yellow-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}

function handleDeleteClient(clientId: number) {
  // TODO: Implement client deletion
  console.log('Delete client:', clientId);
}
</script>

<template>
  <AppLayout :title="t('Data Quality')">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Navigation Tabs -->
        <div class="mb-4 border-b border-gray-200">
          <nav class="flex space-x-8" aria-label="Tabs">
            <button
              v-for="tab in ['clients', 'properties', 'rents']"
              :key="tab"
              @click="activeTab = tab"
              :class="[
                activeTab === tab
                  ? 'border-primary text-primary'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
              ]"
            >
              {{ t(tab.charAt(0).toUpperCase() + tab.slice(1)) }}
              <span
                :class="[
                  activeTab === tab ? 'bg-primary-100 text-primary-600' : 'bg-gray-100 text-gray-900',
                  'ml-3 hidden px-2.5 py-0.5 rounded-full text-xs font-medium md:inline-block'
                ]"
              >
                {{ 
                  tab === 'clients' 
                    ? props.duplicate_clients.length 
                    : tab === 'properties'
                      ? props.duplicate_properties.length
                      : props.inconsistent_rents.length 
                }}
              </span>
            </button>
          </nav>
        </div>

        <!-- Duplicate Clients -->
        <div v-if="activeTab === 'clients'" class="space-y-4">
          <div v-for="client in duplicate_clients" :key="client.id" class="bg-white shadow rounded-lg p-4">
            <div class="border-b pb-4 mb-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">{{ client.display_name }}</h3>
                <button v-if="client.can_be_deleted"
                        @click="handleDeleteClient(client.id)"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                  {{ t('Delete Client') }}
                </button>
              </div>
              <div class="mt-1 text-sm text-gray-500 space-y-1">
                <p>{{ t('Email') }}: {{ client.email }}</p>
                <p>{{ t('Phone') }}: {{ client.phone }}</p>
                <p>{{ t('DNI') }}: {{ client.dni }}</p>
              </div>
              <div v-if="client.rents.length" class="mt-3">
                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('Active Rents') }}:</h4>
                <div class="space-y-2">
                  <div v-for="rent in client.rents" :key="rent.id" 
                       class="flex items-center justify-between bg-gray-50 p-2 rounded text-sm">
                    <span>{{ rent.property_name }} - {{ rent.unit_name }}</span>
                    <span :class="[getRentStatusColor(rent.status), 'px-2 py-1 rounded-full text-xs font-medium']">
                      {{ rent.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="space-y-3">
              <p class="text-sm font-medium text-gray-700">{{ t('Similar Clients') }}:</p>
              <div v-for="similar in client.similar_clients" :key="similar.id" 
                   class="bg-gray-50 p-3 rounded">
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center space-x-3">
                    <p class="font-medium">{{ similar.display_name }}</p>
                    <button v-if="similar.can_be_deleted"
                            @click="handleDeleteClient(similar.id)"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                      {{ t('Delete Client') }}
                    </button>
                  </div>
                  <div class="text-sm">
                    <span class="text-gray-500">{{ t('Similarity') }}:</span>
                    <span class="ml-1 font-medium">{{ similar.similarity_scores.total }}%</span>
                  </div>
                </div>
                <div class="text-sm text-gray-500 space-y-1">
                  <p>{{ t('Email') }}: {{ similar.email }}</p>
                  <p>{{ t('Phone') }}: {{ similar.phone }}</p>
                  <p>{{ t('DNI') }}: {{ similar.dni }}</p>
                </div>
                <div v-if="similar.rents.length" class="mt-3">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('Active Rents') }}:</h4>
                  <div class="space-y-2">
                    <div v-for="rent in similar.rents" :key="rent.id" 
                         class="flex items-center justify-between bg-white p-2 rounded text-sm">
                      <span>{{ rent.property_name }} - {{ rent.unit_name }}</span>
                      <span :class="[getRentStatusColor(rent.status), 'px-2 py-1 rounded-full text-xs font-medium']">
                        {{ rent.status }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Duplicate Properties -->
        <div v-if="activeTab === 'properties'" class="space-y-4">
          <div v-for="property in duplicate_properties" :key="property.id" class="bg-white shadow rounded-lg p-4">
            <div class="border-b pb-4 mb-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">{{ property.name }}</h3>
                <div class="flex items-center space-x-2">
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ t('Total Instances') }}: {{ property.total_instances }}
                  </span>
                  <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ t('Units') }}: {{ property.units_count }}
                  </span>
                </div>
              </div>
              <div class="mt-1 text-sm text-gray-500">
                <p>{{ t('Address') }}: {{ property.address }}</p>
                <p>{{ t('Owner') }}: {{ property.owner_name }}</p>
              </div>
              <div v-if="property.rent_history.length" class="mt-3">
                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('Rent History') }}:</h4>
                <div class="space-y-2">
                  <div v-for="rent in property.rent_history" :key="rent.id" 
                       class="flex items-center justify-between bg-gray-50 p-2 rounded text-sm">
                    <div>
                      <span class="font-medium">{{ rent.unit_name }}</span>
                      <span class="text-gray-500"> - {{ rent.tenant_name }}</span>
                      <div class="text-xs text-gray-500">
                        {{ formatDate(rent.start_date) }} - {{ formatDate(rent.end_date) }}
                      </div>
                    </div>
                    <span :class="[getRentStatusColor(rent.status), 'px-2 py-1 rounded-full text-xs font-medium']">
                      {{ rent.status }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="space-y-3">
              <p class="text-sm font-medium text-gray-700">{{ t('Similar Properties') }}:</p>
              <div v-for="similar in property.similar_properties" :key="similar.id" 
                   class="bg-gray-50 p-3 rounded">
                <div class="flex items-center justify-between mb-2">
                  <div>
                    <div class="flex items-center space-x-2">
                      <p class="font-medium">{{ similar.name }}</p>
                      <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        {{ t('Units') }}: {{ similar.units_count }}
                      </span>
                    </div>
                    <p class="text-sm text-gray-500">{{ similar.address }}</p>
                    <p class="text-sm text-gray-500">{{ t('Owner') }}: {{ similar.owner_name }}</p>
                  </div>
                  <div class="text-sm space-y-1">
                    <p>
                      <span class="text-gray-500">{{ t('Name Match') }}:</span>
                      <span class="ml-1 font-medium">{{ similar.similarity_scores.name }}%</span>
                    </p>
                    <p>
                      <span class="text-gray-500">{{ t('Address Match') }}:</span>
                      <span class="ml-1 font-medium">{{ similar.similarity_scores.address }}%</span>
                    </p>
                    <p>
                      <span class="text-gray-500">{{ t('Total Match') }}:</span>
                      <span class="ml-1 font-medium">{{ similar.similarity_scores.total }}%</span>
                    </p>
                  </div>
                </div>
                <div v-if="similar.rent_history.length" class="mt-3">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">{{ t('Rent History') }}:</h4>
                  <div class="space-y-2">
                    <div v-for="rent in similar.rent_history" :key="rent.id" 
                         class="flex items-center justify-between bg-white p-2 rounded text-sm">
                      <div>
                        <span class="font-medium">{{ rent.unit_name }}</span>
                        <span class="text-gray-500"> - {{ rent.tenant_name }}</span>
                        <div class="text-xs text-gray-500">
                          {{ formatDate(rent.start_date) }} - {{ formatDate(rent.end_date) }}
                        </div>
                      </div>
                      <span :class="[getRentStatusColor(rent.status), 'px-2 py-1 rounded-full text-xs font-medium']">
                        {{ rent.status }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Inconsistent Rents -->
        <div v-if="activeTab === 'rents'" class="space-y-4">
          <div v-for="rent in inconsistent_rents" :key="rent.id" class="bg-white shadow rounded-lg p-4">
            <div class="border-b pb-4 mb-4">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                  {{ rent.unit_name }} - {{ rent.property_name }}
                </h3>
                <span :class="{
                  'bg-red-100 text-red-800': rent.issue_type === 'overlap',
                  'bg-yellow-100 text-yellow-800': rent.issue_type === 'gap',
                  'bg-orange-100 text-orange-800': rent.issue_type === 'duplicate'
                }" class="px-2 py-1 text-xs font-medium rounded-full">
                  {{ t(rent.issue_type.charAt(0).toUpperCase() + rent.issue_type.slice(1)) }}
                </span>
              </div>
              <div class="mt-1 text-sm text-gray-500">
                <p>{{ t('Tenant') }}: {{ rent.tenant_name }}</p>
                <p>{{ t('Period') }}: {{ formatDate(rent.start_date) }} - {{ formatDate(rent.end_date) }}</p>
                <p>{{ t('Status') }}: {{ rent.status }}</p>
              </div>
            </div>
            <div v-if="rent.conflicting_rents?.length" class="space-y-3">
              <p class="text-sm font-medium text-gray-700">{{ t('Conflicting Rents') }}:</p>
              <div v-for="conflict in rent.conflicting_rents" :key="conflict.id" 
                   class="bg-gray-50 p-3 rounded">
                <p class="font-medium">{{ conflict.tenant_name }}</p>
                <p class="text-sm text-gray-500">
                  {{ formatDate(conflict.start_date) }} - {{ formatDate(conflict.end_date) }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 