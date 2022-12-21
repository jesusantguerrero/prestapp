<template>
  <AppLayout title="Facturas de Ingresos">
    <template #header>
      <AccountingSectionNav>
        <template #actions>
          <AppButton @click="router.visit(`/${state.sectionName}/create`)" variant="inverse">Registrar Ingreso</AppButton>
        </template>
      </AccountingSectionNav>
    </template>
    
    <div class="py-10 mx-auto sm:px-6 lg:px-8">
        <AtTable
            :cols="cols"
            :tableData="invoices.data"
            :show-prepend="true"
            class="mt-10 bg-base-lvl-3"
        >
          <!-- Table Data -->
          <template v-slot:date="{ scope: { row } }">
              <div>
                  <div class="font-bold text-blue-400"> {{ formatDate(row.date) }} </div>
              </div>
          </template>

          <template v-slot:concept="{ scope: { row } }">
              <div class="text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-md">
                  {{ row.concept }}
                  <span class="font-bold text-gray-300"> {{ row.series }} #{{ row.number }} </span>
              </div>
          </template>

          <template v-slot:status="{ scope: { row } }">
              <div class="font-bold capitalize">
                  {{ row.status }}
              </div>
          </template>

          <template v-slot:total="{ scope: { row } }">
              <div class="font-bold">
                  {{ formatMoney(row.total) }}
              </div>
          </template>

          <template v-slot:debt="{ scope: { row } }" >
              <div class="font-bold" :class="[row.debt > 0 ?  'text-red-500' : 'text-green-500']">
                  {{ formatMoney(row.debt) }}
              </div>
          </template>

          <template v-slot:actions="{ scope: { row } }">
              <div class="flex items-center space-x-2">
                  <AtButton @click="$inertia.visit(`${state.sectionName}/${row.id}/edit`)" class="w-8 h-8 text-gray-400 rounded-full hover:text-green-400"> 
                    <i class="fa fa-edit"></i> 
                  </AtButton>
                  <AtButton class="w-8 h-8 text-gray-400 rounded-full hover:text-red-400"> 
                    <i class="fa fa-trash"></i> 
                  </AtButton>
                  <InvoicePaymentOptions :invoice="row" />
              </div>
          </template>
          <!-- / Table data-->
        </AtTable>
    </div>
  </AppLayout>
</template>

<script setup>
import { reactive, computed } from "vue";
import { AtButton, AtTable } from "atmosphere-ui"

import AppSectionHeader from "../../../Components/AppSectionHeader.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";

import cols from "./cols";
import { formatMoney, formatDate } from "@/utils";
import AccountingSectionNav from "../Partials/AccountingSectionNav.vue";
import AppButton from "../../../Components/shared/AppButton.vue";
import InvoicePaymentOptions from "../../Rents/Partials/InvoicePaymentOptions.vue";

const props = defineProps({
    invoices: {
        type: Array,
    },
    type: {
        type: String,
    },
})

const state = reactive({
    sectionName: computed(() => {
        return `${props.type.toLowerCase()}s`
    }),
})
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
