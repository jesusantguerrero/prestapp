<script setup lang="ts">
import { reactive, computed, toRefs, ref, capitalize, watch } from "vue";
import { router } from "@inertiajs/vue3";

import { formatMoney } from "@/utils";
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";
import { useServerSearch } from "@/utils/useServerSearch";
import { usePrint } from "@/utils/usePrint";

const props = defineProps({
  categoryType: {
    type: String,
    default: "income",
  },
  categories: {
    type: Array,
  },
  accounts: {
    type: Array,
  },
  ledger: {
    type: Object,
  },
  serverSearchOptions: {
    type: Object,
    default() {
      return {};
    },
  },
});

const sectionTitle = computed(() => {
  return `${capitalize(props.categoryType)} - Statement`;
});

const state = reactive({
  isSummary: true,
  isTransferModalOpen: false,
  mainCategories: computed(() => {
    return props.categories;
  }),
  categoriesTotal: computed(() => {
    return props.categories.reduce((total, category) => {
      return total + parseFloat(category.total || 0);
    }, 0);
  }),
  transferAccount: null,
});

const lastParent = ref(null);
const hasHeader = (category: Record<string, any>) => {
  const parent = category.category;
  if (parent && (!lastParent.value || lastParent.value.id !== parent.id)) {
    lastParent.value = parent;
    return true;
  } else {
    return false;
  }
};

const { isSummary, mainCategories, categoriesTotal } = toRefs(state);

const { serverSearchOptions } = toRefs(props);
const { executeSearch, state: searchState } = useServerSearch(
  serverSearchOptions,
  (url: string) => {
    router.get(
      `${window.location.pathname}?${url}`,
      {},
      {
        preserveScroll: true,
        preserveState: true,
      }
    );
  },
  {
    manual: true,
  }
);
const filters = reactive({
  property: null,
  account: null,
});

const { customPrint } = usePrint("report");
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <div class="flex items-center justify-end py-1 mx-5 rounded-md">
        <div class="flex space-x-2 font-bold text-gray-500 rounded-t-lg max-w-min">
          <ElDatePicker
            :model-value="[searchState.dates.startDate, searchState.dates.endDate]"
            type="daterange"
            unlink-panels
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            size="large"
            @update:model-value="
              (event) => {
                searchState.dates.startDate = event[0];
                searchState.dates.endDate = event[1];
              }
            "
          />
          <BaseSelect
            endpoint="/api/properties"
            v-model="filters.property"
            @update:model-value="searchState.filters.property = $event.id"
            label="name"
            track-by="id"
            class="md:w-[200px]"
            placeholder="Propiedad o Dueño"
          />
          <AccountSelect
            endpoint="/api/accounts"
            v-model="filters.account"
            @update:mode-value="searchState.filters.account = $event.id"
            class="md:w-[200px]"
            multiple
          />
          <AppButton @click="executeSearch()"> Generar Reporte </AppButton>
          <AppButton variant="neutral" @click="customPrint()">
            <IMdiPrinter />
          </AppButton>
        </div>
      </div>
    </template>

    <div
      class="w-full rounded-md bg-white mt-16 shadow-md printable py-10 mx-auto mb-32 sm:px-6 lg:px-8 print:shadow-none print:w-screen print:absolute print:mt-0"
      id="report"
    >
      <header class="text-center text-gray-500">
        <h4 class="text-3xl font-bold capitalize">{{ sectionTitle }}</h4>
        <h5 class="font-bold">Neatforms</h5>
        <p>From date to date</p>
      </header>

      <div class="flex items-center justify-end space-x-2 print:hidden">
        <section>{{ ledger.assets[0].total }} =</section>
        <section class="flex space-x-2">
          <AppButton variant="secondary" @click="isSummary = true"> Summary </AppButton>
          <AppButton variant="secondary" @click="isSummary = false"> Details </AppButton>
        </section>
      </div>
      <div class="mt-10 items" :class="{ 'divide-y': isSummary }">
        <div v-for="category in mainCategories" :key="category.id" class="py-2">
          <div
            v-if="hasHeader(category)"
            class="w-full px-5 py-2 mt-5 font-bold bg-gray-200"
          >
            {{ category.category.alias ?? category.category.name }}
            {{ category.category.total }}
          </div>
          <div class="divide-y" v-if="!isSummary">
            <div class="px-5 py-2 font-semibold bg-gray-100">
              {{ category.alias ?? category.name }}
            </div>
            <div class="w-full px-5" v-for="account in category.accounts">
              <div class="flex justify-between py-2">
                <span class="font-semibold text-blue-500">
                  {{ account.alias ?? account.name }}
                </span>
                <div class="space-x-4">
                  <span class="font-bold text-success">
                    {{ formatMoney(account.income) }}</span
                  >
                  <span class="font-bold text-error">
                    {{ formatMoney(account.outcome) }}</span
                  >
                  <span> {{ formatMoney(account.balance) }}</span>
                  <!-- <AtButton @click="setPayment(account)"> Pay </AtButton> -->
                </div>
              </div>
            </div>
          </div>
          <div class="flex justify-between px-5 py-2" :class="{ 'border-t': !isSummary }">
            <span class="font-bold"> {{ category.alias ?? category.name }} </span>
            <div class="flex space-x-4">
              <span class="font-bold text-success">
                {{ formatMoney(category.income) }}</span
              >
              <span class="font-bold text-error">
                {{ formatMoney(category.outcome) }}</span
              >
              <span class="font-bold"> {{ formatMoney(category.total) }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-between py-5 text-xl capitalize">
          <span class="font-bold"> Total {{ categoryType }}s </span>
          <span class="font-bold"> {{ formatMoney(categoriesTotal) }}</span>
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
