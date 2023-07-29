<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed, ref, toRefs } from "vue";
import { router } from "@inertiajs/core";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import cols from "./cols";
import { IRent } from "@/Modules/property/propertyEntity";
import { getRangeParams } from "@/utils";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";
import { ElMessageBox } from "element-plus";
import { rentStatus } from "@/Modules/properties/constants";
import ContractCardMini from "@/Components/templates/ContractCardMini.vue";
import { useResponsive } from "@/utils/useResponsive";
import AppButtonTab from "@/Components/shared/AppButtonTab.vue";
import { addDays, addMonths, endOfMonth, startOfMonth } from "date-fns";

interface IPaginatedData {
  data: IRent[];
}

const props = defineProps<{
  rents: IRent[] | IPaginatedData;
  kpis: Record<string, number>;
  serverSearchOptions: IServerSearchData;
}>();

const { serverSearchOptions } = toRefs(props);
const {
  executeSearch,
  updateSearch,
  changeSize,
  paginate,
  reset,
  state: searchState,
} = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/rents?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const expiringRanges: Record<string, any> = {
  in_month: {
    range() {
      const today = new Date();
      return [startOfMonth(today), endOfMonth(today)];
    },
  },
  within_three_months: {
    range() {
      const today = new Date();
      const nextMonth = addDays(endOfMonth(today), 1);

      return [nextMonth, endOfMonth(addMonths(nextMonth, 2))];
    },
  },
  expired: {
    range: () => [null, 0],
  },
};

const setRange = (field: string, range: number[]) => {
  const params = getRangeParams(field, range);
  router.get(
    `/rent-renewals?${params}`,
    {},
    {
      preserveState: true,
    }
  );
};

const onStateSelected = (rangeName: string) => {
  if (rangeName == "TOTAL") {
    router.get("/rent-renewals");
  } else {
    setRange("end_date", expiringRanges[rangeName].range());
  }
};
const listData = computed(() => {
  return Array.isArray(props.rents) ? props.rents : props.rents.data;
});

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};

const deleteRent = async (rent: IRent) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el contrato de ${rent.address} ${rent.client_name}?`,
    "Eliminar contrato"
  );
  if (isValid) {
    router.delete(route("rents.destroy", rent), {
      onSuccess() {
        router.reload();
      },
    });
  }
};

const { isMobile } = useResponsive();

const statusTabs = computed(() => {
  return Object.entries(rentStatus).map(([name, value]) => ({
    ...value,
    name,
    count: props?.kpis[name?.toUpperCase?.()] ?? 0,
  }));
});
</script>

<template>
  <AppLayout title="Contratos de alquiler">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-16 -mx-4 md:mx-0">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @search="executeSearch"
          @blur="executeSearch"
        />
        <AppButton @click="router.visit(route('rents.create'))" v-if="!isMobile">
          Agregar Contrato
        </AppButton>
      </section>

      <section class="grid grid-cols-3 gap-2 mt-2 md:flex md:space-x-1">
        <AppButtonTab
          class="capitalize text-xs bg-primary/20 rounded-md"
          @click="onStateSelected('TOTAL')"
        >
          {{ $t("TOTAL") }}
        </AppButtonTab>
        <AppButtonTab
          v-for="(status, stateName) in kpis"
          class="capitalize text-xs bg-primary/20 rounded-md"
          @click="onStateSelected(stateName)"
        >
          {{ $t(stateName) }} ({{ status }})
        </AppButtonTab>
      </section>

      <AtTable
        class="mt-4 md:bg-white rounded-md text-body-1"
        :table-data="listData"
        :cols="cols"
        :pagination="searchState"
        :total="rents.total"
        responsive
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
        :config="tableConfig"
      >
        <template v-slot:card="{ row }">
          <ContractCardMini
            :contract="row"
            class="mb-6 shadow-md w-full py-6 px-4 border bg-base-lvl-3"
          />
        </template>
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex items-center justify-center">
            <UnitTag :status="row.status" />

            <Link
              class="relative inline-block px-5 py-2 ml-4 overflow-hidden font-bold transition rounded-md cursor-pointer hover:bg-primary hover:text-white text-body focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/rents/${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <div class="flex space-x-2">
              <AppButton
                variant="neutral"
                class="hover:bg-success hover:text-white"
                v-if="row.status !== 'CANCELLED'"
                @click="
                  router.visit(`/contacts/${row.client_id}/tenants/rents/${row.id}/renew`)
                "
                :title="$t('extend rent')"
              >
                <IClarityContractLine class="mr-2" />
              </AppButton>
              <AppButton
                variant="neutral"
                class="hover:bg-error hover:text-white"
                v-if="row.status !== 'CANCELLED'"
                @click="
                  router.visit(`/contacts/${row.client_id}/tenants/rents/${row.id}/end`)
                "
                title="Terminar contrato"
              >
                <IMdiFileDocumentRemove class="mr-2" />
              </AppButton>
            </div>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
