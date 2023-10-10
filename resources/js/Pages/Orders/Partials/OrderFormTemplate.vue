<script setup lang="ts">
import { watch, computed, ref, reactive, nextTick } from "vue";
import { addMonths, parseISO } from "date-fns";
// @ts-ignore: has no ts definitions but is my package
import { AtSteps, AtStep } from "atmosphere-ui";

import AppButton from "@/Components/shared/AppButton.vue";

import OrderFormVendor from "./OrderFormVendor.vue";
import OrderFormItems from "./OrderFormItems.vue";
import OrderFormReview from "./OrderFormReview.vue";

import { formatDate } from "@/utils";
import { IClient } from "@/Modules/clients/clientEntity";
import { InvoiceItem } from "@/Modules/invoicing/entities";

const props = defineProps<{
  data: Record<string, any>;
  isProcessing: boolean;
  client: IClient;
}>();

const emit = defineEmits(["submit"]);

const invoiceForm = reactive({
  id: null,
  is_new_client: false,
  client_id: props.client?.id,
  client: props.client,
  client_name: "",
  date: new Date(),
  deposit_due: new Date(),
  deposit_reference: "",
  is_deposit_received: false,
  payment_account_id: null,
  payment_method: "",
  first_invoice_date: addMonths(new Date(), 1),
  next_invoice_date: addMonths(new Date(), 1),
  end_date: null,
  commission: 10,
  commission_type: "",
  status: "draft",
  items: [],
  additional_fees: [],
});

watch(
  () => props.data,
  (newValue) => {
    if (!newValue || !Object.values(newValue).filter(value => value).length) return;

    Object.keys(invoiceForm).forEach((field: string) => {
      if (newValue[field]?.split && newValue[field]?.split("-").length == 3) {
        // @ts-ignore
        invoiceForm[field] = parseISO(newValue[field]);
      } else if (newValue) {
        // @ts-ignore
        invoiceForm[field] = newValue[field];
      }
    });
  },
  { deep: true, immediate: true }
);
// Wizard
const validations = [
  {
    handle: () => invoiceForm.client || invoiceForm.client_name,
  },
  {
    handle: () => {
      console.log(invoiceForm.items);
      return invoiceForm.items
      .filter((item: InvoiceItem) => item.price && item.quantity && item.concept).length;
    },
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
  const labels = ["items", "review order", "send"];
  return labels[currentStep.value];
});

const handleUpdate = (data: Record<string, any>) => {
  Object.keys(invoiceForm).forEach((field) => {
    if (data[field]) {
      // @ts-ignore: it's ok not to be ok
      invoiceForm[field] = data[field];
    }
  });
};

const onFinished = () => {
  const data = {
    ...invoiceForm,
    date: formatDate(invoiceForm.date, "yyyy-MM-dd"),
    client_id: invoiceForm.client?.id,
    status: "draft",
    items:  invoiceForm.items.map((item: InvoiceItem, index) => ({
        index,
        quantity: parseFloat(item.quantity),
        price: parseFloat(item.price),
        amount: parseFloat(item.quantity) * parseFloat(item.price),
      }))
      .filter((item) => item.concept),
  };

  // @ts-ignore: is optional
  delete data.client;

  emit("submit", data);
};
</script>

<template>
  <AtSteps
    v-model="currentStep"
    finish-status="success"
    simple
    style="margin-top: 20px"
    active-class="text-white bg-primary"
    circle-active-color="bg-primary text-white"
    load-shadow-color="shadow-primary"
    @finished="onFinished"
  >
    <AtStep name="personal" :title="$t('client data')" :before-change="validateStep">
      <OrderFormVendor :model-value="invoiceForm" @update:model-value="handleUpdate" />
    </AtStep>
    <AtStep name="property" :title="$t('products')" :before-change="validateStep">
      <OrderFormItems :model-value="invoiceForm" @update:model-value="handleUpdate" />
    </AtStep>
    <AtStep name="rent_details" :title="$t('review invoice')" :before-change="validateStep">
      <OrderFormReview :model-value="invoiceForm" @update:model-value="handleUpdate" />
    </AtStep>

    <template v-slot:footer="{ prev, next }">
      <footer class="flex justify-end mt-auto space-x-2 md:mt-16 md:px-32">
        <AppButton variant="neutral" @click="prev()" class="w-full capitalize md:w-fit">
          {{ $t("back") }}
        </AppButton>
        <AppButton
          variant="inverse"
          rounded
          class="w-full capitalize md:w-fit"
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
