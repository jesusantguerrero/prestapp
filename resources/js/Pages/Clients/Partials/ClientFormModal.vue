<script setup lang="ts">
import { ref } from "vue";
// @ts-ignore
import { AtButton } from "atmosphere-ui";

import ResponsiveModal from "@/Components/ResponsiveModal.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import ClientForm from "./ClientForm.vue";
import { ElNotification } from "element-plus";

interface Props {
  show: boolean;
  maxWidth?: string;
  closeable?: boolean;
  formData?: Object | null;
  type?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "tenant",
  formData: null,
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

const onSuccess = () => {
  isLoading.value = false;
  emit("saved");
  close();
};
</script>

<template>
  <ResponsiveModal
    :show="show"
    :max-width="maxWidth"
    :closeable="closeable"
    @close="close()"
  >
    <header
      class="border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
    >
      <h4 class="font-bold text-xl">{{ $t("Create contact") }}</h4>
      <button class="hover:text-danger" @click="close()">
        <IMdiClose />
      </button>
    </header>

    <main class="client-form__container">
      <ClientForm
        :form-data="formData"
        :type="type"
        v-model:isLoading="isLoading"
        ref="clientFormRef"
        @success="onSuccess"
        @error="onError"
        class="client-form"
      />

      <footer
        class="px-6 py-2 md:py-4 flex justify-end space-x-3 text-gray-600 text-right bg-neutral"
      >
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
    </main>
  </ResponsiveModal>
</template>

<style lang="scss">
.client-form__container {
  height: calc(100vh - 61px);
  display: grid;
  grid-template-rows: 1fr 60px;
  grid-template-columns: 1fr;
}

.client-form {
  overflow: auto;
}
</style>
