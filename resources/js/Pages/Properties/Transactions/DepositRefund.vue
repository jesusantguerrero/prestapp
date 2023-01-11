<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { AtField, AtInput } from "atmosphere-ui";
import { watch, ref, computed } from "vue";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import BaseTable from "@/Components/shared/BaseTable.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "../Partials/PropertySectionNav.vue";
import axios from "axios";
import AppButton from "@/Components/shared/AppButton.vue";
import { ElNotification } from "element-plus";

const props = defineProps<{
  category: string;
}>();

const formData = useForm({
  client: null,
  account: null,
  payments: [],
});

const categories = ref([]);
watch(
  () => formData.client,
  async () => {
    categories.value = await getDepositBalance(formData.client.id, props.category);
    formData.account = categories.value.length ? categories.value[0] : null;
  }
);

function getDepositBalance(clientId: number, categoryName: string) {
  return axios
    .get(`/categories/${categoryName}/clients/${clientId}/balance?exclude_credits=true`)
    .then(({ data }) => {
      return data;
    });
}

const transactions = ref([]);
const payments = computed(() => {
  return transactions.value.reduce((payments, tran) => {
    console.log(tran);
    if (tran?.transactionable) {
      payments.push(
        ...tran.transactionable.payments.map((payment) => ({
          ...payment,
          rent_id: tran.transactionable.invoiceable_id,
          client: formData.client,
          balance: formData.account.balance,
          payment: 0,
        }))
      );
    }
    return payments;
  }, []);
});
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
    .then(({ data }) => data.data.map((line) => line.transaction));
};
watch(
  () => formData.account,
  async () => {
    transactions.value = await getTransactions(formData.client.id, formData.account.id);
  }
);

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
              :custom-label="
                (category) => {
                  return `${category.name} (Balance: $${Math.abs(category.balance)})`;
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
                name: 'id',
                label: 'Pago #',
              },
              {
                name: 'client.display_name',
                label: 'Cliente',
              },
              {
                name: 'method_name',
                label: 'Metodo de pago',
              },
              {
                name: 'balance',
                label: 'Balance',
                render(row) {
                  return Math.abs(row.balance);
                },
              },
              {
                name: 'payment',
                label: 'Monto de reembolso',
              },
            ]"
            :table-data="payments"
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
