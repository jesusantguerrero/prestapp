<script setup lang="ts">
import { computed, toRefs } from "vue";
// @ts-expect-error
import { AtField, AtInput } from "atmosphere-ui";
import FormSection from "./FormSection.vue";
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
    <article class="w-full space-y-4">
      <FormSection title="Moras" section-class="space-y-4">
        <section class="mt-4 flex space-x-4">
          <AppButton
            v-if="!formData.has_late_fees"
            @click="formData.has_late_fees = true"
          >
            Aplicar Moras</AppButton
          >
          <template v-else>
            <AtField label="Comisión de mora" class="w-full">
              <AtInput v-model="formData.late_fee">
                <template #suffix>
                  <TaxTypeSelector v-model="formData.late_fee_type" />
                </template>
              </AtInput>
            </AtField>
            <AtField label="Dias de gracia" class="w-full">
              <AtInput v-model="formData.grace_days" />
            </AtField>
          </template>
        </section>

        <section>
          <ServiceBlock
            v-if="formData.additional_fees.length"
            v-model:items="formData.additional_fees"
            subtitle=""
            :hide-fields="['qty', 'description']"
            :labels="{
              name: 'Servicio',
              price: 'Precio',
            }"
            class="py-2"
          />
          <AtButton class="text-gray-400" @click="addAdditionalFee()">
            <i class="mr-2 fa fa-plus-circle"></i>
            Add service
          </AtButton>
        </section>
      </FormSection>
    </article>
  </section>
</template>
