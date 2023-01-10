<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { AtField } from "atmosphere-ui";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "../Partials/PropertySectionNav.vue";
import { watch, ref } from "vue";

const props = defineProps<{
  category: string;
}>();

const formData = useForm({
  client_id: "",
  client: null,
  category_id: "",
  category: null,
});

const categories = ref([]);
watch(
  () => formData.client,
  async () => {
    categories.value = await getDepositBalance(formData.client.id, props.category);
    formData.category = categories.value.length ? categories.value[0] : null;
  }
);

function getDepositBalance(clientId: number, categoryName: string) {
  return axios
    .get(`/categories/${categoryName}/clients/${clientId}/balance?exclude_credits=true`)
    .then(({ data }) => {
      return data;
    });
}

const getTransactions = () => {
  //   take:
  // 10
  // page:
  // 1
  // filter[client_id]:
  // 1671672
  // filter[account_id]:
  // 1
  // filter[currency]:
  // DOP
  // filter[meta][rules]:
  // true
  // filter[only_deposits]:
  // true
  // fields[userClient]:
  // name
  // fields[user]:
  // fields[transaction]:
  // item_id
  // fields[transaction_payment]:
  // amount,currency,online_id,transaction_id,account_to_id
  // fields[transaction_payment_online]:
  // gateway
  // include:
  // client,online,online.sender,transaction
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
              v-model="formData.category"
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
      </section>
    </main>
  </AppLayout>
</template>
