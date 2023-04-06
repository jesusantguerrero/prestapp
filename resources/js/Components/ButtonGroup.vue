<template>
  <section
    class="flex overflow-hidden font-bold border rounded-md bg-base-lvl-3 text-body-1 border-secondary min-w-max"
  >
    <button
      v-for="(item, statusName) in values"
      class="px-2 py-1.5 w-full md:w-fit flex items-center border border-transparent hover:bg-secondary/5"
      :class="{
        'text-primary bg-base-lvl-2 border border-secondary hover:text-primary': isSelected(
          statusName
        ),
      }"
      :key="statusName"
      @click="$emit('update:modelValue', item.value || statusName)"
    >
      {{ item.label }}
    </button>
  </section>
</template>

<script setup lang="ts">
const props = defineProps({
  values: {
    type: Object,
    required: true,
  },
  modelValue: {
    type: String,
  },
});

const isSelected = (itemValue: string) => {
  return itemValue == props.modelValue;
};
</script>
