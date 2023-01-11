<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { AtField, AtInput } from "atmosphere-ui";
import { watch, ref, computed, reactive } from "vue";
import { ElNotification } from "element-plus";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "./Partials/LoanSectionNav.vue";
import axios from "axios";
import AppButton from "@/Components/shared/AppButton.vue";

import { formatMoney } from "@/utils";
import { useSectionFilters } from "@/Modules/_app/useSectionFilters";
import { router } from "@inertiajs/core";
import { IClientSaved } from "@/Modules/clients/clientEntity";
import { ILoan } from "@/Modules/loans/loanEntity";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";

const props = defineProps<{
  invoices: ILoanInstallment[];
  loans: ILoan[];
  clients: IClientSaved[];
}>();

const formData = useForm({
  client: null,
  account: null,
  payments: [],
});

const filters = useSectionFilters(["client", "loan"], router);

const handleChange = () => {};

interface IRelatedPayments {
  id: number;
  amount: number;
  original_amount: number;
}
const submit = () => {
  const relatedPayments = payments.value?.reduce(
    (selectedPayments: IRelatedPayments[], doc) => {
      if (doc.amount) {
        selectedPayments.push({
          id: doc.id,
          rent_id: doc.rent_id,
          amount: doc.payment,
          original_amount: doc.amount,
        });
      }
      return selectedPayments;
    },
    []
  );
  if (!relatedPayments.length) {
    ElNotification({
      type: "error",
      message: "Seleccione al menos un pago",
      title: "Error de pago",
    });
    return;
  }
  const rentId = relatedPayments[0].rent_id;
  const data = {
    client_id: formData.client?.id,
    account_id: formData.account?.id,
    rent_id: rentId,
    payments: relatedPayments,
    total: relatedPayments.reduce(
      (total, payment) => total + parseFloat(payment.amount),
      0
    ),
  };
  axios.post(`/properties/${rentId}/transactions/refund`, data).then(({ data }) => {
    // todo: launch payment modal or doit automatically in backend?
    console.log(data);
  });
};
</script>

<template>
  <AppLayout title="Centro de pago de prestamos">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-10 mx-auto sm:px-6 lg:px-8">
      <section class="rounded-md bg-base-lvl-3">
        <header class="flex space-x-4 w-full px-4">
          <AtField label="Cliente" class="w-full">
            <BaseSelect
              v-model="filters.client"
              :options="clients"
              placeholder="selecciona un cliente"
              label="display_name"
              track-by="id"
            />
          </AtField>
          <AtField label="Categoria" class="w-full">
            <BaseSelect
              v-model="filters.loan"
              :track-by="id"
              :options="loans"
              :hide-selected="false"
              :custom-label="
                (category) => {
                  return `Prestamo ${category.id} (${category.amount}) (debt: $${category.debt})`;
                }
              "
              placeholder="selecciona una categoria"
            />
          </AtField>
        </header>
        <article class="px-4 pb-10">
          <BaseTable
            :cols="[
              {
                name: 'item',
                label: '',
              },
              {
                name: 'loan_id',
                label: 'Prestamo #',
              },
              {
                name: 'client.display_name',
                label: 'Cliente',
              },
              {
                name: 'amount',
                label: 'Monto del pagare',
                render(row) {
                  return formatMoney(row.amount);
                },
              },
              {
                name: 'amount_paid',
                label: 'Balance',
                render(row) {
                  return formatMoney(row.amount_paid - row.amount);
                },
              },
              {
                name: 'payment',
                label: 'Monto de reembolso',
              },
            ]"
            :table-data="invoices.data"
          >
            <template v-slot:item="{ scope: { row } }">
              <div class="items-center space-x-2 d-flex">
                <ElCheckbox @change="handleChange($event, row)" />
                <span> {{ row.name }}</span>
              </div>
            </template>

            <template v-slot:payment="{ scope: { row } }">
              <AtInput
                class="rounded-md shadow-none border-body-1/10"
                v-model="row.payment"
                :number-format="true"
              />
            </template>
          </BaseTable>
        </article>
        <footer class="w-full text-right space-x-4 pb-4">
          <AppButton> Cancel</AppButton>
          <AppButton @click="submit"> Guardar y Pagar</AppButton>
        </footer>
      </section>
    </main>
  </AppLayout>
</template>
