<script setup lang="ts">
import { ref, watch } from "vue";
import AppButton from "./shared/AppButton.vue";

defineProps<{
  photoUrl: string;
  errors: string;
  alt: string;
  file?: File;
}>();

const emit = defineEmits("update:file");

const photoPreview = ref<ArrayBuffer | string | null>(null);
const photoInput = ref();
const updatePhotoPreview = () => {
  const photo = photoInput.value.files[0];

  if (!photo) return;

  const reader = new FileReader();

  reader.onload = (e) => {
    photoPreview.value = e.target?.result ?? null;
  };

  emit("update:file", photo ?? null);

  reader.readAsDataURL(photo);
};

const selectNewPhoto = () => {
  photoInput.value.click();
};

const removePhoto = () => {
  if (photoPreview.value) {
    clearPhotoFileInput();
    return;
  }
  emit("deleted");
};

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value.value = null;
  }
};
</script>

<template>
  <section class="col-span-6 sm:col-span-4">
    <!-- Profile Photo File Input -->
    <input ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview" />

    <!-- Current Profile Photo -->
    <div v-show="!photoPreview" class="mt-2">
      <img :src="photoUrl" :alt="alt" class="rounded-full h-20 w-20 object-cover" />
    </div>

    <!-- New Profile Photo Preview -->
    <div v-show="photoPreview" class="mt-2">
      <span
        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
        :style="'background-image: url(\'' + photoPreview + '\');'"
      />
    </div>

    <AppButton class="mt-2 mr-2" type="button" @click.prevent.stop="selectNewPhoto">
      Select A New Photo
    </AppButton>

    <AppButton
      v-if="photoUrl"
      variant="neutral"
      type="button"
      class="mt-2"
      @click.prevent="removePhoto"
    >
      Remove Photo
    </AppButton>
  </section>
</template>
