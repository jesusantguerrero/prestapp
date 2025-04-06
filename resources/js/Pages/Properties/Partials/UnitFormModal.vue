<script lang="ts" setup>
import { AtField, AtInput } from "atmosphere-ui";
import { useForm } from "@inertiajs/vue3";
import { ElNotification } from "element-plus";

import AppButton from "@/Components/shared/AppButton.vue";
import UnitForm from "./UnitForm.vue";
import { computed, watch } from "vue";
import { useI18n } from "vue-i18n";

const props = defineProps({
  property: {
    type: Object,
  },
  unit: {
    type: Object,
  },
});

const emit = defineEmits(["close"]);

const formData = useForm({
  id: props.unit?.id,
  property_id: props.property?.id,
  index: 0,
  description: "",
  price: 0,
  name: "",
  area: "",
  bedrooms: 1,
  bathrooms: 1,
});

const close = () => {
  emit('close');
}

const { t } = useI18n();
const formTitle = computed(() => {
  return props.unit?.id ? t('Update unit') : t('Create unit');
});

// api calls
const onSubmit = () => {
  if (formData.processing) return;
  if (!formData.name || !formData.price) {
    ElNotification({
      type: "error",
      message: "Debe llenar los campos de nombre y precio de renta al menos",
      title: "Campos requeridos",
    });
    return;
  }
  if (props.unit?.id) {
    updateUnit();
  } else {
    formData.post(`/properties/${props.property?.id}/units/`, {
      onSuccess() {
        ElNotification({
          message: `Unidad ${formData.name} agregada con exito`,
          title: "Unidad agregada",
          type: "success",
        });
        close();
      },
    });
  }
};
const updateUnit = () => {
  formData.put(`/properties/${props.property?.id}/units/${props.unit?.id}`, {
    onSuccess() {
      ElNotification({
        message: `Unidad ${formData.name} actualizada con exito`,
        title: "Unidad agregada",
        type: "success",
      });
      close();
    },
  });
};

watch(
  () => props.unit,
  (newValue) => {
    if (!newValue) return;
    Object.keys(formData.data()).forEach((field: string) => {
      if (field == "date") {
        // @ts-ignore
        formData[field] = new Date(newValue[field]);
      } else if (newValue) {
        // @ts-ignore
        formData[field] = newValue[field];
      }
    });
  },
  { deep: true, immediate: true }
);
</script>

<template>
  <main class="mx-auto text-gray-500">
    <header
      class="border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
    >
      <h4 class="font-bold text-xl">{{ formTitle }}</h4>
      <button class="hover:text-danger" @click="close()">
        <IMdiClose />
      </button>
    </header>
    <section class="sm:px-6 lg:px-8">
      <UnitForm :unit="formData" :property="property" />
    </section>
    <footer
      class="px-6 py-4 flex justify-end space-x-3 text-gray-600 text-right bg-neutral"
    >
      <AppButton variant="neutral" @click="close()"> Cerrar </AppButton>
      <AppButton variant="secondary" @click="onSubmit()"> Guardar </AppButton>
    </footer>
  </main>
</template>
