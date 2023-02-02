<script setup lang="ts">
import { reactive, computed, ref, watch } from "vue";
import { router } from "@inertiajs/core";
// @ts-ignore
import { AtButton, AtTextarea, AtSimpleSelect } from "atmosphere-ui";

import Modal from "@/Components/Modal.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import SectionNav from "@/Components/SectionNav.vue";

import { clientInteractions } from "@/Modules/clients/clientInteractions";
import { documentTypes, DOCUMENT_TYPES } from "@/Modules/clients/constants";
import { useForm } from "@inertiajs/vue3";

const props = defineProps({
  show: {
    type: Boolean,
  },
  maxWidth: {
    type: Number,
  },
  closeable: {
    type: Boolean,
  },
  formData: {
    type: [Object, null],
  },
  type: {
    type: String,
    default() {
      return "lender";
    },
  },
});

const emit = defineEmits(["close", "saved", "update:show"]);

const close = () => {
  clientForm.reset();
  emit("update:show", false);
};

const clientForm = useForm({
  names: "",
  lastnames: "",
  email: "",
  cellphone: "",
  address_details: "",
  dni_type: "DNI",
  dni: "",
  work_name: "",
  work_email: "",
  work_cellphone: "",
  work_address_details: "",
  bank_name: "",
  bank_account_number: "",
  owner_distribution_date: "",
});

const tabs = [
  {
    value: "general",
    label: "Datos Generales",
  },
  {
    value: "work",
    label: "Datos de trabajo",
  },
  {
    value: "accounting",
    label: "Cuenta y Pago",
  },
];
const selectedTab = ref("general");

const currentTab = computed(() => {
  return tabs.find((tab) => tab.value == selectedTab.value)?.label;
});

const documentType = computed(() => {
  return documentTypes.find((doc) => doc.name == clientForm.dni_type).label;
});

const onSubmit = () => {
  clientInteractions
    .create({
      ...clientForm,
      [`is_${props.type}`]: true,
    })
    .then(() => {
      close();
      router.reload();
    })
    .catch((err) => {
      console.log(err);
    });
};

watch(
  () => props.formData,
  (newValue) => {
    Object.keys(clientForm.data()).forEach((field: string) => {
      if (field == "date") {
        clientForm[field] = new Date(newValue[field]);
      } else if (newValue) {
        clientForm[field] = newValue[field];
      }
    });
  },
  { deep: true, immediate: true }
);
</script>

<template>
  <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close()">
    <header
      class="border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
    >
      <h4 class="font-bold text-xl">Crear Contacto</h4>
      <button class="hover:text-danger" @click="close()">
        <IMdiClose />
      </button>
    </header>
    <SectionNav
      class="bg-primary/10 w-full py-1"
      v-model="selectedTab"
      selected-class="bg-base-lvl-3 text-primary font-bold"
      :sections="tabs"
    />
    <main class="pb-4 bg-white sm:p-6 sm:pb-4">
      <h1 class="font-bold text-lg w-full text-center">
        {{ currentTab }}
      </h1>
      <article v-if="selectedTab == 'general'">
        <section class="flex space-x-2">
          <AppFormField label="Nombres" v-model="clientForm.names" required />
          <AppFormField
            label="Apellidos"
            v-model="clientForm.lastnames"
            rounded
            required
          />
        </section>
        <AppFormField label="Dirección" required>
          <AtTextarea v-model="clientForm.address_details" class="border" />
        </AppFormField>
        <section class="flex space-x-2">
          <AppFormField label="Tipo Documento" required>
            <AtSimpleSelect :options="documentTypes" v-model="clientForm.dni_type" />
          </AppFormField>
          <AppFormField :label="documentType" v-model="clientForm.dni" rounded required />
        </section>
        <section class="flex space-x-2">
          <AppFormField label="Email" v-model="clientForm.email" rounded type="email" />
          <AppFormField
            required
            label="Telefono"
            v-model="clientForm.cellphone"
            type="tel"
            rounded
          />
        </section>
      </article>
      <article v-else-if="selectedTab == 'work'">
        <section class="flex space-x-2">
          <AppFormField label="Lugar de trabajo" v-model="clientForm.work_name" />
        </section>
        <AppFormField label="Dirección de trabajo">
          <AtTextarea v-model="clientForm.work_address_details" class="border" />
        </AppFormField>
        <section class="flex space-x-2">
          <AppFormField
            label="Email"
            v-model="clientForm.work_email"
            rounded
            type="email"
          />
          <AppFormField
            label="Telefono"
            v-model="clientForm.work_cellphone"
            type="tel"
            rounded
          />
        </section>
      </article>
      <article v-else-if="selectedTab == 'accounting'">
        <section class="flex space-x-2">
          <AppFormField label="Banco" v-model="clientForm.bank_name" />
          <AppFormField label="No. de cuenta" v-model="clientForm.bank_account_number" />
        </section>
        <section class="flex space-x-2">
          <AppFormField
            label="Dia de generacion de factura"
            v-model="clientForm.owner_distribution_date"
          />
        </section>
      </article>
    </main>
    <footer class="px-6 py-4 space-x-3 text-gray-600 text-right bg-neutral">
      <AtButton @click="close()" class="text-gray"> Cancelar </AtButton>
      <AppButton variant="secondary" @click="onSubmit()"> Guardar </AppButton>
    </footer>
  </Modal>
</template>
