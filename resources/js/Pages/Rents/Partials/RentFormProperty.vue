<script setup lang="ts">
import { computed, toRefs } from "vue";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "./FormSection.vue";

import { IUnit } from "@/Modules/properties/propertyEntity";
import { useReactiveForm } from "@/utils/useReactiveForm";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { formatMoney } from "@/utils";

const props = defineProps<{
  modelValue: Record<string, any>;
}>();
const { modelValue } = toRefs(props);
const emit = defineEmits(["update:modelValue"]);

const { formData } = useReactiveForm(
  {
    property_id: null,
    property: null,
    unit_id: null,
    unit: null,
  },
  modelValue,
  emit
);

const availableUnits = computed(() => {
  // @ts-expect-error
  return formData.property?.units.filter((unit: IUnit) => unit.status !== "RENTED");
});

const unitLabel = (unit: IUnit) => {
  return `${unit.name} (${formatMoney(unit.price)})`;
};
</script>

<template>
  <section>
    <FormSection section-class="flex flex-col md:space-x-4 md:flex-row">
      <AppFormField class="w-full" label="Propiedad">
        <BaseSelect
          v-model="formData.property"
          endpoint="/api/properties"
          placeholder="Selecciona una propiedad"
          label="name"
          track-by="id"
        />
      </AppFormField>
      <AppFormField class="w-full" v-if="formData.property" label="Unidad">
        <BaseSelect
          v-model="formData.unit"
          :options="availableUnits"
          placeholder="Seleccione unidad"
          label="name"
          track-by="id"
          :custom-label="unitLabel"
        />
      </AppFormField>
    </FormSection>
  </section>
</template>
