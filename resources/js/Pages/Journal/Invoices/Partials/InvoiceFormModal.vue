<script setup lang="ts">
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { format as formatDate } from "date-fns";
import { ElDatePicker, ElDialog, ElNotification } from "element-plus";
import { inject, ref, watch, computed } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";

import { MathHelper } from "@/Modules/loans/mathHelper";
import { paymentMethods } from "@/Modules/loans/constants";
import AppFormField from "@/Components/shared/AppFormField.vue";

const defaultFormData = {
  amount: 0,
  account_id: "",
};

const props = defineProps({
  modelValue: {
    type: Boolean,
  },
  defaultConcept: {
    type: String,
    required: true,
  },
  defaultAmount: {
    type: Number,
    required: true,
  },
  payment: {
    type: [Object, null],
  },
  due: {
    type: Number,
    default: 0,
  },
  endpoint: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    default: "Crear factura",
  },
});

const emit = defineEmits(["update:modelValue", "saved"]);

const formData = ref(generatePaymentData());

const setFormData = () => {
  formData.value = generatePaymentData();
};

function generatePaymentData() {
  return {
    ...defaultFormData,
    concept: props.defaultConcept,
    amount: props.due ?? 0,
    payment_method_id: paymentMethods[0].id,
    paymentMethod: paymentMethods[0],
    date: new Date(),
  };
}

watch(
  () => props.defaultConcept,
  (defaultConcept) => {
    formData.value.concept = defaultConcept;
  },
  {
    immediate: true,
  }
);

watch(
  () => props.due,
  (due) => {
    if (!formData.value.id) {
      formData.value.amount = due;
    }
  },
  {
    immediate: true,
  }
);

watch(
  props.payment,
  (payment) => {
    if (payment) {
      setFormData();
    }
  },
  {
    deep: true,
    immediate: true,
  }
);

const documentTotal = computed(() => {
  return formData.value.documents?.reduce((total, payment) => {
    return MathHelper.sum(total, payment.payment);
  }, 0);
});

const rentsUrl = computed(() => {
  return `/api/rents?filter[client_id]=${formData.value.client?.id}`;
});

const isLoading = ref(false);
function onSubmit() {
  if (!formData.value.amount) {
    ElNotification({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  const data = {
    date: formatDate(formData.value.date || new Date(), "yyyy-MM-dd"),
    amount: formData.value.amount,
    concept: formData.value.concept,
    account_id: formData.value.account_id,
    client_id: formData.value.client.id,
    rent_id: formData.value.rent.id,
    details: formData.value.notes,
  };

  isLoading.value = true;
  axios
    .post(`properties/${data.rent_id}/transactions/expense`, data)
    .then(() => {
      resetForm(true);
      emit("saved");
    })
    .catch((err) => {
      ElNotification({
        type: "error",
        message: err.response ? err.response.data.status.message : "Ha ocurrido un error",
      });
    })
    .finally(() => {
      isLoading.value = false;
    });
}

function deletePayment() {
  axios
    .delete(`${props.endpoint}/${formData.id}`)
    .then(() => {
      emit("update:modelValue", false);
      emit("saved");
      resetForm(true);
    })
    .catch((err) => {
      notify({
        type: "error",
        message: err.response ? err.response.data.status.message : "Ha ocurrido un error",
      });
    });
}

function resetForm(shouldClose) {
  formData.value = {
    ...defaultFormData,
    concept: props.defaultConcept,
  };
  if (shouldClose) {
    emitChange(false);
  }
}

function emitChange(value) {
  emit("update:modelValue", value);
}
</script>

<template>
  <ElDialog
    @open="setFormData()"
    :model-value="modelValue"
    class="overflow-hidden"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <template #header>
      <header
        class="border-b -mx-6 -mt-6 -mr-10 bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"
      >
        <h4 class="font-bold text-xl">{{ title ?? defaultConcept }}</h4>
        <button class="hover:text-danger" @click="close()">
          <IMdiClose />
        </button>
      </header>
    </template>
    <div class="-mt-10">
      <section class="flex space-x-4">
        <AtField class="w-full text-left" label="Concepto">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="formData.concept"
            rounded
          />
        </AtField>
      </section>

      <section>
        <!-- <AtField class="w-full mb-5 text-left" label="Categoría">
          <AccountSelect
            v-model="formData.account"
            placeholder="Selecciona una categoria"
            class="w-full"
          />
        </AtField> -->
        <section class="flex">
          <AppFormField label="Fecha limite" class="w-6/12">
            <ElDatePicker v-model="formData.date" size="large" class="w-full" rounded />
          </AppFormField>
          <AppFormField class="w-6/12 text-left" label="Monto Recibido">
            <AtInput
              class="form-control"
              number-format
              v-model="formData.amount"
              rounded
              required
            />
            {{ documentTotal }}
          </AppFormField>
        </section>
      </section>

      <section class="mt-4 flex space-x-4">
        <AppFormField label="Cliente">
          <BaseSelect
            v-model="formData.client"
            endpoint="/api/clients"
            placeholder="Selecciona cliente"
            label="display_name"
            track-by="id"
          />
        </AppFormField>
        <AppFormField label="Propiedad">
          <BaseSelect
            v-model="formData.rent"
            :endpoint="rentsUrl"
            placeholder="Selecciona el contrato"
            label="date"
            track-by="id"
          />
        </AppFormField>
      </section>

      <AppFormField class="w-full text-left" label="Notes">
        <textarea
          class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="formData.notes"
          cols="3"
          rows="3"
        />
      </AppFormField>
    </div>

    <template #footer>
      <div class="space-x-2 dialog-footer">
        <AtButton
          :disabled="isLoading"
          @click="emitChange(false)"
          class="bg-white border rounded-md text-gray"
        >
          Cancel
        </AtButton>
        <AppButton
          class="text-white bg-blue-500"
          v-if="formData.id"
          @click="deletePayment()"
          :disabled="isLoading"
        >
          Delete
        </AppButton>
        <AppButton
          :processing="isLoading"
          :disabled="isLoading"
          variant="secondary"
          v-else
          @click="onSubmit()"
        >
          Guardar
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
