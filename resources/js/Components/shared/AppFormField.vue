<script setup lang="ts">
import { AtField, AtInput } from "atmosphere-ui";

defineProps<{
  label: string;
  modelValue?: any;
}>();
</script>

<template>
  <AtField :label="label" class="w-full text-secondary font-bold">
    <slot>
      <AtInput
        :model-value="modelValue"
        @update:modelValue="$emit('update:modelValue')"
        rounded
        class="bg-neutral/20 shadow-none border-neutral hover:border-secondary/60"
      />
    </slot>
  </AtField>
</template>
