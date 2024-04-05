<script setup lang="ts">
import { watch, computed, ref, reactive, nextTick, onMounted } from "vue";
import { addMonths, parseISO } from "date-fns";

import AppButton from "@/Components/shared/AppButton.vue";

import OrderFormVendor from "./OrderFormVendor.vue";
import OrderFormItems from "./OrderFormItems.vue";
import OrderFormReview from "./OrderFormReview.vue";

import { formatDate, formatMoney } from "@/utils";
import { IClient } from "@/Modules/clients/clientEntity";
import { InvoiceItem, ILineItem } from "@/Modules/invoicing/entities";
import LoanSummary from "@/Pages/Loans/Partials/LoanSummary.vue";

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
  lines: [],
  additional_fees: [],
});

watch(
  () => props.data,
  (newValue) => {
    if (!newValue || !Object.values(newValue).filter((value) => value).length) return;
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
      return invoiceForm.lines.filter(
        (item: InvoiceItem) => item.price && item.quantity && item.concept
      ).length;
    },
  },
  {
    handle: () => true,
  },
];

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
    lines: invoiceForm.lines
      .map((item: InvoiceItem, index) => ({
        ...item,
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

const subtotal = computed(() => {
  return invoiceForm.lines.reduce((total, row: ILineItem) => {
    total += parseFloat(row.quantity ?? 0) * parseFloat(row.price ?? 0);
    return total;
  }, 0);
});

const discount = computed(() =>
  invoiceForm.lines.reduce((total, row: ILineItem) => {
    total += subtotal.value * parseFloat(row.discount ?? 0);
    return total;
  }, 0)
);

const total = computed(() => {
  return subtotal.value - parseFloat(discount.value ?? 0);
});

const showActions = ref(false);
onMounted(() => {
  nextTick(() => {
    showActions.value = true;
  });
});
</script>

<template>
  <section class="flex space-x-8">
    <section class="w-8/12 px-4 pt-8 rounded-md bg-base-lvl-3">
      <OrderFormItems :model-value="invoiceForm" @update:model-value="handleUpdate" />
    </section>
    <section class="w-4/12 space-y-4 rounded-md">
      <section
        class="relative w-full px-4 mt-4 overflow-hidden bg-white rounded-md shadow-md md:mt-0"
      >
        <OrderFormVendor :model-value="invoiceForm" @update:model-value="handleUpdate" />
        <OrderFormReview
          :model-value="invoiceForm"
          @update:model-value="handleUpdate"
          class="mt-4"
        />
      </section>

      <article
        class="w-full mt-4 md:mt-0 rounded-md bg-white shadow-md relative overflow-hidden grid gap-4 grid-cols-1 grid-rows-[1fr_50px]"
      >
        <section class="w-full px-4 overflow-hidden text-body-1">
          <header class="py-4 font-bold first-letter:capitalize">
            {{ $t("invoice summary") }}
          </header>
          <LoanSummary
            :cards="[
              {
                label: $t('subtotal'),
                value: formatMoney(subtotal),
              },
              {
                label: $t('discount'),
                value: formatMoney(discount),
              },
              {
                label: $t('total'),
                value: formatMoney(total),
              },
            ]"
          />
        </section>
        <footer class="flex justify-between w-full px-4 py-1">
          <AppButton
            class="font-bold text-red-400 rounded-md bg-base-lvl-2"
            variant="neutral"
            @click="route('dropshipping.invoices')"
          >
            {{ $t("cancel") }}
          </AppButton>
          <AppButton
            :processing="isProcessing"
            variant="secondary"
            @click="onFinished"
            :disabled="isProcessing"
          >
            {{ $t("save invoice") }}
          </AppButton>
        </footer>
      </article>
    </section>
    <Teleport to="#outer-action" v-if="showActions">
      <section class="flex space-x-2">
        <AppButton
          class="font-bold text-red-400 rounded-md bg-base-lvl-2"
          variant="neutral"
          @click="route('dropshipping.invoices')"
        >
          {{ $t("cancel") }}
        </AppButton>
        <AppButton
          :processing="isProcessing"
          variant="secondary"
          @click="onFinished"
          :disabled="isProcessing"
        >
          {{ $t("save invoice") }}
        </AppButton>
      </section>
    </Teleport>
  </section>
</template>
