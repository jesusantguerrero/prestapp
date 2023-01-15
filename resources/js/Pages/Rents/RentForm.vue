<script setup lang="ts">
import { watch, ref } from "vue";
import { useForm, router } from "@inertiajs/vue3";
import { addMonths, format as formatDate } from "date-fns";

import AppLayout from "@/Components/templates/AppLayout.vue";
import FormTemplate from "./Partials/FormTemplate.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { IProperty } from "@/Modules/properties/propertyEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import RentFormTemplate from "./Partials/RentFormTemplate.vue";

defineProps<{
  loanData: ILoan[];
  properties: IProperty[];
  clients: IClient[];
}>();

const rentForm = useForm({
  property_id: null,
  property: null,
  unit_id: null,
  unit: null,
  client_id: null,
  date: new Date(),
  deposit: 0,
  deposit_due: new Date(),
  is_deposit_received: false,
  deposit_reference: "",
  payment_account_id: null,
  payment_method: "",
  amount: 0,
  first_invoice_date: addMonths(new Date(), 1),
  next_invoice_date: addMonths(new Date(), 1),
  commission: 10,
  commission_type: "",
  late_fee: 10,
  late_fee_type: "",
  grace_days: 0,
  frequency: "MONTHLY",
  additional_fees: [],
});

watch(
  () => rentForm.unit,
  (unit) => {
    if (unit) {
      rentForm.amount = unit.price;
    }
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

const onSubmit = () => {
  rentForm
    .transform((data) => ({
      ...rentForm.data(),
      deposit_due: formatDate(rentForm.deposit_due, "y-M-d"),
      date: formatDate(rentForm.date, "yyyy-MM-dd"),
      first_invoice_date: formatDate(rentForm.first_invoice_date, "y-M-d"),
      next_invoice_date: formatDate(rentForm.next_invoice_date, "y-M-d"),
      property: undefined,
    }))
    .submit("post", route("rents.store"), {
      onSuccess() {
        router.visit(`/properties/`);
      },
    });
};

const goToList = () => {
  router.visit("/loans");
};
</script>

<template>
  <AppLayout title="Crear contrato">
    <main class="w-full bg-white px-5 py-5 rounded-md text-body-1">
      <RentFormTemplate :data="rentForm" :current-step="step" @submit="onSubmit" />
    </main>
  </AppLayout>
</template>
