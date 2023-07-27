<script lang="ts" setup>
// @ts-ignore
import AppButtonTab from "@/Components/shared/AppButtonTab.vue";
import { AtInput } from "atmosphere-ui";

defineProps<{
  modelValue: string;
}>();

const emit = defineEmits(["update:model-value"]);
</script>

<template>
  <div class="d-flex py-2">
    <AtInput
      name=""
      :value="modelValue"
      @update:model-value="$emit('update:model-value', $event.target.value)"
      class="form-control border-none hover:border"
      rounded
    />
    <section>
      <AppButtonTab>
        <IMdiFormatAlignJustify />
      </AppButtonTab>
      <AppButtonTab>
        <IMdiFormatAlignJustify />
      </AppButtonTab>
    </section>
  </div>
</template>
>
