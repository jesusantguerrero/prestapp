<script setup lang="ts">
import { toRefs } from "vue";

import FormSection from "./FormSection.vue";
import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";

// @ts-expect-error
import { AtInput, AtSimpleSelect } from "atmosphere-ui";
import { useReactiveForm } from "@/utils/useReactiveForm";
import { addMonths } from "date-fns";
import { paymentMethods } from "@/Modules/loans/constants";
import AppFormField from "@/Components/shared/AppFormField.vue";

const props = defineProps<{
  modelValue: Record<string, any>;
}>();
const emit = defineEmits(["update:modelValue"]);

const { modelValue } = toRefs(props);

const { formData } = useReactiveForm(
  {
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
    commission: 10,
    commission_type: "PERCENTAGE",
    frequency: "MONTHLY",
  },
  modelValue,
  emit
);
</script>

<template>
  <section>
    <FormSection title="Datos de pago" section-class="grid grid-cols-2 gap-2 md:flex-row">
      <AppFormField label="Monto abonado" class="w-full">
        <AtInput
          :number-format="true"
          :model-value="formData.deposit"
          @update:modelValue="formData.deposit = $event"
          rounded
        >
          <template #suffix>
            <button
              class="w-44 px-2 transition"
              :class="[
                formData.is_deposit_received
                  ? 'bg-success text-white font-bold'
                  : 'bg-base-lvl-2 text-body-1',
              ]"
              @click="formData.is_deposit_received = !formData.is_deposit_received"
            >
              {{ formData.is_deposit_received ? "Pagado" : "No Pagado" }}
            </button>
          </template>
        </AtInput>
      </AppFormField>
      <AppFormField
        label="Metodo de pago"
        class="w-full"
        v-if="formData.is_deposit_received"
      >
        <AtSimpleSelect
          v-model="formData.payment_method_id"
          v-model:selected="formData.paymentMethod"
          :options="paymentMethods"
          placeholder="Seleccione metodo de pago"
          class="w-full"
          label="name"
          key-track="id"
        />
      </AppFormField>
      <AppFormField label="Fecha de pago" class="flex flex-col w-full -mt-8 md:mt-0">
        <ElDatePicker v-model="formData.deposit_due" size="large" class="w-full" />
      </AppFormField>
    </FormSection>
  </section>
</template>
