<script setup lang="ts">
import { router } from "@inertiajs/core";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";

defineProps<{
  loans: ILoanWithInstallments;
}>();
</script>

<template>
  <AppLayout title="Prestamos">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="rounded-md"
        :resource="loans"
        :title="loans.client.names"
        @create="router.visit('/loans/create')"
      />
      <section class="mt-4 space-x-8 flex w-full">
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
