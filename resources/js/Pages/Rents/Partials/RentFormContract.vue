<script setup lang="ts">
import { toRefs } from "vue";

import FormSection from "./FormSection.vue";
import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";

// @ts-expect-error
import { AtField, AtInput } from "atmosphere-ui";
import { useReactiveForm } from "@/utils/useReactiveForm";
import { addMonths } from "date-fns";

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
    commission: 10,
    commission_type: "",
    frequency: "MONTHLY",
  },
  modelValue,
  emit
);
</script>

<template>
  <section>
    <FormSection title="Datos de deposito">
      <AtField label="Deposito" class="w-full">
        <AtInput
          :number-format="true"
          :model-value="formData.deposit"
          @update:modelValue="formData.deposit = $event"
          rounded
        >
          <template #suffix>
            <button
              class="w-32 px-2 transition"
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
      </AtField>
      <AtField label="Fecha de pago deposito" class="flex flex-col w-full">
        <ElDatePicker v-model="formData.deposit_due" size="large" class="w-full" />
      </AtField>
    </FormSection>

    <FormSection
      title=""
      v-if="formData.is_deposit_received"
      section-class="flex w-full space-x-4"
    >
      <AtField label="Referencia" class="w-full">
        <AtInput v-model="formData.deposit_reference" rounded />
      </AtField>

      <AtField label="Cuenta de pago" class="w-full">
        <AtInput :number-format="true" v-model="formData.payment_account_id" rounded />
      </AtField>

      <AtField label="Metodo de pago" class="w-full">
        <AtInput v-model="formData.payment_method" rounded />
      </AtField>
    </FormSection>

    <FormSection title="Datos de renta" section-class="flex space-x-4">
      <AtField label="Precio de renta" class="w-full">
        <AtInput :number-format="true" v-model="formData.amount" />
      </AtField>
      <AtField label="Comisión" class="w-full">
        <AtInput v-model="formData.commission">
          <template #suffix>
            <TaxTypeSelector
              :model-value="formData.commission_type"
              @update:modelValue="formData.commission_type = $event"
            />
          </template>
        </AtInput>
      </AtField>
      <AtField label="Fecha de Inicio" class="flex flex-col">
        <ElDatePicker v-model="formData.date" size="large" />
      </AtField>
      <AtField label="Fecha de primer pago" class="flex flex-col">
        <ElDatePicker v-model="formData.first_invoice_date" size="large" />
      </AtField>
    </FormSection>
  </section>
</template>
