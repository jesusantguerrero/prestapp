<script setup lang="ts">
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { format as formatDate } from "date-fns";
import { ElDatePicker, ElDialog } from "element-plus";
import { inject, ref, watch, computed } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import PaymentGrid from "./PaymentGrid.vue";

import { MathHelper } from "@/Modules/loans/mathHelper";
import { paymentMethods } from "@/Modules/loans/constants";

const defaultPaymentForm = {
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

const paymentForm = ref(generatePaymentData());

const setFormData = () => {
  paymentForm.value = generatePaymentData();
};

function generatePaymentData() {
  return {
    ...defaultPaymentForm,
    concept: props.defaultConcept,
    amount: props.due,
    payment_method_id: paymentMethods[0].id,
    paymentMethod: paymentMethods[0],
    payment_date: new Date(),
    ...(props.payment?.id ? props.payment : {}),
  };
}

watch(
  () => props.defaultConcept,
  (defaultConcept) => {
    paymentForm.value.concept = defaultConcept;
  },
  {
    immediate: true,
  }
);

watch(
  () => props.due,
  (due) => {
    if (!paymentForm.value.id) {
      paymentForm.value.amount = due;
    }
  },
  {
    immediate: true,
  }
);

watch(
  () => props.payment,
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
  return paymentForm.value.documents?.reduce((total, payment) => {
    return MathHelper.sum(total, payment.payment);
  }, 0);
});

const isLoading = ref(false);
function onSubmit() {
  if (isLoading.value) {
    return;
  }

  if (!paymentForm.id) {
    createPayment();
    return;
  }

  isLoading.value = true;

  const formData = {
    payment_date: formatDate(paymentForm.value.payment_date || new Date(), "yyyy-MM-dd"),
    amount: paymentForm.value.amount,
    concept: paymentForm.value.concept,
    payment_method_id: paymentForm.value.payment_method,
    account_id: paymentForm.value.account_id,
    reference: paymentForm.value.reference,
    notes: paymentForm.value.notes,
    documents: paymentForm.value.documents?.filter((doc) => doc.payment),
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
    })
    .finally(() => {
      isLoading.value = false;
    });
}

function createPayment() {
  if (!paymentForm.value.amount) {
    notify({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  isLoading.value = true;
  const formData = {
    resource_id: props.resourceId,
    payment_date: formatDate(paymentForm.value.payment_date || new Date(), "yyyy-MM-dd"),
    amount: paymentForm.value.amount,
    concept: paymentForm.value.concept,
    payment_method_id: paymentForm.value.payment_method,
    account_id: paymentForm.value.account_id,
    reference: paymentForm.value.reference,
    notes: paymentForm.value.notes,
    documents: paymentForm.value.documents?.filter((doc) => doc.payment),
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
    })
    .finally(() => {
      isLoading.value = false;
    });
}

function deletePayment() {
  const endpoint = props.endpoint
    ? `${props.endpoint}`
    : `/invoice/${props.payment.resource_id}/payments/${props.payment?.id}`;

  axios
    .delete(endpoint)
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
  paymentForm.value = {
    ...defaultPaymentForm,
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
    title="Pagar prestamo"
    @open="setFormData()"
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
            v-model="paymentForm.concept"
            rounded
          />
        </AtField>

        <AtField class="w-full text-left" label="Referencia">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="paymentForm.reference"
            rounded
          />
        </AtField>

        <AtField class="w-full text-left" label="Monto Recibido">
          <AtInput
            class="form-control"
            number-format
            v-model="paymentForm.amount"
            rounded
            required
          />
          {{ documentTotal }}
        </AtField>
      </section>

      <section class="flex space-x-4">
        <AtField class="w-5/12 mb-5 text-left" label="Cuenta de Pago">
          <AtSimpleSelect
            v-model="paymentForm.account_id"
            v-model:selected="paymentForm.paymentAccount"
            :options="categories"
            placeholder="Pick an account"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AtField>
        <AtField class="w-3/12 mb-5 text-left" label="Metodo de Pago">
          <AtSimpleSelect
            v-model="paymentForm.payment_method_id"
            v-model:selected="paymentForm.paymentMethod"
            :options="paymentMethods"
            placeholder="Seleccione metodo de pago"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AtField>
        <AtField label="Fecha de pago" class="w-3/12">
          <ElDatePicker
            v-model="paymentForm.payment_date"
            size="large"
            class="w-full"
            rounded
          />
        </AtField>
      </section>

      <div class="w-full text-left">
        <label for="">Notes</label>
        <textarea
          class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="paymentForm.notes"
          cols="3"
          rows="3"
        />
      </div>

      <PaymentGrid
        v-if="paymentForm.documents"
        :table-data="paymentForm.documents"
        :available-taxes="[]"
      />
    </div>

    <template #footer>
      <div class="space-x-2 dialog-footer">
        <AtButton @click="emitChange(false)" class="bg-white border rounded-md text-gray">
          Cancel
        </AtButton>
        <AppButton
          class="text-white bg-blue-500"
          v-if="paymentForm.id"
          @click="deletePayment()"
        >
          Delete
        </AppButton>
        <AppButton v-else @click="onSubmit()" :disabled="isLoading" :loading="isLoading">
          Efectuar pago
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
