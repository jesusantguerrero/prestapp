<script lang="ts" setup>
import { AtField, AtInput } from "atmosphere-ui";
import { useForm } from "@inertiajs/vue3";
import { ElNotification } from "element-plus";

import AppButton from "@/Components/shared/AppButton.vue";
import UnitForm from "./UnitForm.vue";

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
  area: "",
  bedrooms: 1,
  bathrooms: 1,
});

// api calls
const onSubmit = () => {
  if (!formData.name || !formData.price) {
    ElNotification({
      type: "error",
      message: "Debe llenar los campos de nombre y precio de renta al menos",
      title: "Campos requeridos",
    });
    return;
  }
  formData.post(`/properties/${props.property.id}/units/`, {
    onSuccess() {
      emit("close");
    },
  });
};
</script>

<template>
  <main class="mx-auto text-gray-500">
    <header
      class="border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
    >
      <h4 class="font-bold text-xl">Crear Contacto</h4>
      <button class="hover:text-danger" @click="close()">
        <IMdiClose />
      </button>
    </header>
    <section class="sm:px-6 lg:px-8">
      <AtField class="w-full" label="Nombre">
        <AtInput v-model="formData.name" class="w-full" rounded required />
      </AtField>
      <UnitForm :unit="formData" />
    </section>
    <footer
      class="px-6 py-4 flex justify-end space-x-3 text-gray-600 text-right bg-neutral"
    >
      <AppButton variant="neutral" @click="$emit('close')"> Cerrar </AppButton>
      <AppButton variant="secondary" @click="onSubmit()"> Guardar </AppButton>
    </footer>
  </main>
</template>