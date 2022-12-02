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
          />
        </AtField>

        <AtField class="w-full text-left" label="Referencia">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="paymentForm.reference"
          />
        </AtField>

        <AtField class="w-full text-left" label="Monto Recibido">
          <AtInput
            type="number"
            class="form-control"
            v-model="paymentForm.amount"
            required
            min="1"
          />
        </AtField>
      </section>

      <section class="flex space-x-4">
        <AtField class="w-5/12 text-left mb-5" label="Cuenta de Pago">
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
        <AtField class="w-3/12 text-left mb-5" label="Metodo de Pago">
          <AtSimpleSelect
            v-model="paymentForm.payment_method"
            v-model:selected="paymentForm.paymentMethod"
            :options="[
              {
                id: 'cash',
                name: 'Efectivo',
              },
              {
                id: 'check',
                name: 'Cheque',
              },
              {
                id: 'bank',
                name: 'Banco',
              },
            ]"
            placeholder="Seleccione metodo de pago"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AtField>
        <AtField label="Fecha de pago" class="w-3/12">
          <ElDatePicker v-model="paymentForm.payment_date" size="large" class="w-full" />
        </AtField>
      </section>

      <div class="w-full text-left">
        <label for="">Notes</label>
        <textarea
          class="w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="paymentForm.notes"
          cols="3"
          rows="3"
        />
      </div>
    </div>

    <template #footer>
      <div class="dialog-footer space-x-2">
        <AtButton @click="emitChange(false)" class="bg-white text-gray border rounded-md">
          Cancel
        </AtButton>
        <AppButton
          class="bg-blue-500 text-white"
          v-if="paymentForm.id"
          @click="deletePayment()"
        >
          Delete
        </AppButton>
        <AppButton v-else @click="addPayment()"> Efectuar pago </AppButton>
      </div>
    </template>
  </ElDialog>
</template>

<script setup>
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { format as formatDate } from "date-fns";
import { ElDatePicker, ElDialog } from "element-plus";
import { inject, ref, watch } from "vue";
import AppButton from "../../../Components/shared/AppButton.vue";

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
    payment_date: new Date(),
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
  props.payment,
  (payment) => {
    if (payment) {
      paymentForm.value = payment;
    }
  },
  {
    deep: true,
    immediate: true,
  }
);

function addPayment() {
  if (!paymentForm.value.amount) {
    notify({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  const formData = {
    resource_id: props.resourceId,
    payment_date: formatDate(paymentForm.value.payment_date || new Date(), "yyyy-MM-dd"),
    amount: paymentForm.value.amount,
    concept: paymentForm.value.concept,
    payment_method_id: paymentForm.value.payment_method,
    account_id: paymentForm.value.account_id,
    reference: paymentForm.value.reference,
    notes: paymentForm.value.notes,
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
    .delete(`${props.endpoint}/${paymentForm.id}`)
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
