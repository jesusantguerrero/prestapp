<script setup lang="ts">
import { computed } from "vue";

import ButtonGroup from "@/Components/ButtonGroup.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import { useResponsive } from "@/utils/useResponsive";

const props = defineProps<{
  modelValue: string;
  sections: Record<string, IButtonSection>;
  placeholder: string;
}>();

interface IButtonSection {
  label: string;
  link: string;
}

const options = computed(() => {
  return Object.entries(props.sections).map(([name, option]) => ({
    value: name,
    ...option,
  }));
});

const selected = computed(() => {
  return options.value.find((option) => option.value == props.modelValue);
});
const { isMobile } = useResponsive();
</script>

<template>
  <div class="h-full">
    <ButtonGroup
      v-if="!isMobile"
      class="w-full md:w-fit h-full"
      :values="sections"
      :model-value="modelValue"
      @update:modelValue="$emit('update:model-value', $event)"
    />
    <BaseSelect
      v-else
      :model-value="selected"
      :options="options"
      :placeholder="placeholder"
      @update:modelValue="$emit('update:model-value', $event.value)"
    />
  </div>
</template>
