<script setup lang="ts">
import { AtField, AtInput } from "atmosphere-ui";

defineProps<{
  label: string;
  modelValue?: any;
  required?: boolean;
}>();
</script>

<template>
  <AtField :label="label" class="w-full text-secondary font-bold" :required="required">
    <slot>
      <AtInput
        :model-value="modelValue"
        @update:modelValue="$emit('update:modelValue', $event)"
        rounded
        :required="required"
        class="bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60"
      />
    </slot>
  </AtField>
</template>

<style lang="scss">
.form-group[required] {
  label::after {
    content: "*";
    @apply text-error/80;
  }
}
</style>
