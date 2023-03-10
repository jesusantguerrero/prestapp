<script setup lang="ts">
import { AtButton, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { format as formatDate } from "date-fns";
import { ElDatePicker, ElDialog, ElNotification } from "element-plus";
import { ref, watch, computed, nextTick } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import PaymentGrid from "./PaymentGrid.vue";

import { MathHelper } from "@/Modules/loans/mathHelper";
import { paymentMethods } from "@/Modules/loans/constants";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";

const defaultPaymentForm = {
  amount: 0,
  account_id: "",
};

const props = withDefaults(
  defineProps<{
    modelValue: boolean;
    defaultConcept: string;
    defaultAmount: string;
    payment: Record<string, any> | null;
    due: Number;
    endpoint: string;
    title?: string;
    accountsEndpoint: string;
    hideAccountSelector?: boolean;
  }>(),
  {
    accountsEndpoint: "/api/accounts",
  }
);

const emit = defineEmits(["update:modelValue", "saved"]);

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
    documents: props.payment?.documents ?? [],
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
  return paymentForm.value.documents?.reduce(
    (total: number, payment: Record<string, any>) => {
      return MathHelper.sum(total, payment.payment);
    },
    0
  );
});

watch(
  () => documentTotal.value,
  (total) => {
    nextTick(() => {
      paymentForm.value.amount = total;
    });
  }
);
const isMultiple = computed(() => {
  return paymentForm.value?.documents?.length;
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
      ElNotification({
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
    ElNotification({
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
    documents: paymentForm.value.documents?.filter((doc: any) => doc.payment),
  };

  axios
    .post(props.endpoint, formData)
    .then(() => {
      resetForm(true);
      emit("saved");
    })
    .catch((err) => {
      console.log(err);
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
    class="rounded-lg overflow-hidden"
    @open="setFormData()"
    :model-value="modelValue"
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
    <div class="">
      <section class="flex space-x-4">
        <AppFormField
          class="w-full text-left"
          label="Concepto"
          v-model="paymentForm.concept"
        />

        <AppFormField
          class="w-full text-left"
          label="Referencia"
          v-model="paymentForm.reference"
          rounded
        />

        <AppFormField class="w-full text-left" label="Monto Recibido">
          <AtInput
            class="form-control"
            number-format
            @update:model-value="paymentForm.amount = $event"
            :model-value="paymentForm.amount"
            rounded
            :disabled="documentTotal"
            required
          />
        </AppFormField>
      </section>

      <section class="flex space-x-4">
        <AppFormField
          v-if="!hideAccountSelector"
          class="w-5/12 mb-5 text-left"
          label="Cuenta de Pago"
        >
          <AccountSelect
            :endpoint="accountsEndpoint"
            v-model="paymentForm.paymentAccount"
            placeholder="Selecciona una cuenta"
            @update:modelValue="paymentForm.account_id = $event?.id"
          />
        </AppFormField>
        <AppFormField class="w-3/12 mb-5 text-left" label="Metodo de Pago">
          <AtSimpleSelect
            v-model="paymentForm.payment_method_id"
            v-model:selected="paymentForm.paymentMethod"
            :options="paymentMethods"
            placeholder="Forma pago"
            class="w-full"
            label="name"
            key-track="id"
          />
        </AppFormField>
        <AppFormField label="Fecha de pago" class="w-3/12">
          <ElDatePicker
            v-model="paymentForm.payment_date"
            size="large"
            class="w-full"
            rounded
          />
        </AppFormField>
      </section>

      <AppFormField class="w-full text-left" label="Notes">
        <textarea
          class="w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
          v-model="paymentForm.notes"
          cols="3"
          rows="3"
        />
      </AppFormField>

      <PaymentGrid
        v-if="isMultiple"
        :table-data="paymentForm.documents"
        :available-taxes="[]"
      />
    </div>

    <template #footer>
      <div class="space-x-2 dialog-footer flex justify-end">
        <AtButton
          :disabled="isLoading"
          @click="emitChange(false)"
          class="bg-white border rounded-md text-gray"
        >
          Cancel
        </AtButton>
        <AppButton
          class="text-white bg-blue-500"
          v-if="paymentForm.id"
          @click="deletePayment()"
          :disabled="isLoading"
        >
          Delete
        </AppButton>
        <AppButton
          v-else
          variant="secondary"
          @click="onSubmit()"
          :processing="isLoading"
          :disabled="isLoading"
          :loading="isLoading"
        >
          Efectuar pago
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>
