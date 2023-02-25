<script setup lang="ts">
import { toRefs } from "vue";

import FormSection from "./FormSection.vue";
import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";

// @ts-expect-error
import { AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { useReactiveForm } from "@/utils/useReactiveForm";
import { addMonths } from "date-fns";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";
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
    <FormSection
      title="Datos de deposito"
      section-class="flex flex-col md:space-x-4 md:flex-row"
    >
      <AppFormField label="Deposito" class="w-full">
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
      </AppFormField>
      <AppFormField
        label="Fecha de pago deposito"
        class="flex flex-col w-full -mt-8 md:mt-0"
      >
        <ElDatePicker v-model="formData.deposit_due" size="large" class="w-full" />
      </AppFormField>
    </FormSection>

    <FormSection
      title=""
      v-if="formData.is_deposit_received"
      section-class="flex w-full space-x-4"
    >
      <AppFormField
        label="Referencia"
        class="w-full"
        v-model="formData.deposit_reference"
      />

      <AppFormField label="Cuenta de pago" class="w-full">
        <AccountSelect v-model="formData.payment_account_id" rounded />
      </AppFormField>

      <AppFormField label="Metodo de pago" class="w-full">
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
    </FormSection>

    <FormSection
      title="Datos de renta"
      section-class="flex flex-col md:grid md:grid-cols-2 md:gap-2"
    >
      <AppFormField
        label="Precio de renta"
        class="w-full"
        :number-format="true"
        v-model="formData.amount"
      />
      <AppFormField label="Comisión" class="w-full" v-model="formData.commission">
        <template #suffix>
          <TaxTypeSelector
            :model-value="formData.commission_type"
            @update:modelValue="formData.commission_type = $event"
          />
        </template>
      </AppFormField>
      <AppFormField label="Fecha de Inicio" class="flex flex-col">
        <ElDatePicker v-model="formData.date" size="large" />
      </AppFormField>
      <AppFormField label="Fecha de primer pago" class="flex flex-col">
        <ElDatePicker v-model="formData.first_invoice_date" size="large" />
      </AppFormField>
      <AppFormField label="Finaliza en" class="flex flex-col">
        <ElDatePicker v-model="formData.end_date" size="large" />
      </AppFormField>
    </FormSection>
  </section>
</template>
