<script setup lang="ts">
import { ref } from "vue";
// @ts-ignore
import { AtButton } from "atmosphere-ui";

import Modal from "@/Components/Modal.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import ClientForm from "./ClientForm.vue";
import { ElNotification } from "element-plus";

interface Props {
  show: boolean;
  maxWidth?: string;
  closeable?: boolean;
  formData: Object | null;
  type?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "lender",
});

const emit = defineEmits(["close", "saved", "update:show"]);

const isLoading = ref(false);
const clientFormRef = ref();

const close = () => {
  clientFormRef.value.reset();
  isLoading.value = false;
  emit("update:show", false);
};

const onSubmit = () => {
  clientFormRef.value.onSubmit();
};

const onError = (message: string) => {
  ElNotification({
    type: "error",
    message: message,
  });
};
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

    <ClientForm
      :form-data="formData"
      :type="type"
      v-model:isLoading="isLoading"
      ref="clientFormRef"
      @success="$emit('saved')"
      @error="onError"
    />

    <footer class="px-6 py-4 space-x-3 text-gray-600 text-right bg-neutral">
      <AtButton :disabled="isLoading" @click="close()" class="text-gray">
        Cancelar
      </AtButton>
      <AppButton
        :processing="isLoading"
        :disabled="isLoading"
        variant="secondary"
        @click="onSubmit()"
      >
        Guardar
      </AppButton>
    </footer>
  </Modal>
</template>
