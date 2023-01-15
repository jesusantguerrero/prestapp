<script setup lang="ts">
import { watch, computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { AtButton } from "atmosphere-ui";
import { addMonths } from "date-fns";

import AppButton from "@/Components/shared/AppButton.vue";

import RentFormPersonal from "./RentFormPersonal.vue";
import RentFormProperty from "./RentFormProperty.vue";
import RentFormContract from "./RentFormContract.vue";
import RentFormFees from "./RentFormFees.vue";

defineProps<{
  data: Record<string, any>;
}>();

const rentForm = useForm({
  property_id: null,
  property: null,
  unit_id: null,
  unit: null,
  is_new_client: false,
  client_id: null,
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

const addAdditionalFee = () => {
  const index = rentForm.additional_fees.length + 1;
  rentForm.additional_fees.push({
    index: index,
    concept: "",
    description: "",
    price: 0,
    quantity: 1,
    total: "",
  });
};

const validations = [
  {
    handle() {
      return new Promise((resolve) => {
        console.log(rentForm);
        const isValid = rentForm.client || rentForm.client_name;
        resolve(isValid);
      });
    },
  },
  {
    handle() {
      return new Promise((resolve) => {
        const isValid = rentForm.property && rentForm.unit;
        resolve(!isValid);
      });
    },
  },
  {
    handle() {
      return new Promise((resolve) => {
        const isValid = rentForm.deposit && rentForm.amount;
        resolve(!isValid);
      });
    },
  },
];

const validateStep = (tab: index) => {
  return validations[tab]?.handle();
};

// Wizard
const currentStep = ref(0);
const handleNext = async () => {
  if (await validateStep(currentStep.value)) {
    currentStep.value++;
  }
};
const prev = (index: number) => {
  if (currentStep.value > 0) currentStep.value--;
};
const nextButtonLabel = computed(() => {
  const labels = ["Datos de propiedad", "Datos de renta", "Cargo y Moras", "Guardar"];
  return labels[currentStep.value];
});

const handleUpdate = (data: Record<string, any>) => {
  console.log(data);
  Object.keys(rentForm).forEach((field) => {
    if (data[field]) {
      rentForm[field] = data[field];
    }
  });
};
</script>

<template>
  <section>
    <ElSteps
      :active="currentStep"
      finish-status="success"
      simple
      style="margin-top: 20px"
    >
      <ElStep title="Datos Personales" />
      <ElStep title="Propiedad" />
      <ElStep title="Detalles de renta" />
      <ElStep title="Cargos y mora" />
    </ElSteps>

    <section class="mt-8">
      <RentFormPersonal
        :model-value="rentForm"
        @update:model-value="handleUpdate"
        v-if="currentStep == 0"
      />
      <RentFormProperty v-model="rentForm" v-else-if="currentStep == 1" />
      <RentFormContract v-model="rentForm" v-else-if="currentStep == 2" />
      <RentFormFees v-else-if="currentStep == 3" />
    </section>

    <footer class="flex justify-end space-x-2">
      <AtButton type="secondary" rounded @click="prev()">Atras</AtButton>
      <AppButton variant="inverse" rounded @click="handleNext()">
        {{ nextButtonLabel }}
      </AppButton>
    </footer>
  </section>
</template>
