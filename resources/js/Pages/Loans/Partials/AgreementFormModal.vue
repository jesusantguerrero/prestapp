<script setup lang="ts">
import { AtButton, AtField, AtInput } from "atmosphere-ui";
import { ElDatePicker, ElDialog, ElNotification } from "element-plus";
import { ref } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import { useForm } from "@inertiajs/vue3";

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
</script>

<template>
  <ElDialog
    title="Agregar acuerdo de pago"
    width="550"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="">
      <section class="flex space-x-4">
        <AtField class="w-full text-left" label="Concepto">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="formData.reason"
            rounded
          />
        </AtField>

        <AtField class="w-full text-left" label="Monto Recibido">
          <AtInput
            class="form-control"
            number-format
            v-model="formData.amount"
            rounded
            required
          />
        </AtField>
      </section>

      <section class="flex space-x-4 justify-between">
        <AtField label="Fecha de creacion" class="w-full">
          <ElDatePicker v-model="formData.date" size="large" class="w-full" rounded />
        </AtField>
        <AtField label="Fecha de limite" class="w-full">
          <ElDatePicker
            v-model="formData.due"
            size="large"
            width="100%"
            class="w-full"
            rounded
          />
        </AtField>
      </section>
    </div>

    <template #footer>
      <div class="space-x-2 dialog-footer">
        <AtButton @click="emitChange(false)" class="bg-white border rounded-md text-gray">
          Cancel
        </AtButton>
        <AppButton
          @click="onSubmit()"
          :disabled="formData.processing"
          :loading="formData.processing"
        >
          Guardar
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
