<script lang="ts" setup>
import { AtField, AtInput } from "atmosphere-ui";
import { useForm } from "@inertiajs/vue3";
import { ElNotification } from "element-plus";

import AppButton from "@/Components/shared/AppButton.vue";
import UnitForm from "./UnitForm.vue";
import { watch, ref } from "vue";
import { propertyService } from "@/services/properties";
import { IUnit } from "@/Modules/properties/propertyEntity";

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
  index: 0,
  description: "",
  price: 0,
  name: "",
  area: "",
  bedrooms: 1,
  bathrooms: 1,
  images: [],
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
    formData.post(`/properties/${props.property.id}/units/`, {
      onSuccess() {
        ElNotification({
          message: `Unidad ${formData.name} agregada con exito`,
          title: "Unidad agregada",
          type: "success",
        });
        emit("close");
      },
    });
  }
};

const isUploadingImages = ref(false);
const uploadImages = async (rawImages: any[]) => {
  const images: {
    name: string;
    url: string;
  }[] = [];
  isUploadingImages.value = true;
  try {
    for (const image of rawImages) {
      const currentImage = image as { raw: File };
      const uploadedImage = await propertyService.saveImage(
        formData.data() as IUnit,
        currentImage.raw,
        currentImage.raw.name
      );
      if (uploadedImage) {
        images.push({
          name: uploadedImage.name ?? "",
          url: uploadedImage.path,
        });
        console.log(uploadedImage);
      }
    }
  } catch (err) {
    console.log(err);
  }
  isUploadingImages.value = false;
  return images;
};
const updateUnit = async () => {
  formData.images = await uploadImages(formData.images);
  formData
    .transform((data) => ({
      ...data,
    }))
    .put(`/properties/${props.property.id}/units/${props.unit?.id}`, {
      onSuccess() {
        ElNotification({
          message: `Unidad ${formData.name} actualizada con exito`,
          title: "Unidad agregada",
          type: "success",
        });
        emit("close");
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
        formData[field] = newValue[field] ?? formData[field];
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
      <h4 class="font-bold text-xl">{{ $t("Create unit") }}</h4>
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
      <AppButton
        variant="secondary"
        @click="onSubmit()"
        :processing="formData.processing || isUploadingImages"
      >
        Guardar
      </AppButton>
    </footer>
  </main>
</template>
