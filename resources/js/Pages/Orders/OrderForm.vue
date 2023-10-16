<script lang="ts" setup>
import { useForm, router } from "@inertiajs/vue3";
import { computed } from "vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import OrderFormTemplate from "./Partials/OrderFormTemplate.vue";

import { IClient } from "@/Modules/clients/clientEntity";
import { IInvoice } from "@/Modules/invoicing/entities";
import DropshippingSectionNav from "./Partials/DropshippingSectionNav.vue";

const props = defineProps<{
  invoices: IInvoice;
  client: IClient;
}>();

const invoiceForm = useForm({ ...(props.invoices ?? {}) });

const invoiceData = computed(() => {
  return invoiceForm.data();
});
const onSubmit = (formData: Record<string, any>) => {
  if (invoiceForm.processing) return;
  const url = props.invoice?.id
    ? route("dropshipping.invoices.update", props.invoice)
    : route("dropshipping.invoices.store");
  const method = props.invoice?.id ? "put" : "post";
  invoiceForm
    .transform(() => ({
      ...formData,
    }))
    .submit(method, url, {
      onSuccess() {
        Object.entries(formData).forEach(([field, value]) => {
          invoiceForm[field] = value;
        });
        props.invoice?.id
          ? router.visit(`/invoices/${props.invoice.id}`)
          : router.visit(route("dropshipping.invoices.index"));
      },
    });
};
</script>

<template>
  <AppLayout :title="$t('Create order')">
    <template #header>
      <DropshippingSectionNav />
    </template>

    <main class="w-full px-5 py-10 pb-24 rounded-md rent-form md:pb-4 text-body-1">
      <OrderFormTemplate
        :data="invoices"
        :client="client"
        :is-processing="invoiceForm.processing"
        @submit="onSubmit"
      />
    </main>
  </AppLayout>
</template>

<style scoped>
@media (max-width: 640px) {
  .rent-form {
    height: calc(100vh - 100px);
    overflow: auto;
  }
}
</style>
