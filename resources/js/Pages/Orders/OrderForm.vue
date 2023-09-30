<script lang="ts" setup>
import { useForm, router } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";

import { IProperty, IRent, IUnit } from "@/Modules/properties/propertyEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import OrderFormTemplate from "./Partials/OrderFormTemplate.vue";

const props = defineProps<{
  rents: IRent;
  properties: IProperty[];
  client: IClient;
  unit: IUnit;
  property: IProperty;
}>();

const rentForm = useForm({ ...(props.rents ?? {}) });

const onSubmit = (formData: Record<string, any>) => {
  if (rentForm.processing) return;
  const url = props.rents?.id
    ? route("orders.update", props.rents)
    : route("orders.store");
  const method = props.rents?.id ? "put" : "post";
  rentForm
    .transform(() => ({
      ...formData,
    }))
    .submit(method, url, {
      onSuccess() {
        props.rents?.id
          ? router.visit(`/orders/${props.rents.id}`)
          : router.visit(`/orders`);
      },
    });
};
</script>

<template>
  <AppLayout :title="$t('Create order')">
    <main
      class="w-full rent-form pb-24 md:pb-4 bg-white px-5 py-5 rounded-md text-body-1"
    >
      <OrderFormTemplate
        :data="rentForm"
        :client="client"
        :property="property"
        :unit="unit"
        :current-step="step"
        :is-processing="rentForm.processing"
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
