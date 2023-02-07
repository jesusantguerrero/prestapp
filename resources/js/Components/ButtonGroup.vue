<template>
  <section
    class="flex rounded-md border bg-secondary/10 font-bold text-secondary border-secondary min-w-max overflow-hidden"
  >
    <button
      v-for="(item, statusName) in values"
      class="px-2 py-1.5 flex items-center border border-transparent hover:bg-secondary/5"
      :class="{
        'text-white bg-secondary border border-secondary hover:text-secondary': isSelected(
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
