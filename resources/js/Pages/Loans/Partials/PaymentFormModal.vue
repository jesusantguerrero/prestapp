<template>
  <ElDialog
    title="Pagar prestamo"
    :model-value="modelValue"
    @update:model-value="$emit('update:modelValue', $event)"
  >
    <div class="">
      <section class="flex space-x-4">
        <AtField class="w-full text-left" label="Concepto:">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="invoicePayment.concept"
          />
        </AtField>

        <AtField class="w-full text-left" label="Referencia">
          <AtInput
            type="text"
            class="form-control"
            name="invoice-description"
            id="invoice-description"
            v-model="invoicePayment.reference"
          />
        </AtField>

        <AtField class="w-full text-left" label="Monto Recibido">
          <AtInput
            type="number"
            class="form-control"
            v-model="invoicePayment.amount"
            required
            min="1"
          />
        </AtField>
      </section>

      <section class="flex space-x-4">
        <AtField class="w-5/12 text-left mb-5" label="Cuenta de Pago">
          <AtSimpleSelect
            v-model="invoicePayment.account_id"
            v-model:selected="invoicePayment.paymentAccount"
            :options="categories"
            placeholder="Pick an account"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AtField>
        <AtField class="w-3/12 text-left mb-5" label="Metodo de Pago">
          <AtSimpleSelect
            v-model="invoicePayment.payment_method"
            v-model:selected="invoicePayment.paymentMethod"
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
          <ElDatePicker
            v-model="invoicePayment.payment_date"
            size="large"
            class="w-full"
          />
        </AtField>
      </section>

      <div class="w-full text-left">
        <label for="">Notes</label>
        <textarea
          class="w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="invoicePayment.notes"
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
          v-if="invoicePayment.id"
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

const defaultInvoicePayment = {
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
  paymentDue: Number,
  endpoint: {
    type: String,
    required: true,
  },
});

const emit = defineEmits(["update:modelValue"]);

const categories = inject("accountsOptions", []);

const invoicePayment = ref({ ...defaultInvoicePayment });

watch(
  props.defaultConcept,
  () => {
    invoicePayment.value.concept = props.defaultConcept;
  },
  {
    immediate: true,
  }
);

watch(
  props.paymentDue,
  () => {
    if (!invoicePayment.id) {
      invoicePayment.value.amount = props.paymentDue;
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
      invoicePayment.value = payment;
    }
  },
  {
    deep: true,
    immediate: true,
  }
);

function addPayment() {
  if (!invoicePayment.value.amount) {
    notify({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  const formData = {
    resource_id: props.resourceId,
    payment_date: formatDate(
      invoicePayment.value.payment_date || new Date(),
      "yyyy-MM-dd"
    ),
    amount: invoicePayment.value.amount,
    concept: invoicePayment.value.concept,
    payment_method_id: invoicePayment.value.payment_method,
    account_id: invoicePayment.value.account_id,
    reference: invoicePayment.value.reference,
    notes: invoicePayment.value.notes,
  };

  axios
    .post(props.endpoint, formData)
    .then(() => {
      resetForm(true);
      emit("saved");
    })
    .catch((err) => {
      notify({
        type: "error",
        message: err.response ? err.response.data.status.message : "Ha ocurrido un error",
      });
    });
}

function deletePayment() {
  axios
    .delete(`${props.endpoint}/${invoicePayment.id}`)
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
  invoicePayment = {
    ...defaultInvoicePayment,
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
