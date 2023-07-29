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
  if (section.url || section.to) {
    router.visit(section.url || section.to);
  } else {
    emit("update:modelValue", section.value || sectionName);
  }
};
</script>

<template>
  <div class="flex justify-between w-full md:pr-8">
    <SectionNavTab
      v-for="(section, sectionName) in sections"
      @click="handleClick(section, sectionName)"
      :current-value="modelValue"
      :key="section.url ?? section.value ?? sectionName"
      :value="section.url ?? section.to ?? section.value ?? sectionName"
      :is-active-function="section.isActiveFunction"
      :selected-class="selectedClass"
      :label="section.label"
    >
      <slot name="title" :tab="section" :tabName="sectionName" />
    </SectionNavTab>
    <div class="flex items-center justify-end py-1 ml-auto space-x-2">
      <slot name="actions" />
    </div>
  </div>
</template>
