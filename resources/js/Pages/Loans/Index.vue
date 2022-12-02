<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import { ILoan } from "../../Modules/loans/loanEntity";
import { AtButton } from "atmosphere-ui";
import { computed } from "vue";
import cols from "./cols";
import AtTable from "../../Components/AtTable.vue";
import AppButton from "../../Components/shared/AppButton.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps<{
  loans: [ILoan[] | Object];
}>();

const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});
</script>

<template>
  <AppLayout title="Prestamos">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="rounded-md"
        @create="router.visit('/loans/create')"
      />
      <section class="mt-4">
        <AtTable :table-data="listData" :cols="cols">
          <template v-slot:actions="{ scope: { row } }" class="flex">
            <div class="flex">
              <Link
                class="relative px-5 py-2 overflow-hidden focus:outline-none hover:bg-opacity-80 border transition font-bold rounded-md min-w-max inline-block bg-primary text-white"
                :href="`/loans/${row.id}`"
              >
                Edit</Link
              >
              <AppButton> Delete</AppButton>
            </div>
          </template>
        </AtTable>
      </section>
    </main>
  </AppLayout>
</template>
