<script setup>
import { reactive, computed, watch } from "vue";
import { AtButton, AtTable, AtBackgroundIconCard } from "atmosphere-ui";
import { Link, router } from "@inertiajs/vue3";

import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";

import { formatMoney, formatDate } from "@/utils";
import AppButton from "@/Components/shared/AppButton.vue";

const props = defineProps({
  invoices: {
    type: Array,
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
});

const filters = reactive({
  owner: null,
  property: null,
});

const state = reactive({
  sectionName: computed(() => {
    return `${props.type.toLowerCase()}s`;
  }),
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc, [filterName, filter]) => {
        acc[filterName] = filter?.value;
        return acc;
      },
      {}
    );

    router.get(
      "/properties/management-tools",
      {
        filters: selectedFilters,
      },
      { preserveState: true }
    );
  },
  { deep: true }
);
</script>

<template>
  <AppLayout title="Facturas de Ingresos">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <!-- <p>Total: {{ invoices.length }}</p> -->
          <!-- <AtButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Imprimir
          </AtButton> -->
          <!-- <BaseSelect :options="properties" v-model="filters.property" /> -->
          <BaseSelect
            :options="owners"
            placeholder="Filtrar por dueño"
            v-model="filters.owner"
          />

          <AppButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Ingreso</AppButton
          >
          <AppButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Egreso</AppButton
          >
        </template>
      </PropertySectionNav>
    </template>

    <div class="py-10 mx-auto sm:px-6 lg:px-8">
      <section class="flex space-x-4">
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Pagado"
          :value="formatMoney(paid)"
        />
        <button v-if="deposits">Renbolsar Deposito</button>
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Balance de Pendiente"
          :value="formatMoney(outstanding)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Dias de mora"
          :value="lateDays || 0"
        />
      </section>
      <InvoiceTable :invoice-data="invoices" class="mt-10 bg-base-lvl-3 rounded-md" />
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
