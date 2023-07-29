<script setup lang="ts">
import { watch, computed, ref, reactive, nextTick } from "vue";
import { addMonths, parseISO } from "date-fns";
// @ts-ignore: has no ts definitions but is my package
import { AtSteps, AtStep } from "atmosphere-ui";

import AppButton from "@/Components/shared/AppButton.vue";

import OrderFormVendor from "./OrderFormVendor.vue";
import OrderFormItems from "./OrderFormItems.vue";
import OrderFormReview from "./OrderFormReview.vue";
import OrderFormSend from "./OrderFormSend.vue";

import { dateToIso, formatDate } from "@/utils";
import { IClient } from "@/Modules/clients/clientEntity";
import { IProperty, IUnit } from "@/Modules/properties/propertyEntity";

const props = defineProps<{
  data: Record<string, any>;
  isProcessing: boolean;
  client: IClient;
  unit: IUnit;
  property: IProperty;
}>();

const emit = defineEmits(["submit"]);

const rentForm = reactive({
  id: null,
  property_id: props.property?.id,
  property: props.property,
  unit_id: props.unit?.id,
  unit: props.unit,
  is_new_client: false,
  client_id: props.client?.id,
  client: props.client,
  client_name: "",
  date: new Date(),
  deposit: props.unit?.price,
  deposit_due: new Date(),
  deposit_reference: "",
  is_deposit_received: false,
  payment_account_id: null,
  payment_method: "",
  amount: props.unit?.price,
  first_invoice_date: addMonths(new Date(), 1),
  next_invoice_date: addMonths(new Date(), 1),
  end_date: null,
  frequency: "MONTHLY",
  commission: 10,
  commission_type: "",
  late_fee: 10,
  late_fee_type: "",
  grace_days: 0,
  additional_fees: [],
});

watch(
  () => rentForm.unit,
  (unit) => {
    if (unit) {
      rentForm.amount = unit.price;
    }
  },
  {
    immediate: true,
  }
);

watch(
  () => rentForm.deposit_due,
  (date) => {
    rentForm.date = date;
  },
  { immediate: true }
);

watch(
  () => rentForm.date,
  (date) => {
    rentForm.first_invoice_date = addMonths(date, 1);
  },
  { immediate: true }
);

watch(
  () => props.data,
  (newValue) => {
    if (!newValue) return;
    Object.keys(rentForm).forEach((field: string) => {
      if (newValue[field]?.split && newValue[field]?.split("-").length == 3) {
        // @ts-ignore
        rentForm[field] = parseISO(newValue[field]);
      } else if (newValue) {
        // @ts-ignore
        rentForm[field] = newValue[field];
      }
    });
  },
  { deep: true, immediate: true }
);

watch(
  () => props.property,
  () => {
    rentForm.property_id = props.property?.id;
    rentForm.property = props.property;
  },
  {
    immediate: true,
  }
);

watch(
  () => props.unit,
  () => {
    rentForm.unit_id = props.unit?.id;
    rentForm.unit = props.unit;
  },
  {
    immediate: true,
  }
);

// Wizard
const validations = [
  {
    handle: () => rentForm.client || rentForm.client_name,
  },
  {
    handle: () => true,
  },
  {
    handle: () => true,
  },
];
const validateStep = () => {
  return new Promise((resolve) => resolve(!validations[currentStep.value]?.handle()));
};
const currentStep = ref(0);
const nextButtonLabel = computed(() => {
  const labels = ["items", "review order", "send", "send"];
  return labels[currentStep.value];
});

const handleUpdate = (data: Record<string, any>) => {
  Object.keys(rentForm).forEach((field) => {
    if (data[field]) {
      rentForm[field] = data[field];
    }
  });
};

const onFinished = () => {
  const data = {
    ...rentForm,
    deposit_due: formatDate(rentForm.deposit_due, "yyyy-MM-dd"),
    date: formatDate(rentForm.date, "yyyy-MM-dd"),
    first_invoice_date: dateToIso(rentForm.first_invoice_date),
    next_invoice_date: dateToIso(rentForm.next_invoice_date),
    end_date: dateToIso(rentForm.end_date),
    unit_id: rentForm.unit?.id,
    property_id: rentForm.property?.id,
    client_id: rentForm.client?.id,
  };

  delete data.property;
  delete data.client;
  delete data.unit;

  emit("submit", data);
};
</script>

<template>
  <AtSteps
    v-model="currentStep"
    finish-status="success"
    simple
    style="margin-top: 20px"
    active-class="bg-primary text-white"
    circle-active-color="bg-primary text-white"
    load-shadow-color="shadow-primary"
    @finished="onFinished"
  >
    <AtStep name="personal" :title="$t('Vendor data')" :before-change="validateStep">
      <OrderFormVendor :model-value="rentForm" @update:model-value="handleUpdate" />
    </AtStep>
    <AtStep name="property" :title="$t('Propiedad')" :before-change="validateStep">
      <OrderFormItems :model-value="rentForm" @update:model-value="handleUpdate" />
    </AtStep>
    <AtStep name="rent_details" :title="$t('Review order')" :before-change="validateStep">
      <OrderFormReview :model-value="rentForm" @update:model-value="handleUpdate" />
    </AtStep>
    <AtStep name="fees" :title="$t('Send')">
      <OrderFormSend :model-value="rentForm" @update:model-value="handleUpdate" />
    </AtStep>

    <template v-slot:footer="{ prev, next }">
      <footer class="flex justify-end space-x-2 mt-auto md:mt-16 md:px-32">
        <AppButton variant="neutral" @click="prev()" class="w-full md:w-fit capitalize">
          {{ $t("back") }}
        </AppButton>
        <AppButton
          variant="inverse"
          rounded
          class="w-full md:w-fit capitalize"
          :processing="isProcessing"
          :disabled="isProcessing"
          @click="next()"
        >
          {{ $t(nextButtonLabel) }}
        </AppButton>
      </footer>
    </template>
  </AtSteps>
</template>
