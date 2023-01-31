<script setup lang="ts">
import { useForm, router } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { IProperty } from "@/Modules/properties/propertyEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import RentFormTemplate from "./Partials/RentFormTemplate.vue";

defineProps<{
  rentData: ILoan[];
  properties: IProperty[];
  clients: IClient[];
}>();

const rentForm = useForm({});
const onSubmit = (formData: Record<string, any>) => {
  rentForm
    .transform(() => ({
      ...formData,
    }))
    .post(route("rents.store"), {
      onSuccess() {
        router.visit(`/rents`);
      },
    });
};
</script>

<template>
  <AppLayout title="Crear contrato">
    <main class="w-full bg-white px-5 py-5 rounded-md text-body-1">
      <RentFormTemplate
        :data="rentForm"
        :current-step="step"
        :is-processing="rentForm.processing"
        @submit="onSubmit"
      />
    </main>
  </AppLayout>
</template>
