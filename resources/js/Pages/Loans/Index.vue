<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { computed, toRefs } from "vue";

// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
// @ts-ignore: its my template
import AtTable from "@/Components/shared/BaseTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";

import { ILoan } from "@/Modules/loans/loanEntity";
import cols from "./cols";
import { ElMessageBox } from "element-plus";
import LoanCard from "./Partials/LoanCard.vue";
import ButtonCircle from "@/Components/mobile/ButtonCircle.vue";
import { useResponsive } from "@/utils/useResponsive";

interface IPaginatedData {
  data: ILoan[];
}

const props = defineProps<{
  loans: ILoan[] | IPaginatedData;
  serverSearchOptions: IServerSearchData;
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
const { isMobile } = useResponsive();
</script>

<template>
  <AppLayout title="Prestamos">
    <template #header>
      <LoanSectionNav />
    </template>

    <main class="pt-16">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="state.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <AppButton
          variant="inverse"
          @click="router.visit('/loans/create')"
          class="hidden md:flex items-center"
        >
          Nuevo prestamo
        </AppButton>
      </section>
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1 mt-4"
        :layout="isMobile ? 'grid' : 'table'"
      >
        <template v-slot:actions="{ scope: { row } }">
          <div class="flex justify-end">
            <Link
              class="relative inline-block cursor-pointer hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/loans/${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <AppButton
              variant="neutral"
              class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
              @click="deleteLoan(row)"
            >
              <IMdiTrash />
            </AppButton>
          </div>
        </template>
        <template v-slot:card="{ row }">
          <LoanCard :loan="row" />
        </template>
      </AtTable>
      <ButtonCircle
        class="fixed bottom-16 right-5"
        @click="router.visit('/loans/create')"
        v-if="isMobile"
      >
        <IMdiPlus />
      </ButtonCircle>
    </main>
  </AppLayout>
</template>
