<template>
  <AppLayout title="Pagos">
    <template #header>
      <div class="flex justify-between items-center px-5 py-1">
        <div class="font-bold">Account Filter:</div>
        <div class="text-right space-x-2">
          <AppButton variant="secondary" @click="$inertia.visit('invoices/create')">
            Crear Factura
          </AppButton>
        </div>
      </div>
    </template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div class="mt-5 rounded-md bg-white">
        <div class="w-full">
          <AtTable :cols="cols" :tableData="invoices.data" :show-prepend="true">
            <!-- Table Data -->
            <template v-slot:date="{ scope: { row } }">
              <div>
                <div class="font-bold text-blue-400">{{ formatDate(row.date) }}</div>
              </div>
            </template>

            <template v-slot:concept="{ scope: { row } }">
              <div
                class="border-dashed border-b border-blue-400 text-blue-400 cursor-pointer capitalize text-md"
              >
                {{ row.concept }}
                <span class="font-bold text-gray-300">
                  {{ row.series }} #{{ row.number }}
                </span>
              </div>
            </template>

            <template v-slot:status="{ scope: { row } }">
              <div class="font-bold capitalize">
                {{ row.status }}
              </div>
            </template>

            <template v-slot:total="{ scope: { row } }">
              <div class="font-bold">
                {{ formatMoney(row.amount) }}
              </div>
            </template>

            <template v-slot:debt="{ scope: { row } }">
              <div
                class="font-bold"
                :class="[row.debt > 0 ? 'text-red-500' : 'text-green-500']"
              >
                {{ formatMoney(row.debt) }}
              </div>
            </template>

            <template v-slot:actions="{ scope: { row } }">
              <div class="space-x-2 flex">
                <AtButton
                  @click="$inertia.visit(`invoices/${row.id}/edit`)"
                  class="rounded-full text-gray-400 hover:text-green-400 w-8 h-8"
                >
                  <i class="fa fa-edit"></i>
                </AtButton>
                <AtButton class="rounded-full text-gray-400 hover:text-red-400 w-8 h-8">
                  <i class="fa fa-trash"></i>
                </AtButton>
              </div>
            </template>
            <!-- / Table data-->
          </AtTable>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ElNotification } from "element-plus";
import { AtButton, AtTable } from "atmosphere-ui";
import AppButton from "@/Components/shared/AppButton.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";

import cols from "./cols";
import { format } from "date-fns";
import { formatMoney, formatDate } from "@/utils";

const props = defineProps({
  invoices: {
    type: Array,
  },
  categories: {
    type: Array,
  },
});

const isAccountDialogVisible = false;
const selectedAccount = null;
const showAdd = false;
const isIncome = false;
const isLoading = false;
const formData = {};
const searchText = "";
const activeName = [];
const activeAccountSection = "";
const accountsCategories = [];

function rowClick(command, service) {
  switch (command) {
    case "edit":
      this.editAccount(service);
      break;
    default:
      break;
  }
}

function setRequestData(data) {
  const requestData = {
    ...data,
  };
  requestData.direction = this.isIncome ? "DEPOSIT" : "WITHDRAW";
  requestData.resource_type_id = "MANUAL";
  return requestData;
}
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
