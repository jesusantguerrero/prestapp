<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
// @ts-ignore
import { AtField, AtInput } from "atmosphere-ui";
import { watch, ref, nextTick } from "vue";
import { ElNotification } from "element-plus";
import axios from "axios";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "../Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";

import { IClientSaved } from "@/Modules/clients/clientEntity";
import { IInvoice } from "@/Modules/loans/loanEntity";

const props = defineProps<{
  category: string;
  client?: IClientSaved;
  refunds?: IInvoice[];
}>();

const formData = useForm({
  client: props.client,
  account: null,
  payments: [],
});

const categories = ref([]);
watch(
  () => formData.client,
  async () => {
    if (formData.client) {
      categories.value = await getDepositBalance(formData.client.id, props.category);
      formData.account = categories.value.length ? categories.value[0] : null;
    }
  },
  { immediate: true }
);

function getDepositBalance(clientId: number, categoryName: string) {
  return axios
    .get(`/categories/${categoryName}/clients/${clientId}/balance?exclude_credits=true`)
    .then(({ data }) => {
      return data;
    });
}

const payments = ref([]);

const mapPayments = (transactions: any[]) => {
  return transactions.reduce((payments, tran) => {
    if (tran?.transactionable) {
      payments.push(
        ...tran.transactionable.payments.map((payment: any) => ({
          ...payment,
          rent_id: tran.transactionable.invoiceable_id,
          client: formData.client,
          // @ts-ignore
          balance: formData.account?.balance ?? 0,
          payment: 0,
        }))
      );
    }
    return payments;
  }, []);
};

const getTransactions = (clientId: number, categoryId: number) => {
  return axios
    .get("/api/transaction-lines", {
      params: {
        limit: 10,
        page: 1,
        filter: {
          account_id: categoryId,
          payee_id: clientId,
        },
        relationships: "transaction.transactionable.payments",
      },
    })
    .then(({ data }) => data.data.map((line: { transaction: any }) => line.transaction));
};

watch(
  () => formData.account,
  async () => {
    if (formData.client && formData.account) {
      const transactions = await getTransactions(
        formData.client?.id,
        formData.account?.id
      );
      payments.value = mapPayments(transactions);
    }
  }
);

const handleChange = () => {};
const customLabel = (category: { name: any; balance: number }) => {
  return `${category.name} (Balance: $${Math.abs(category.balance)})`;
};

interface IRelatedPayments {
  id: number;
  amount: number;
  original_amount: number;
}

const depositForm = useForm({});
const submit = () => {
  const relatedPayments: Record<string, any>[] = payments.value?.reduce(
    (selectedPayments: IRelatedPayments[], doc: any) => {
      if (doc.payment) {
        selectedPayments.push({
          id: doc.id,
          // @ts-ignore
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

  depositForm
    .transform(() => ({
      ...data,
    }))
    .post(`/properties/${rentId}/transactions/refund`, {
      async onSuccess() {
        ElNotification({
          type: "success",
          message: "Reembolso de deposito creado y pagado correctamente",
          title: "Reembolso creado",
        });
        categories.value = await getDepositBalance(formData.client.id, props.category);
        formData.account = categories.value.length ? categories.value[0] : null;
      },
    });
};

const tableCols = [
  {
    name: "id",
    label: "Pago #",
  },
  {
    name: "client.display_name",
    label: "Cliente",
  },
  {
    name: "method_name",
    label: "Metodo de pago",
  },
  {
    name: "balance",
    label: "Balance",
    render(row: { balance: number }) {
      return Math.abs(row.balance);
    },
  },
  {
    name: "payment",
    label: "Monto de reembolso",
  },
];
</script>

<template>
  <AppLayout title="Rembolso de depositos">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="py-10 mx-auto sm:px-6 lg:px-8">
      <section class="rounded-md bg-base-lvl-3">
        <header class="flex space-x-4 w-full px-4">
          <AtField label="Cliente" class="w-full">
            <BaseSelect
              v-model="formData.client"
              endpoint="/api/clients"
              placeholder="selecciona un cliente"
              label="display_name"
              track-by="id"
            />
          </AtField>
          <AtField label="Categoria" class="w-full">
            <BaseSelect
              v-model="formData.account"
              :track-by="id"
              :options="categories"
              :hide-selected="false"
              :custom-label="customLabel"
              placeholder="selecciona una categoria"
            />
          </AtField>
        </header>
        <article class="px-4">
          <BaseTable :cols="tableCols" :table-data="payments">
            <template v-slot:id="{ scope: { row } }">
              <div class="items-center space-x-2 d-flex">
                <ElCheckbox @change="handleChange($event, row)" />
                <span> {{ row.concept }} #{{ row.id }}</span>
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
        <footer class="w-full flex justify-end space-x-4 pb-4 px-4">
          <AppButton :disabled="depositForm.processing" variant="neutral">
            Cancel</AppButton
          >
          <AppButton
            @click="submit"
            :processing="depositForm.processing"
            :disabled="depositForm.processing"
            variant="secondary"
          >
            Guardar y Pagar</AppButton
          >
        </footer>
        <article class="px-4">
          <InvoiceCard v-for="invoice in refunds" :invoice="invoice" :key="invoice.id" />
        </article>
      </section>
    </main>
  </AppLayout>
</template>
