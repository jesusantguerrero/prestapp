<template>
  <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Payments</h2>
    <div class="mt-5">
      <div class="flex justify-between items-center">
        <div class="font-bold">Account Filter:</div>
        <div class="text-right mb-5 space-x-2">
          <jet-button @click="$inertia.visit('invoices/create')">
            Add Invoice
          </jet-button>
        </div>
      </div>

      <div class="w-full">
        <at-table :cols="cols" :tableData="invoices.data" :show-prepend="true">
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
              {{ formatMoney(row.total) }}
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
              <at-button
                @click="$inertia.visit(`invoices/${row.id}/edit`)"
                class="rounded-full text-gray-400 hover:text-green-400 w-8 h-8"
              >
                <i class="fa fa-edit"></i>
              </at-button>
              <at-button class="rounded-full text-gray-400 hover:text-red-400 w-8 h-8">
                <i class="fa fa-trash"></i>
              </at-button>
            </div>
          </template>
          <!-- / Table data-->
        </at-table>
      </div>
    </div>
  </div>
</template>

<script setup>
import AtTable from "@/Atmosphere/Atoms/Table/CustomTable";
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";
import AtButton from "@/Atmosphere/Atoms/Button.vue";
import AtBadge from "@/Atmosphere/Atoms/Badge.vue";
import JetDangerButton from "@/Jetstream/DangerButton.vue";
import JetSelect from "@/Atmosphere/Molecules/JetSelect.vue";
import { format } from "date-fns";
import cols from "./cols";
import { ElNotification } from "element-plus";
import formatMoney from "@/Atmosphere/utils/formatMoney";
import { formatDate } from "@/Atmosphere/utils/formatDate";

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
