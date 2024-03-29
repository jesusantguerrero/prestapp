<script setup lang="ts">
import { AtButton, AtInput } from "atmosphere-ui";
import { format as formatDate, parseISO } from "date-fns";
import { ElDialog, ElNotification } from "element-plus";
import { ref, watch, computed, nextTick } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import PaymentGrid from "./PaymentGrid.vue";

import { MathHelper } from "@/Modules/loans/mathHelper";
import { paymentMethods } from "@/Modules/loans/constants";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import axios from "axios";
import { useI18n } from "vue-i18n";
import { useResponsive } from "@/utils/useResponsive";
import BaseDatePicker from "@/Components/shared/BaseDatePicker.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import DepositSelect from "@/Components/shared/Selects/DepositSelect.vue";

const { t } = useI18n();

const defaultPaymentForm = {
  amount: 0,
  account_id: "",
  payment_method_id: {
    id: "cash",
  },
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
  nextTick(() => {
    paymentForm.value = generatePaymentData();
  });
};

function generatePaymentData() {
  return {
    ...defaultPaymentForm,
    ...(props.payment?.id ? props.payment : {}),
    concept: props.defaultConcept,
    amount: props.due,
    payment_method_id: paymentMethods[0].id,
    paymentMethod: paymentMethods[0],
    payment_date: props.payment?.payment_date
      ? parseISO(props.payment?.payment_date)
      : new Date(),
    documents: props.payment?.documents ?? [],
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

const setPaymentAmount = (amount: number, source: string) => {
  console.log("source: ", source);
  nextTick(() => {
    paymentForm.value.amount = amount;
  });
};

watch(
  () => props.due,
  (due) => {
    if (!paymentForm.value?.id && props.due) {
      setPaymentAmount(due, `change of due ${props.due}`);
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
    setPaymentAmount(total, `change of document total ${total}`);
  }
);
const isMultiple = computed(() => {
  return paymentForm.value?.documents?.length;
});

const isLoading = ref(false);

function onSubmit() {
  if (isLoading.value) return;
  if (paymentForm.value.payment_method_id.id == "deposit") {
    applyDeposit();
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

  const requiredFields = [
    "payment_date",
    "paymentMethod",
    "amount",
    ...(props.hideAccountSelector ? [] : ["account_id"]),
  ];
  const fieldsMapper = Object.entries(paymentForm.value).filter(([fieldName]) =>
    requiredFields.includes(fieldName)
  );

  const hasErrors = fieldsMapper
    .map(([fieldName, value]) => value)
    .some((value) => !value);

  if (hasErrors) {
    ElNotification({
      message: t("Check all required fields"),
      type: "error",
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

  axios({
    method: paymentForm.value?.id ? "put" : "post",
    url: props.endpoint,
    data: formData,
  })
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
function applyDeposit() {
  if (!paymentForm.value.amount) {
    ElNotification({
      type: "error",
      message: "should specify an amount",
    });
    return;
  }

  const requiredFields = ["payment_date", "paymentMethod", "amount"];
  const fieldsMapper = Object.entries(paymentForm.value).filter(([fieldName]) =>
    requiredFields.includes(fieldName)
  );

  const hasErrors = fieldsMapper
    .map(([fieldName, value]) => value)
    .some((value) => !value);

  if (hasErrors) {
    ElNotification({
      message: t("Check all required fields"),
      type: "error",
    });
    return;
  }

  isLoading.value = true;

  const { client_id, invoiceable_id } = props.payment;

  const formData = {
    client_id,
    total: paymentForm.value.amount,
    rent_id: invoiceable_id,
    payment_date: formatDate(paymentForm.value.payment_date || new Date(), "yyyy-MM-dd"),
    concept: paymentForm.value.concept,
    payment_method_id: paymentForm.value.payment_method,
    account_id: paymentForm.value.depositSource,
    reference: paymentForm.value.reference,
    notes: paymentForm.value.notes,
  };

  const { invoiceable_id: rentId, invoice_id: invoiceId } = props.payment;

  const endpoint = `/rents/${rentId}/invoices/${invoiceId}/apply-deposit`;
  axios({
    method: paymentForm.value?.id ? "put" : "post",
    url: endpoint,
    data: formData,
  })
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

const savePaymentText = computed(() => {
  return paymentForm.value.id ? "Update payment" : "Save payment";
});

const { isMobile } = useResponsive();

const dialogWidth = computed(() => {
  return isMobile.value ? "100%" : "50%";
});
</script>

<template>
  <ElDialog
    class="rounded-lg overflow-hidden"
    @open="setFormData()"
    :width="dialogWidth"
    :fullscreen="isMobile"
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
      <section class="md:flex md:space-x-4">
        <AppFormField
          required
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

        <AppFormField class="w-full text-left" :label="$t('amount received')" required>
          <AtInput
            class="form-control"
            number-format
            v-model="paymentForm.amount"
            rounded
            :disabled="documentTotal"
            required
          />
        </AppFormField>
      </section>

      <section class="md:flex md:space-x-4">
        <AppFormField
          v-if="!hideAccountSelector && paymentForm.payment_method_id?.id != 'deposit'"
          class="w-5/12 mb-5 text-left"
          label="Cuenta de Pago"
          required
        >
          <AccountSelect
            :endpoint="accountsEndpoint"
            v-model="paymentForm.account"
            placeholder="Selecciona una cuenta"
            @update:modelValue="paymentForm.account_id = $event?.id"
          />
        </AppFormField>
        <AppFormField
          v-else-if="paymentForm.payment_method_id?.id == 'deposit'"
          class="w-5/12 mb-5 text-left"
          label="Deposit"
          required
        >
          <DepositSelect
            :client-id="payment.client_id"
            category-name="security_deposits"
            v-model="paymentForm.depositSource"
            placeholder="Selecciona una cuenta"
          />
        </AppFormField>
        <AppFormField class="md:w-3/12 mb-5 text-left" label="Metodo de Pago" required>
          <BaseSelect
            v-model="paymentForm.payment_method_id"
            :client-id="payment?.client_id"
            :options="paymentMethods"
            placeholder="Forma pago"
            class="w-full"
            label="name"
            track-by="id"
          />
        </AppFormField>
        <AppFormField :label="$t('Payment date')" class="md:w-3/12 w-full" required>
          <BaseDatePicker v-model="paymentForm.payment_date" />
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
          variant="secondary"
          @click="onSubmit()"
          :processing="isLoading"
          :disabled="isLoading"
          :loading="isLoading"
        >
          {{ $t(savePaymentText) }}
        </AppButton>
      </div>
    </template>
  </ElDialog>
</template>

<style lang="scss">
.el-dialog.is-dialog footer.el-dialog__footer {
  position: fixed !important;
  width: 100%;
  bottom: 0;
}
</style>
