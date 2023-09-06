<script setup lang="ts">
import { computed, toRefs } from "vue";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "./FormSection.vue";

import { IProperty, IUnit } from "@/Modules/properties/propertyEntity";
import { useReactiveForm } from "@/utils/useReactiveForm";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { formatMoney } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";

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

const propertyLabel = (property: IProperty) => {
  return `${property.name} [${
    property.units.filter((unit) => unit.status !== "RENTED").length
  }] (${property.address}) `;
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
          :custom-label="propertyLabel"
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
        >
          <template v-slot:option="{ option }">
            <article>
              <UnitTitle
                :title="option.name"
                :owner-name="formData.property.owner_name"
                :price="formatMoney(option.price)"
              />
              <section class="flex mt-2 space-x-2 text-gray-500">
                <span class="flex items-center space-x-1">
                  <i-ic-sharp-photo-size-select-small />
                  <span>
                    {{ option.area ?? 0 }}
                  </span>
                </span>
                <span class="flex items-center space-x-1"
                  ><IIcTwotoneBed />
                  <span>
                    {{ option.bedrooms ?? 0 }}
                  </span>
                </span>
                <span class="flex items-center space-x-1"
                  ><IIcTwotoneBathtub />
                  <span>
                    {{ option.bathrooms ?? 0 }}
                  </span>
                </span>
              </section>
            </article>
          </template>
        </BaseSelect>
      </AppFormField>
    </FormSection>
  </section>
</template>
