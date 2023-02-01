<script setup lang="ts">
import { watch, computed, ref, reactive } from "vue";
import { AtButton } from "atmosphere-ui";
import { addMonths } from "date-fns";
import { AtSteps, AtStep } from "atmosphere-ui";

import AppButton from "@/Components/shared/AppButton.vue";

import RentFormPersonal from "./RentFormPersonal.vue";
import RentFormProperty from "./RentFormProperty.vue";
import RentFormContract from "./RentFormContract.vue";
import RentFormFees from "./RentFormFees.vue";
import { dateToIso, formatDate } from "@/utils";

defineProps<{
  data: Record<string, any>;
  isProcessing: boolean;
}>();

const emit = defineEmits(["submit"]);

const rentForm = reactive({
  property_id: null,
  property: null,
  unit_id: null,
  unit: null,
  is_new_client: false,
  client_id: null,
  client_name: "",
  client: null,
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

// Wizard
const validations = [
  {
    handle: () => rentForm.client || rentForm.client_name,
  },
  {
    handle: () => rentForm.property && rentForm.unit,
  },
  {
    handle: () => rentForm.deposit && rentForm.amount,
  },
];
const validateStep = () => {
  return new Promise((resolve) => resolve(!validations[currentStep.value]?.handle()));
};
const currentStep = ref(0);
const nextButtonLabel = computed(() => {
  const labels = ["Datos de propiedad", "Datos de renta", "Cargo y Moras", "Guardar"];
  return labels[currentStep.value];
});

const handleUpdate = (data: Record<string, any>) => {
  console.log(data, "updating");
  Object.keys(rentForm).forEach((field) => {
    if (data[field]) {
      rentForm[field] = data[field];
    }
  });
};

const onFinished = () => {
  const data = {
    ...rentForm,
    deposit_due: formatDate(rentForm.deposit_due, "y-M-d"),
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
  <section>
    <AtSteps
      v-model="currentStep"
      finish-status="success"
      simple
      style="margin-top: 20px"
      @finished="onFinished"
    >
      <AtStep name="personal" title="Datos Personales" :before-change="validateStep">
        <RentFormPersonal :model-value="rentForm" @update:model-value="handleUpdate" />
      </AtStep>
      <AtStep name="property" title="Propiedad" :before-change="validateStep">
        <RentFormProperty :model-value="rentForm" @update:model-value="handleUpdate" />
      </AtStep>
      <AtStep name="rent_details" title="Detalles de renta" :before-change="validateStep">
        <RentFormContract :model-value="rentForm" @update:model-value="handleUpdate" />
      </AtStep>
      <AtStep name="fees" title="Cargos y mora">
        <RentFormFees :model-value="rentForm" @update:model-value="handleUpdate" />
      </AtStep>

      <template v-slot:footer="{ prev, next }">
        <footer class="flex justify-end space-x-2">
          <AtButton type="secondary" rounded @click="prev()">Atras</AtButton>
          <AppButton
            variant="inverse"
            rounded
            :processing="isProcessing"
            :disabled="isProcessing"
            @click="next()"
          >
            {{ nextButtonLabel }}
          </AppButton>
        </footer>
      </template>
    </AtSteps>
  </section>
</template>
