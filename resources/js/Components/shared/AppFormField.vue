<script setup lang="ts">
// @ts-ignore
import { AtField, AtInput } from "atmosphere-ui";

withDefaults(
  defineProps<{
    label: string;
    modelValue?: any;
    required?: boolean;
    placeholder?: string;
    disabled?: boolean;
    numberFormat?: boolean;
    row?: boolean;
  }>(),
  {
    required: false,
  }
);
</script>

<template>
  <AtField
    :label="label"
    class="w-full capitalize text-secondary font-bold"
    :class="row ? 'flex space-x-2' : ''"
    :required="required"
  >
    <slot>
      <AtInput
        :model-value="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
        rounded
        :required="required"
        :placeholder="placeholder"
        :disabled="disabled"
        :number-format="numberFormat"
        class="bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60"
      >
        <template #suffix>
          <slot name="suffix" />
        </template>
        <template #prefix>
          <slot name="prefix" />
        </template>
      </AtInput>
    </slot>
  </AtField>
</template>

<style lang="scss">
.form-group[required="true"] {
  label::after {
    content: "*";
    @apply text-error/80;
  }
}
</style>
