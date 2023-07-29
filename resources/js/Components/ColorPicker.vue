<template>
  <div ref="ColorPicker" class="color-picker">
      <input
          :value="modelValue"
          @input="$emit('update:modelValue', $event.target?.value)"
          type="text"
          :placeholder="placeholder"
          class="color-picker__input form-control"
      >
      <input
          id=""
          ref="colorPickerRef"
          :value="modelValue"
          @change="$emit('update:modelValue', $event.target?.value)"
          type="color"
          class="color-picker__picker"
      >
      <div class="color-picker__preview" @click="onPickerClick()" />
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';

  const props = defineProps({
      placeholder: {
          type: String,
          default: ""
      },
      modelValue: {
          type: String,
          required: true
      }
  });

  const colorPickerRef = ref<HTMLInputElement>();
function setColor() {
    const documentRoot = colorPickerRef.value;
    documentRoot && documentRoot.style.setProperty("--selected-color", props.modelValue);
}

function onPickerClick () {
  colorPickerRef.value?.click()
}

onMounted(() => {
  setColor();
})

</script>

<style lang="scss">
.color-picker {
  display: flex;
  position: relative;

  &__input.form-control {
      border: none;
      color: #707070;
      padding-left: 0;
      padding-right: 0;
      background: transparent;
      border-bottom: 1px solid #C9C9C9;
      border-radius: 0 0 0 0 !important;
      cursor: pointer;
      font-weight: 600;
  }

  &__picker {
      background: var(--selected-color);
      width: 0px;
      height: 0px;
      opacity: 0;
  }

  &__preview {
      position: absolute;
      background: v-bind(modelValue);
      width: 20px;
      height: 20px;
      border-radius: 4px;
      right: 5px;
      bottom: 5px;
      border: 1px solid  #C9C9C9;
      cursor: pointer;
  }
}
</style>
