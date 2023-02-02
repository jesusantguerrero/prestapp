<template>
  <div class="flex justify-between w-full pr-8">
    <SectionNavTab
      v-for="(section, sectionName) in sections"
      @click="handleClick(section, sectionName)"
      :is-selected="isSelected(section, sectionName)"
      :key="section.url"
      :selected-class="selectedClass"
    >
      {{ section.label }}
    </SectionNavTab>
    <div class="flex items-center justify-end py-1 ml-auto space-x-2">
      <slot name="actions" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { computed } from "vue";
import SectionNavTab from "./SectionNavTab.vue";

const props = defineProps<{
  sections: Record<string, any>;
  modelValue: string;
  selectedClass: string;
}>();

const emit = defineEmits(["update:modelValue"]);

const currentPath = computed(() => {
  return document?.location?.pathname;
});

const isSelected = (section: any, sectionValueName: string | number) => {
  const sectionName = section.url || section.value || sectionValueName;
  const value = props.modelValue || currentPath.value;
  return sectionName == value;
};

const handleClick = (section: any, sectionName: string | number) => {
  if (section.url) {
    router.visit(section.url);
  } else {
    emit("update:modelValue", section.value || sectionName);
  }
};
</script>
