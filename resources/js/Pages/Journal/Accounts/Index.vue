<template>
  <AppLayout title="Cuentas">
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
      <div
        class="flex justify-between items-center mb-10 border-4 border-white bg-gray-50 rounded-md py-2"
      >
        <div class="px-5 text-gray-600 font-bold">Chart of Accounts</div>

        <div
          class="flex text-gray-500 font-bold rounded-t-lg overflow-hidden max-w-min space-x-2"
        >
          <AtButton @click="isAccountDialogVisible = true" class="w-36">
            Add Account
          </AtButton>
        </div>
      </div>
      <div class="w-full bg-white px-5 py-5 rounded-md shadow-md">
        <ElTabs v-model="activeAccountSection">
          <ElTabPane
            :label="category.name"
            :name="category.id"
            v-for="category in mainCategories"
            :key="category.id"
            class="w-full bg-white"
          >
            <!-- subCategories -->
            <template v-if="category.subcategories.length">
              <Disclosure
                v-for="subCategory in category.subcategories"
                :key="subCategory.id"
              >
                <DisclosureButton
                  class="flex justify-between w-full px-4 py-2 text-sm font-medium text-left text-blue-900 bg-blue-100 hover:bg-blue-200 focus:outline-none focus-visible:ring focus-visible:ring-blue-500 focus-visible:ring-opacity-75"
                  v-slot="{ open }"
                >
                  <span class="font-bold text-lg">
                    {{ subCategory.name }}
                  </span>

                  <i
                    class="fa fa-chevron-down"
                    :class="open ? 'transform rotate-180' : ''"
                  />
                </DisclosureButton>
                <DisclosurePanel class="text-gray-500 font-bold">
                  <!-- accounts  -->
                  <AtTable
                    :cols="cols(subCategory.name)"
                    :tableData="subCategory.accounts"
                  >
                    <template v-slot:name="{ scope }">
                      <div>
                        <div class="font-bold">{{ scope.row.name }}</div>
                        <div
                          class="font-normal italic"
                          v-if="scope.row.last_transaction_date"
                        >
                          Last transaction on: {{ scope.row.last_transaction_date.date }}
                        </div>
                      </div>
                    </template>
                    <template v-slot:actions="{}">
                      <button>Edit</button>
                    </template>
                  </AtTable>
                  <!-- accounts  -->
                </DisclosurePanel>
              </Disclosure>
            </template>
            <!-- subCategories -->
          </ElTabPane>
        </ElTabs>
      </div>

      <FormModal
        v-model:is-open="isAccountDialogVisible"
        :payment="selectedAccount"
        :endpoint="`/accounts`"
        :categories="categories"
        @saved=""
      />
    </div>
  </AppLayout>
</template>

<script setup>
import { format as formatDate } from "date-fns";
import AppLayout from "@/Components/templates/AppLayout.vue";
// import AtTable from "@/Atmosphere/Atoms/Table/CustomTable";
import cols from "./cols";
// import FormModal from "./FormModal.vue";
import {
  ElTabs,
  ElTabPane,
  ElDropdown,
  ElDropdownItem,
  ElDropdownMenu,
} from "element-plus";
import { Disclosure, DisclosureButton, DisclosurePanel } from "@headlessui/vue";
import { AtButton } from "atmosphere-ui";
import { onMounted, ref, computed } from "vue";

const props = defineProps({
  accounts: {
    type: Array,
  },
  categories: {
    type: Array,
  },
});

const isAccountDialogVisible = ref(false);
const selectedAccount = ref(null);
const searchText = ref("");
const activeName = ref([]);
const activeAccountSection = ref("");
const accountsCategories = ref([]);

onMounted(() => {
  getAccounts();
});

const formatDateFilter = (date) => {
  return formatDate(date, "YYYY-MM-DD");
};

const section = computed(() => {
  return "accounts";
});

const mainCategories = computed(() => {
  return props.categories;
});

const getAccounts = () => {
  accountsCategories.value = props.categories;
  activeAccountSection.value =
    mainCategories.value && mainCategories.value.length ? mainCategories.value.id : "";
};

const editAccount = () => {};

const rowClick = (command, service) => {
  switch (command) {
    case "edit":
      editAccount(service);
      break;
    default:
      break;
  }
};
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
