<script setup lang="ts">
import { watch, computed, toRefs } from "vue";
// @ts-expect-error
import { AtField } from "atmosphere-ui";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "./FormSection.vue";

import { IUnit } from "@/Modules/properties/propertyEntity";
import { useReactiveForm } from "@/utils/useReactiveForm";

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
    is_new_client: false,
    client_id: null,
  },
  modelValue,
  emit
);

const availableUnits = computed(() => {
  // @ts-expect-error
  return formData.property?.units.filter((unit: IUnit) => unit.status !== "RENTED");
});
</script>

<template>
  <section>
    <FormSection>
      <AtField class="w-full" label="Propiedad">
        <BaseSelect
          v-model="formData.property"
          endpoint="/api/properties"
          placeholder="Selecciona una propiedad"
          label="name"
          track-by="id"
        />
      </AtField>
      <AtField class="w-full" v-if="formData.property" label="Unidad">
        <BaseSelect
          v-model="formData.unit"
          :options="availableUnits"
          placeholder="Unidad #1"
          label="name"
          track-by="id"
        />
      </AtField>
    </FormSection>
  </section>
</template>