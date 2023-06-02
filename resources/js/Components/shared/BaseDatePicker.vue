<script lang="ts" setup>
defineProps<{
  modelValue: Date;
}>();
</script>

<template>
  <ElDatePicker
    :model-value="modelValue"
    @update:model-value="$emit('update:model-value', $event)"
    size="large"
    class="w-full"
    rounded
  />
</template>

<style lang="scss">
.el-date-editor.el-input {
  width: auto !important;
  min-width: 100%;
}
</style>
