<script setup lang="ts">
import { AtButton, AtField, AtInput } from "atmosphere-ui";
import { ElDatePicker, ElDialog, ElNotification } from "element-plus";
import { ref } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import { useForm } from "@inertiajs/vue3";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { useResponsive } from "@/utils/useResponsive";

const props = defineProps({
  modelValue: Boolean,
  loan: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["update:modelValue", "saved"]);

const formData = useForm({
  reason: "",
  due: new Date(),
  date: new Date(),
  amount: props.loan.amount_due,
});

const isLoading = ref(false);
function onSubmit() {
  isLoading.value = true;
  formData.post(`/loans/${props.loan.id}/agreements`, {
    onSuccess() {
      formData.reset();
      emit("saved");
    },
    onError(err) {
      ElNotification({
        type: "error",
        message: err.message ?? "Ha ocurrido un error",
      });
    },
  });
}

function emitChange(value: boolean) {
  emit("update:modelValue", value);
}

const { isMobile } = useResponsive();
</script>

<template>
  <ElDialog
    class="rounded-lg overflow-hidden"
    width="550"
    :fullscreen="isMobile"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <template #header>
      <header
        class="border-b -mx-6 -mt-6 -mr-10 bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
      >
        <h4 class="font-bold text-xl">Agregar acuerdo de pago</h4>
        <button class="hover:text-danger" @click="close()">
          <IMdiClose />
        </button>
      </header>
    </template>
    <div class="-mt-8">
      <section class="flex space-x-4">
        <AppFormField
          class="w-full text-left"
          label="Concepto"
          v-model="formData.reason"
          rounded
        />

        <AppFormField class="w-full text-left" label="Monto Recibido">
          <AtInput
            class="form-control"
            number-format
            v-model="formData.amount"
            rounded
            required
          />
        </AppFormField>
      </section>

      <section class="flex space-x-4 justify-between">
        <AppFormField label="Fecha de creacion" class="w-full">
          <ElDatePicker v-model="formData.date" size="large" class="w-full" rounded />
        </AppFormField>
        <AppFormField label="Fecha de limite" class="w-full">
          <ElDatePicker
            v-model="formData.due"
            size="large"
            width="100%"
            class="w-full"
            rounded
          />
        </AppFormField>
      </section>
    </div>

    <template #footer>
      <div class="space-x-2 flex justify-end dialog-footer">
        <AppButton
          variant="neutral"
          @click="emitChange(false)"
          class="bg-white border rounded-md text-gray"
        >
          Cancel
        </AppButton>
        <AppButton
          @click="onSubmit()"
          variant="secondary"
          :disabled="formData.processing"
          :loading="formData.processing"
        >
          Guardar
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
