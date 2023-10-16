<script setup lang="ts">
import { toRefs } from "vue";

import FormSection from "./FormSection.vue";
import ServiceBlock from "./ServiceBlock.vue";

import { useReactiveForm } from "@/utils/useReactiveForm";
import AppButton from "@/Components/shared/AppButton.vue";

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
    lines: [],
  },
  modelValue,
  emit
);

const checkScroll = () => {
  nextTick(() => {
    ActionButtons.value.scrollIntoView({ smooth: true });
  });
};

// actions
const onCopy = (field) => {
  field.name = uuid();
  state.formData.fields.push({ ...field });
  checkScroll();
};

const onDelete = (index) => {
  state.formData.fields.splice(index, 1);
  checkScroll();
};

// Blocks
const addServiceBlock = () => {
  if (formData.lines.length && !formData.lines.at(-1)?.concept) return;
  const index = formData.lines.length + 1;
  formData.lines.push({
    index: index,
    product_image: "",
    concept: "",
    description: "",
    price: 0,
    quantity: 1,
    total: "",
  });
};

const onSetItem = (index: number, item: Record<string, string>) => {
  formData.lines[index] = {
    index: index,
    product_image: item.product_image,
    concept: item.concept,
    description: item.description,
    price: item.price ?? formData.lines[index].price ?? 0,
    quantity: item.quantity ?? formData.lines[index].quantity ?? 1,
    total: item.total,
  };
  addServiceBlock();
};

addServiceBlock();
</script>

<template>
  <FormSection section-class="flex flex-col md:space-y-4">
    <div class="w-full">
      <h4 class="text-2xl font-bold capitalize">{{ $t("services") }}</h4>
    </div>
    <ServiceBlock
      v-model:items="formData.lines"
      @delete="onDelete(index)"
      @copy="onCopy(field)"
      @set-item="onSetItem"
    />
    <AppButton @click="addServiceBlock()" rounded class="w-fit" variant="neutral">
      <IMdiAdd class="w-4 h-4 mr-1" />
      {{ $t("add item") }}
    </AppButton>
  </FormSection>
</template>
