<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import SectionNavTab from "./SectionNavTab.vue";

withDefaults(
  defineProps<{
    modelValue?: string;
    sections: Record<string, any>;
    selectedClass?: string;
  }>(),
  {
    selectedClass: "border-primary text-primary",
  }
);

const emit = defineEmits(["update:modelValue"]);

const handleClick = (section: any, sectionName: string | number) => {
  if (section.url) {
    router.visit(section.url);
  } else {
    emit("update:modelValue", section.value || sectionName);
  }
};
</script>

<template>
  <div class="flex justify-between w-full pr-8">
    <SectionNavTab
      v-for="(section, sectionName) in sections"
      @click="handleClick(section, sectionName)"
      :current-value="modelValue"
      :key="section.url ?? section.value ?? sectionName"
      :value="section.url ?? section.value ?? sectionName"
      :is-active-function="section.isActiveFunction"
      :selected-class="selectedClass"
      :label="section.label"
    />
    <div class="flex items-center justify-end py-1 ml-auto space-x-2">
      <slot name="actions" />
    </div>
  </div>
</template>
