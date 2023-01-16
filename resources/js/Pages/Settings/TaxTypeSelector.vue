<template>
  <div class="h-10 mt-auto taxes-box__type">
    <div class="flex h-full">
      <button
        v-for="type in state.types"
        class="w-6 px-1 text-gray-400 border border-gray-200 focus:outline-none"
        :class="{ 'bg-gray-200 text-gray-600': modelValue === type.value }"
        @click="setType(type.value)"
      >
        {{ type.name }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { reactive, watch } from "vue";
import IconTrash from "@/Components/icons/IconTrash.vue";

const props = defineProps({
  modelValue: {
    type: String,
  },
});

const emit = defineEmits(["update:modelValue"]);

const state = reactive({
  types: [
    {
      name: "%",
      value: "PERCENTAGE",
    },
    {
      name: "$",
      value: "FIXED",
    },
  ],
});

watch(
  () => props.modelValue,
  () => {
    state.taxData = props.modelValue;
  }
);

const setType = (type) => {
  emit("update:modelValue", type);
};
</script>
