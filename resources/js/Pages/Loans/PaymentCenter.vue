<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { AtInput } from "atmosphere-ui";
import { watch, ref } from "vue";
import { ElNotification } from "element-plus";
import axios from "axios";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "./Partials/LoanSectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";

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
  currentTab: string;
}>();

const formData = useForm({
  client: null,
  account: null,
  payments: [],
});

const filters = useSectionFilters(["client", "loan"], router);

const handleChange = (value: boolean, row: Record<string, any>) => {
  row.payment = row.amount_due;
};

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

const sections = {
  pending: {
    label: "Pendientes",
  },
  all: {
    label: "Todos",
  },
  payments: {
    label: "Pagos",
  },
};

const currentTab = ref(props.currentTab);

watch(currentTab, () => {
  router.reload({
    data: {
      tab: currentTab.value,
    },
  });
});
</script>

<template>
  <AppLayout title="Centro de pago de prestamos">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <ButtonGroup :values="sections" v-model="currentTab" class="hidden md:flex" />
        </template>
      </PropertySectionNav>
    </template>

    <main class="py-10 mx-auto md-24 md:mt-0 sm:px-6 lg:px-8">
      <ButtonGroup
        :values="sections"
        v-model="currentTab"
        class="w-full mt-4 md:hidden"
      />
      <header class="flex space-x-4 w-full px-4 rounded-md bg-base-lvl-3">
        <AppFormField label="Cliente" class="w-full">
          <BaseSelect
            v-model="filters.client"
            :options="clients"
            placeholder="selecciona un cliente"
            label="display_name"
            track-by="id"
          />
        </AppFormField>
        <AppFormField label="Categoria" class="w-full">
          <BaseSelect
            v-model="filters.loan"
            track-by="id"
            :options="loans"
            :hide-selected="false"
            :custom-label="
              (category) => {
                return `Prestamo ${category.id} (${category.amount}) (debt: $${category.debt})`;
              }
            "
            placeholder="selecciona una categoria"
          />
        </AppFormField>
      </header>

      <section class="rounded-md bg-base-lvl-3 mt-4">
        <article class="px-4 pb-10">
          <BaseTable
            :cols="[
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
                render(row: Record<string, any>) {
                  return formatMoney(row.amount);
                },
              },
              {
                name: 'amount_paid',
                label: 'Balance',
                render(row: Record<string, any>) {
                  return formatMoney(row.amount_due);
                },
              },
              {
                name: 'payment',
                label: 'Monto de reembolso',
              },
            ]"
            :table-data="invoices.data"
          >
            <template v-slot:loan_id="{ scope: { row } }">
              <div
                class="items-center space-x-2 flex cursor-pointer"
                @click="handleChange(!row.amount, row)"
              >
                <ElCheckbox :model-value="!!row.payment" />
                <span> Prestamo #{{ row.loan_id }}</span>
              </div>
            </template>

            <template v-slot:payment="{ scope: { row } }">
              <AtInput
                v-if="row.amount_due > 0"
                class="rounded-md shadow-none border-body-1/10"
                v-model="row.payment"
                :number-format="true"
              />
              <span>
                {{ row.amount_due }}
              </span>
            </template>
          </BaseTable>
        </article>
        <footer
          class="w-full text-right px-4 md:px-0 space-x-4 pb-4"
          v-if="currentTab == 'pending' && filters.client"
        >
          <AppButton variant="neutral"> Cancelar</AppButton>
          <AppButton variant="secondary" @click="submit"> Guardar y Pagar</AppButton>
        </footer>
      </section>
    </main>
  </AppLayout>
</template>
