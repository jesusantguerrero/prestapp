<script setup lang="ts">
import { reactive } from "vue";
import { AtButton, AtField, AtInput, AtTextarea, AtSimpleSelect } from "atmosphere-ui";
import { router } from "@inertiajs/core";

import Modal from "@/Components/Modal.vue";
import FormSection from "@/Pages/Rents/Partials/FormSection.vue";
import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";

import { loanFrequencies } from "@/Modules/loans/constants";
import AppButton from "@/Components/shared/AppButton.vue";
import { useForm } from "@inertiajs/vue3";

defineProps({
  show: {
    type: Boolean,
  },
  maxWidth: {
    type: Number,
  },
  closeable: {
    type: Boolean,
  },
});

const emit = defineEmits(["close", "saved", "update:show"]);

const close = () => {
  emit("update:show", false);
};

const loanProductForm = useForm({
  name: "",
  description: "",
  interest_rates: "",
  frequency: "",
  late_fee: 5,
  late_fee_type: "",
  grace_days: 0,
});

const onSubmit = () => {
  loanProductForm
    .transform((data) => ({
      ...data,
      interest_rates: data.interest_rates.split(","),
    }))
    .post(route("loan-products.store"), {
      onSuccess() {
        router.visit(route("loan-products.index"));
      },
    });
};
</script>

<template>
  <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close()">
    <main class="pb-4 bg-white sm:p-6 sm:pb-4">
      <h4 class="text-xl font-bold text-body">Crear tipo de prestamo</h4>
      <section class="flex space-x-2">
        <AtField label="Nombre" class="w-full">
          <AtInput v-model="loanProductForm.name" rounded class="shadow-none" />
        </AtField>
      </section>
      <AtField label="Descripcion" class="w-full">
        <AtTextarea v-model="loanProductForm.description" class="rounded-md border" />
      </AtField>
      <section class="flex space-x-2">
        <AtField label="Intereses Permitidos" class="w-full">
          <AtInput v-model="loanProductForm.interest_rates" rounded class="shadow-none" />
        </AtField>
        <AtField label="Frecuencia" class="w-full">
          <AtSimpleSelect
            :options="loanFrequencies"
            v-model="loanProductForm.frequency"
          />
        </AtField>
      </section>

      <FormSection title="Moras" section-class="space-y-4">
        <section class="mt-4 flex space-x-4">
          <AppButton
            v-if="!loanProductForm.has_late_fees"
            @click="loanProductForm.has_late_fees = true"
          >
            Aplicar Moras</AppButton
          >
          <template v-else>
            <AtField label="Comisión de mora" class="w-full">
              <AtInput v-model="loanProductForm.late_fee" rounded>
                <template #suffix>
                  <TaxTypeSelector v-model="loanProductForm.late_fee_type" />
                </template>
              </AtInput>
            </AtField>
            <AtField label="Dias de gracia" class="w-full">
              <AtInput v-model="loanProductForm.grace_days" rounded />
            </AtField>
          </template>
        </section>
      </FormSection>
    </main>
    <footer class="px-6 py-4 space-x-3 text-gray-600 text-right bg-gray-100">
      <AtButton @click="close()" class="text-gray"> Cancel </AtButton>
      <AtButton class="text-white bg-blue-400 rounded-md" @click="onSubmit()">
        Save
      </AtButton>
    </footer>
  </Modal>
</template>
