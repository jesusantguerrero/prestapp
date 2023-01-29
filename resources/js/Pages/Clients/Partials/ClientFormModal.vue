<script setup lang="ts">
import { reactive, computed } from "vue";
// @ts-ignore
import { AtButton, AtField, AtInput, AtTextarea, AtSimpleSelect } from "atmosphere-ui";
import Modal from "@/Components/Modal.vue";

import { clientInteractions } from "@/Modules/clients/clientInteractions";
import { documentTypes, DOCUMENT_TYPES } from "@/Modules/clients/constants";
import { router } from "@inertiajs/core";
import AppButton from "@/Components/shared/AppButton.vue";

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
  type: {
    type: String,
    default() {
      return "lender";
    },
  },
});

const emit = defineEmits(["close", "saved", "update:show"]);

const close = () => {
  emit("update:show", false);
};

const clientForm = reactive({
  names: "",
  lastnames: "",
  email: "",
  cellphone: "",
  address_details: "",
  dni_type: "DNI",
  dni: "",
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
</script>

<template>
  <Modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close()">
    <header
      class="border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
    >
      <h4 class="font-bold text-xl">Crear Contacto {{ type }}</h4>
      <button class="hover:text-danger" @click="close()">
        <IMdiClose />
      </button>
    </header>
    <main class="pb-4 bg-white sm:p-6 sm:pb-4">
      <section class="flex space-x-2">
        <AtField label="Nombres" class="w-full">
          <AtInput
            v-model="clientForm.names"
            rounded
            class="bg-neutral/20 shadow-none border-neutral hover:border-secondary/60"
          />
        </AtField>
        <AtField label="Apellidos" class="w-full">
          <AtInput v-model="clientForm.lastnames" rounded />
        </AtField>
      </section>
      <AtField label="Dirección" class="w-full">
        <AtTextarea v-model="clientForm.address_details" class="border" />
      </AtField>
      <section class="flex space-x-2">
        <AtField label="Tipo Documento" class="w-full">
          <AtSimpleSelect :options="documentTypes" v-model="clientForm.dni_type" />
        </AtField>
        <AtField :label="documentType" class="w-full">
          <AtInput v-model="clientForm.dni" rounded />
        </AtField>
      </section>
      <section class="flex space-x-2">
        <AtField label="Email" class="w-full">
          <AtInput v-model="clientForm.email" rounded type="email" />
        </AtField>
        <AtField label="Telefono" class="w-full">
          <AtInput v-model="clientForm.cellphone" type="tel" rounded />
        </AtField>
      </section>
    </main>
    <footer class="px-6 py-4 space-x-3 text-gray-600 text-right bg-neutral">
      <AtButton @click="close()" class="text-gray"> Cancelar </AtButton>
      <AppButton variant="secondary" @click="onSubmit()"> Guardar </AppButton>
    </footer>
  </Modal>
</template>
