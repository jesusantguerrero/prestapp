<template>
  <multiselect
    class="h-full base-select"
    :id="id"
    :modelValue="modelValue"
    :disabled="disabled"
    :trackBy="trackBy"
    :loading="isLoading"
    :label="label"
    :tag="tag"
    :internal-search="!endpoint"
    :placeholder="placeholder"
    :hideSelected="hideSelected"
    :showLabels="showLabels"
    :allowCreate="allowCreate"
    :customLabel="customLabel"
    :options="localOptions"
    :size="size"
    v-on="multiselectListeners"
  >
    <template v-slot:singleLabel="{ option }">
      <slot name="singleLabel" :option="option" />
    </template>
    <template v-slot:option="{ option }">
      <slot name="option" :option="option" />
    </template>
  </multiselect>
</template>

<script setup lang="ts">
import axios from "axios";
import { debounce } from "lodash";
import { ref } from "vue";

type SelectOption = Object | any[] | string | number | null;

interface Props {
  modelValue: SelectOption;
  trackBy: string;
  label: string;
  id?: string | number;
  options?: any[];
  disabled?: boolean;
  placeholder?: string;
  hideSelected?: boolean;
  showLabels?: boolean;
  endpoint?: string;
  size?: string;
  tag?: boolean;
  allowCreate?: boolean;
  customLabel?: Function;
}

const props = withDefaults(defineProps<Props>(), {
  trackBy: "value",
  label: "label",
  placeholder: "Type and select option…",
  hideSelected: false,
  showLabels: false,
  size: "large",
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

const localOptions = ref(props.options ?? []);
const isLoading = ref(false);

const resultParser = (apiOptions: Record<string, string>[], query: string = "") => {
  let includeCustom = true;
  const originalMap = apiOptions.map((option) => {
    const optionLabel = props.label ? option[props.label] : option.label;
    if (includeCustom && query && optionLabel.toLowerCase().includes(query))
      includeCustom = false;

    return {
      ...option,
      [props.label]: optionLabel,
      [props.trackBy]: props.trackBy ? option[props.trackBy] : option.id,
    };
  });

  const custom = includeCustom
    ? [
        {
          [props.label]: query,
          [props.trackBy]: `new::${query}`,
        },
      ]
    : [];

  return [...custom, ...originalMap];
};

const handleSearch = debounce((query) => {
  if (!query.length) {
    localOptions.value = [];
    return;
  }
  isLoading.value = true;
  const params = props.endpoint?.includes("?") ? `&search=${query}` : `?search=${query}`;

  axios
    .get(`${props.endpoint}${params}`)
    .then(({ data }) => {
      localOptions.value = resultParser(Array.isArray(data) ? data : data.data, query);
    })
    .finally(() => {
      isLoading.value = false;
    });
}, 400);

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
      handleSearch(searchQuery);
    } else {
      emit("searchChange", searchQuery);
    }
  },
  value: (optionId: string, option?: Record<string, string>) => {
    const optionData =
      option ?? localOptions.value.find((option) => option.value == optionId);
    selected.value = optionData;
    emit("update:modelValue", optionId);
    emit("update:value", optionData);
    emit("update:label", optionData?.label);
  },
};
</script>

<style lang="scss">
@import "vue-multiselect/dist/vue-multiselect.css";
</style>

<style lang="scss">
.multiselect__option--highlight {
  @apply bg-primary;
}

.multiselect__tags,
.multiselect__single,
.multiselect__input {
  @apply bg-base-lvl-2;
  margin-bottom: 0 !important;
}

.multiselect__single {
  display: inline-flex;
}

.multiselect__tags {
  padding-top: 0 !important;
  display: flex !important;
  align-items: center;
}

.multiselect__content-wrapper {
  &::-webkit-scrollbar-thumb {
    background-color: transparentize($color: #000000, $amount: 0.8);
    border-radius: 4px;

    &:hover {
      background-color: transparentize($color: #000000, $amount: 0.8);
    }
  }

  &::-webkit-scrollbar {
    background-color: transparent;
    width: 8px;
    height: 10px;
  }
}

.multiselect__select,
.multiselect__tags {
  height: 48px !important;
}
</style>
