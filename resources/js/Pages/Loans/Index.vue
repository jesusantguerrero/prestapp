<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { computed, toRefs } from "vue";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
// @ts-ignore: its my template
import AtTable from "@/Components/AtTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { useServerSearch } from "@/utils/useServerSearch";

import { ILoan } from "@/Modules/loans/loanEntity";
import cols from "./cols";
import { formatMoney } from "@/utils";
import { ElMessageBox } from "element-plus";

interface IPaginatedData {
  data: ILoan[];
}

const props = defineProps<{
  loans: ILoan[] | IPaginatedData;
  serverSearchOptions: Object;
}>();

const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});

const deleteLoan = async (loan: ILoan) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el prestamo ${loan.id} por ${loan.amount} a ${loan.client?.fullName}?`,
    "Eliminar prestamo"
  );
  if (isValid) {
    router.delete(route("loans.destroy", loan), {
      onSuccess() {
        router.reload();
      },
    });
  }
};

const { serverSearchOptions } = toRefs(props);
const { state, updateSearch, executeSearch, reset } = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/loans?${finalUrl}`);
  },
  {
    manual: true,
  }
);
</script>

<template>
  <AppLayout title="Prestamos">
    <template #header>
      <LoanSectionNav>
        <template #actions>
          <AppSearch
            v-model.lazy="state.search"
            v-model:filters="state.filters"
            v-model:sorts="state.sorts"
            class="w-full"
            :has-filters="true"
            @clear="reset()"
            @blur="executeSearch"
          />
          <AppButton variant="inverse" @click="router.visit('/loans/create')">
            Nuevo prestamo
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <main class="pt-16">
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
      >
        <template v-slot:actions="{ scope: { row } }">
          <div class="flex">
            <Link
              class="relative inline-block px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/loans/${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <AppButton
              variant="neutral"
              class="hover:text-error transition hover:border-red-400"
              @click="deleteLoan(row)"
            >
              <IMdiTrash />
            </AppButton>
          </div>
        </template>
        <template v-slot:amount_due="{ scope: { row } }">
          <div class="font-bold">
            <p class="font-bold text-green-500">
              {{ formatMoney(row.amount_due) }}
            </p>
          </div>
        </template>
      </AtTable>
    </main>
  </AppLayout>
</template>
