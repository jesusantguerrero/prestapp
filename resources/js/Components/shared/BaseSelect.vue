<template>
  <multiselect
    class="base-select"
    :id="id"
    :modelValue="modelValue"
    :disabled="disabled"
    :trackBy="trackBy"
    :loading="isLoading"
    :label="label"
    :internal-search="!endpoint"
    :placeholder="placeholder"
    :hideSelected="hideSelected"
    :showLabels="showLabels"
    :allowCreate="allowCreate"
    :customLabel="customLabel"
    :options="localOptions"
    v-on="multiselectListeners"
  />
</template>

<script setup lang="ts">
import axios from "axios";
import { debounce } from "lodash";
import { ref, computed } from "vue";

type SelectOption = Object | any[] | string | number;

interface Props {
  id: string | number;
  modelValue: SelectOption;
  options: any[];
  disabled: boolean;
  trackBy: string;
  label: string;
  placeholder?: string;
  hideSelected: boolean;
  showLabels: boolean;
  endpoint?: string;
  allowCreate: boolean;
  customLabel?: Function;
}

const props = withDefaults(defineProps<Props>(), {
  trackBy: "value",
  label: "label",
  placeholder: "Type and select option…",
  hideSelected: true,
  showLabels: false,
});

const emit = defineEmits([
  "update:modelValue",
  "input",
  "select",
  "remove",
  "searchChange",
  "open",
  "close",
  "update:label",
]);

const multiselectListeners = {
  "update:modelValue": (value: SelectOption) => emit("update:modelValue", value),
  select: (selectedOption: string) => emit("select", selectedOption),
  remove: (removedOption: string) => emit("remove", removedOption),
  close: (value: string) => emit("close", value),
  open: (id: string) => {
    emit("open", id);
    if (props.endpoint) {
      handleSearch(" ");
    }
  },
  searchChange: (searchQuery: string) => {
    if (props.endpoint) {
      console.log(searchQuery);
      handleSearch(searchQuery);
    } else {
      emit("searchChange", searchQuery);
    }
  },
};

const localOptions = ref([]);
const isLoading = ref(false);

const handleSearch = debounce((query) => {
  if (!query.length) {
    localOptions.value = [];
    return;
  }
  isLoading.value = true;
  axios
    .get(`${props.endpoint}?q=${query}`)
    .then(({ data }) => {
      localOptions.value = data;
    })
    .finally(() => {
      isLoading.value = false;
    });
}, 200);
</script>

<style lang="scss">
@import "vue-multiselect/dist/vue-multiselect.css";
</style>
