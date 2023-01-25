<script lang="ts" setup>
import BaseSelect from "../BaseSelect.vue";

const props = defineProps<{
  modelValue: string | Object;
}>();

defineEmits(["update:modelValue"]);
</script>

<template>
  <BaseSelect
    :endpoint="`/loan-accounts`"
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    placeholder="Selecciona una cuenta"
    label="alias"
    track-by="id"
  />
</template>
