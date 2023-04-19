<script lang="ts" setup>
import { reactive, computed, watch, ref } from "vue";
import { AtButton } from "atmosphere-ui";
import { router } from "@inertiajs/vue3";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";
import AppButton from "@/Components/shared/AppButton.vue";
import AccountingSectionNav from "../Partials/AccountingSectionNav.vue";

const props = defineProps({
  invoices: {
    type: Array,
  },
  type: {
    type: String,
  },
});

const state = reactive({
  sectionName: computed(() => {
    return `${props.type.toLowerCase()}s`;
  }),
});

const filters = reactive({
  client_id: null,
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc, [filterName, filter]) => {
        acc[filterName] = filter?.id;
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

const isInvoiceModalOpen = ref(false);
</script>

<template>
  <AppLayout title="Transaccciones">
    <template #header>
      <AccountingSectionNav>
        <template #actions>
          <p>Total: {{ invoices.data.length }}</p>
          <AtButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Imprimir</AtButton
          >
          <AtButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Filtros</AtButton
          >
          <BaseSelect
            v-model="filters.client_id"
            track-by="id"
            endpoint="/api/clients"
            placeholder="selecciona un cliente"
            label="display_name"
          />
          <AppButton
            @click="router.visit(`/${state.sectionName}/create`)"
            variant="inverse"
            >Ingreso</AppButton
          >
          <AppButton @click="isInvoiceModalOpen = !isInvoiceModalOpen" variant="inverse">
            Egreso
          </AppButton>
        </template>
      </AccountingSectionNav>
    </template>

    <div class="py-10 mx-auto sm:px-6 lg:px-8">
      <InvoiceTable :invoice-data="invoices.data" class="mt-10 bg-base-lvl-3" />
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
