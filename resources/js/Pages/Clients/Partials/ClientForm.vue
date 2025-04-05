<script setup lang="ts">
import { computed, ref, watch } from "vue";
// @ts-ignore
import { AtTextarea } from "atmosphere-ui";

import AppFormField from "@/Components/shared/AppFormField.vue";
import SectionNav from "@/Components/SectionNav.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import { clientInteractions } from "@/Modules/clients/clientInteractions";
import { documentTypes } from "@/Modules/clients/constants";
import { useForm } from "@inertiajs/vue3";

interface Props {
  formData: Object | null;
  type: string;
  disabled: boolean;
  isLoading: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: "lender",
});

const emit = defineEmits(["success", "error", "update:isLoading"]);

const clientForm = useForm({
  id: null,
  names: "",
  lastnames: "",
  email: "",
  cellphone: "",
  address_details: "",
  dniType: documentTypes[0],
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

const clientTypes = [
  {
    value: "owner",
    label: "Propietario",
  },
  {
    value: "tenant",
    label: "Inquilino",
  },
];
const clientTypeId = ref(props.type);

const selectedClientType = computed({
  set(type: Record<string, any>) {
    clientTypeId.value = type?.value ?? props.type;
  },
  get() {
    return clientTypes.filter((type) => clientTypeId.value == type.value);
  },
});

const onSubmit = async () => {
  if (props.isLoading) return;
  emit("update:isLoading", true);
  await clientInteractions
    .create({
      ...clientForm,
      [`is_${clientTypeId.value}`]: true,
    })
    .then(() => {
      emit("success");
    })
    .catch((err) => {
      emit("update:isLoading", false);
      emit("error", err);
    })
    .finally(() => {
      emit("update:isLoading", false);
    });

  emit("update:isLoading", false);
};

defineExpose({
  reset: clientForm.reset,
  onSubmit,
});

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
  <section>
    <SectionNav
      class="bg-primary/5 w-full"
      v-model="selectedTab"
      selected-class="bg-base-lvl-3 text-primary font-bold"
      :sections="tabs"
    />
    <main class="pb-4 bg-white sm:p-6 sm:pb-4">
      <h1 class="font-bold text-lg w-full text-center">
        {{ currentTab }}
      </h1>
      <article v-if="selectedTab == 'general'" class="px-2 md:px-0">
        <section class="flex flex-col md:flex-row md:space-x-2">
          <AppFormField
            label="Nombres"
            v-model="clientForm.names"
            required
            :disabled="disabled"
          />
          <AppFormField
            label="Apellidos"
            v-model="clientForm.lastnames"
            rounded
            required
            :disabled="disabled"
          />
        </section>
        <AppFormField label="Dirección" required>
          <AtTextarea
            v-model="clientForm.address_details"
            class="border px-4 py-2 bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60"
            :disabled="disabled"
          />
        </AppFormField>
        <AppFormField label="Tipo de cliente " required :disabled="disabled">
          <BaseSelect
            v-model="selectedClientType"
            :options="clientTypes"
            track-by="value"
            label="label"
            placeholder="Tipo de cliente"
            :disabled="disabled"
          />
        </AppFormField>
        <section class="flex flex-col md:flex-row md:space-x-2">
          <AppFormField label="Tipo Documento" required>
            <BaseSelect
              :options="documentTypes"
              track-by="name"
              v-model="clientForm.dniType"
              :disabled="disabled"
            />
          </AppFormField>
          <AppFormField :label="documentType" v-model="clientForm.dni" rounded required />
        </section>
        <section class="flex flex-col md:flex-row md:space-x-2">
          <AppFormField
            label="Email"
            v-model="clientForm.email"
            rounded
            type="email"
            :disabled="disabled"
          />
          <AppFormField
            required
            label="Telefono"
            v-model="clientForm.cellphone"
            type="tel"
            rounded
            :disabled="disabled"
          />
        </section>
      </article>
      <article v-else-if="selectedTab == 'work'" class="px-2 md:px-0">
        <section class="flex space-x-2">
          <AppFormField
            label="Lugar de trabajo"
            v-model="clientForm.work_name"
            :disabled="disabled"
          />
        </section>
        <AppFormField label="Dirección de trabajo">
          <AtTextarea
            v-model="clientForm.work_address_details"
            class="border"
            :disabled="disabled"
          />
        </AppFormField>
        <section class="flex flex-col md:flex-row md:space-x-2">
          <AppFormField
            label="Email"
            v-model="clientForm.work_email"
            rounded
            type="email"
            :disabled="disabled"
          />
          <AppFormField
            label="Telefono"
            v-model="clientForm.work_cellphone"
            type="tel"
            rounded
            :disabled="disabled"
          />
        </section>
      </article>
      <article v-else-if="selectedTab == 'accounting'">
        <section class="flex space-x-2">
          <AppFormField
            label="Banco"
            v-model="clientForm.bank_name"
            :disabled="disabled"
          />
          <AppFormField
            label="No. de cuenta"
            v-model="clientForm.bank_account_number"
            :disabled="disabled"
          />
        </section>
        <section class="flex space-x-2">
          <AppFormField
            label="Dia de generacion de factura"
            v-model="clientForm.owner_distribution_date"
            :disabled="disabled"
          />
        </section>
      </article>
    </main>
  </section>
</template>
