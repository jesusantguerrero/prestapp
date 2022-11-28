<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";

export interface Props {
  loans: ILoanWithInstallments;
  currentTab: string;
}
withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  summary: "Detalles",
  documents: "Tabla de Amortización",
  transactions: "Pagos",
  details: "Details",
};
</script>

<template>
  <AppLayout title="Prestamos">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="rounded-md px-5 rounded-b-none border-2 shadow-md border-white"
        :resource="loans"
        :title="loans.client.names"
        @create="router.visit('/loans/create')"
      />
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <div>
          Prestamo #{{ loans.id }} para
          {{ loans.client.names + " " + loans.client?.lastnames }}
        </div>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-gray-300': tab == currentTab }"
            :href="`/loans/${loans.id}?tab=${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </div>
      <section class="space-x-8 flex w-full border-t-none rounded-t-none">
        <article class="w-9/12 rounded-md border p-4 shadow-md space-y-2">
          <span> Cliente: {{ loans.client.names }} {{ loans.client.lastnames }} </span>
          <p>
            Monto Prestado:
            {{ loans.amount }}
          </p>
          <p>
            Fecha Primer Pago:
            {{ loans.first_installment_date }}
          </p>
          <p>
            Estatus:
            {{ loans.payment_status }}
          </p>

          <InstallmentTable :installments="loans.installments" accept-payment />
        </article>
        <article class="w-3/12 rounded-md border p-4 shadow-md space-y-2">
          <AppButton class="w-full"> Agregar Pago </AppButton>
          <AppButton class="w-full"> Recibo Multiple </AppButton>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
