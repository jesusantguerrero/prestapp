<template>
  <multiselect class="base-select" v-bind="$props" v-on="multiselectListeners" />
</template>

<script>
export default {
  name: "BaseSelect",
  props: {
    id: [String, Number],
    modelValue: [Object, Array, String, Number],
    options: Array,
    disabled: Boolean,
    trackBy: {
      type: String,
      default: "value",
    },
    label: {
      default: "label",
    },
    placeholder: {
      type: String,
      default: "Type and select option…",
    },
    hideSelected: {
      default: true,
    },
    showLabels: {
      default: false,
    },
  },
  emits: ["input", "select", "remove", "searchChange", "open", "close"],
  setup(props, { emit }) {
    const multiselectListeners = {
      "update:modelValue": (value) => emit("update:modelValue", value),
      select: (selectedOption) => emit("select", selectedOption),
      remove: (removedOption) => emit("remove", removedOption),
      searchChange: (searchQuery) => emit("searchChange", searchQuery),
      open: (id) => emit("open", id),
      close: (value) => emit("close", value),
    };

    return { multiselectListeners };
  },
};
</script>

<style lang="scss">
@import "vue-multiselect/dist/vue-multiselect.css";
</style>
