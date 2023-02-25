<script setup lang="ts">
import { toRefs } from "vue";
// @ts-expect-error
import { AtField, AtInput } from "atmosphere-ui";
import FormSection from "./FormSection.vue";
import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { useReactiveForm } from "@/utils/useReactiveForm";
import AppFormField from "@/Components/shared/AppFormField.vue";

const props = defineProps<{
  modelValue: Record<string, any>;
}>();

const emit = defineEmits(["update:modelValue"]);
const { modelValue } = toRefs(props);

const { formData } = useReactiveForm(
  {
    late_fee: 10,
    late_fee_type: "PERCENTAGE",
    grace_days: 0,
    additional_fees: [],
  },
  modelValue,
  emit
);
</script>

<template>
  <section>
    <article class="w-full space-y-4">
      <FormSection title="Moras" section-class=" space-y-4">
        <section class="mt-4 flex flex-col md:flex-row md:space-x-4">
          <AppButton
            variant="neutral"
            v-if="!formData.has_late_fees"
            @click="formData.has_late_fees = true"
          >
            Aplicar Moras
          </AppButton>
          <template v-else>
            <AppFormField
              label="Comisión de mora"
              class="w-full"
              v-model="formData.late_fee"
            >
              <template #suffix>
                <TaxTypeSelector v-model="formData.late_fee_type" />
              </template>
            </AppFormField>
            <AppFormField
              label="Dias de gracia"
              class="w-full"
              v-model="formData.grace_days"
            />
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
