<script setup>
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { format as formatDate } from "date-fns";
import { ElDatePicker, ElDialog } from "element-plus";
import { inject, ref, watch, computed } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import { MathHelper } from "@/Modules/loans/mathHelper";
import { paymentMethods } from "@/Modules/loans/constants";

const defaultFormData = {
  amount: 0,
  account_id: "",
};

const props = defineProps({
  modelValue: Boolean,
  defaultConcept: {
    type: String,
    required: true,
  },
  defaultAmount: {
    type: Number,
    required: true,
  },
  payment: [Object, null],
  due: Number,
  endpoint: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["update:modelValue", "saved"]);

const categories = inject("accountsOptions", []);

const formData = ref(generatePaymentData());

const setFormData = () => {
  formData.value = generatePaymentData();
};

function generatePaymentData() {
  return {
    ...defaultFormData,
    concept: props.defaultConcept,
    amount: props.due,
    payment_method_id: paymentMethods[0].id,
    paymentMethod: paymentMethods[0],
    payment_date: new Date(),
    amount: 0,
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

function addPayment() {
  if (!formData.value.amount) {
    notify({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  const formData = {
    resource_id: props.resourceId,
    payment_date: formatDate(formData.value.payment_date || new Date(), "yyyy-MM-dd"),
    amount: formData.value.amount,
    concept: formData.value.concept,
    payment_method_id: formData.value.payment_method,
    account_id: formData.value.account_id,
    reference: formData.value.reference,
    notes: formData.value.notes,
    documents: formData.value.documents?.filter((doc) => doc.payment),
  };

  axios
    .post(props.endpoint, formData)
    .then(() => {
      resetForm(true);
      emit("saved");
    })
    .catch((err) => {
      console.log(err);
      notify({
        type: "error",
        message: err.response ? err.response.data.status.message : "Ha ocurrido un error",
      });
    });
}

function deletePayment() {
  axios
    .delete(`${props.endpoint}/${formData.id}`)
    .then(() => {
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
    title="Crear factura"
    @open="setFormData()"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div>
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
        <AtField class="w-full mb-5 text-left" label="Categoría">
          <AtSimpleSelect
            v-model="formData.account_id"
            v-model:selected="formData.account"
            :options="categories"
            placeholder="Selecciona una categoria"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AtField>
        <section class="flex">
          <AtField label="Fecha limite" class="w-3/12">
            <ElDatePicker
              v-model="formData.payment_date"
              size="large"
              class="w-full"
              rounded
            />
          </AtField>
          <AtField class="w-9/12 text-left" label="Monto Recibido">
            <AtInput
              class="form-control"
              number-format
              v-model="formData.amount"
              rounded
              required
            />
            {{ documentTotal }}
          </AtField>
        </section>
      </section>

      <section class="mt-4 flex space-x-4">
        <BaseSelect
          v-model="formData.client"
          endpoint="/api/clients"
          placeholder="Selecciona cliente"
          label="display_name"
          track-by="id"
        />

        <BaseSelect
          v-model="formData.rent"
          :endpoint="rentsUrl"
          placeholder="Selecciona el contrato"
          label="name"
          track-by="id"
        />
      </section>

      <div class="w-full text-left">
        <label for="">Notes</label>
        <textarea
          class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="formData.notes"
          cols="3"
          rows="3"
        />
      </div>
    </div>

    <template #footer>
      <div class="space-x-2 dialog-footer">
        <AtButton @click="emitChange(false)" class="bg-white border rounded-md text-gray">
          Cancel
        </AtButton>
        <AppButton
          class="text-white bg-blue-500"
          v-if="formData.id"
          @click="deletePayment()"
        >
          Delete
        </AppButton>
        <AppButton v-else @click="addPayment()"> Efectuar pago </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
