<script setup lang="ts">
import { computed, toRefs } from "vue";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "./FormSection.vue";
import ServiceBlock from "./ServiceBlock.vue";

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
    items: []
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
  return `${property.name} (${property.address})`;
};


const checkScroll = () => {
    nextTick(() => {
        ActionButtons.value.scrollIntoView({ smooth: true })
    })
}

// actions
const onCopy = (field) => {
    field.name = uuid();
    state.formData.fields.push({...field})
    checkScroll()
}

const onDelete = (index) => {
    state.formData.fields.splice(index, 1)
    checkScroll()
}

// Blocks
const addServiceBlock = () => {
    const index = formData.items.length + 1
    formData.items.push({
        index: index,
        product_image: '',
        concept: '',
        description: '',
        price: 0,
        quantity: 1,
        total: ''
    })
}

const onSetItem = (index: number, item) => {
    debugger
    formData.items[index] = {
        index: index,
        product_image: item.product_image,
        concept: item.concept,
        description: item.description,
        price: item.price ?? formData.items[index].price ?? 0,
        quantity: item.quantity ?? formData.items[index].quantity ?? 1,
        total: ''
    }
    if (!formData.items.at(-1).concept) addServiceBlock()
}

addServiceBlock()
addServiceBlock()
</script>

<template>
    <FormSection section-class="flex flex-col md:space-y-4">
      <div class="w-full">
        <h4 class="text-2xl font-bold">Services</h4>
      </div>
      <ServiceBlock
          v-model:items="formData.items"
          @delete="onDelete(index)"
          @copy="onCopy(field)"
          @set-item="onSetItem"
      />
    </FormSection>
</template>
