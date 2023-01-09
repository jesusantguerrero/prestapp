<template>
  <AppLayout title="Facturas de Ingresos">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <p>Total: {{ invoices.length }}</p>
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
      <InvoiceTable :invoice-data="invoices" class="mt-10 bg-base-lvl-3" />
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from "vue";
import { AtButton, AtTable } from "atmosphere-ui";

import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";

import { formatMoney, formatDate } from "@/utils";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { Link } from "@inertiajs/vue3";

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
</script>

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
