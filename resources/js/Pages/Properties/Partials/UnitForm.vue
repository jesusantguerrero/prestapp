<script setup>
import { watch } from "vue";
import { AtButton, AtField, AtInput, AtSelect, AtTextarea } from "atmosphere-ui";
import { useForm } from "@inertiajs/vue3";
import { ElNotification } from "element-plus";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import { propertyTypes } from "@/Modules/properties/constants";

const props = defineProps({
  property: {
    type: Object,
  },
});

const emit = defineEmits(["close"]);

const formData = useForm({
  index: 0,
  description: "",
  price: 0,
  name: "",
});

// api calls
const onSubmit = () => {
  if (!formData.name || !formData.price) {
    ElNotification({
      type: "Error",
      message: "Debe llenar los campos de nombre y precio de renta al menos",
      title: "Campos requeridos",
    });
    return;
  }
  formData.post(`/properties/${props.property.id}/units/`, {
    onsuccess() {
      emit("close");
    },
  });
};
</script>

<template>
  <main class="mx-auto text-gray-500 sm:px-6 lg:px-8">
    <header class="font-bold py-4 text-xl flex">
      <h4>Agregar unidad</h4>
      <Button>
        <IMdiClose />
      </Button>
    </header>
    <AtField class="w-full" label="Nombre">
      <AtInput v-model="formData.name" class="w-full" rounded required />
    </AtField>
    <section class="space-x-4 grid grid-cols-[196px,1fr]">
      <AtField class="w-full" label="Precio de Renta">
        <AtInput v-model="formData.price" class="w-full" rounded number-format required />
      </AtField>
      <AtField label="Descripción">
        <AtTextarea
          v-model="formData.description"
          class="w-full p-2 border focus:outline-none"
          placeholder="Descripcion de la propiedad"
        />
      </AtField>
    </section>
    <footer class="justify-end w-full flex space-x-4 py-1">
      <AppButton variant="error" @click="$emit('close')"> Cerrar </AppButton>
      <AppButton @click="onSubmit()"> Guardar </AppButton>
    </footer>
  </main>
</template>
