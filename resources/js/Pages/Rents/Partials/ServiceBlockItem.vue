<template>
  <div
    class="w-full text-gray-600 rounded-md form-field"
    :class="[`item-`, disabled ? 'pb-4' : 'bg-white']"
  >
    <div class="flex space-x-4">
      <AtField class="w-6/12" :label="getLabel('name')">
        <div>
          <AtInput
            v-model="item.concept"
            :placeholder="getLabel('name')"
            :disabled="disabled"
          />
        </div>
        <textarea
          v-model="item.description"
          v-if="(!isFieldHidden('description') && !disabled) || (disabled && description)"
          class="w-full transition border-gray-200 rounded-md resize-none focus:outline-none focus:border-gray-400"
          :class="[disabled ? 'border-0 mt-2 px-0' : 'border mt-4']"
          placeholder="Enter description here"
          :disabled="disabled"
        />
      </AtField>
      <div class="flex w-6/12 space-x-2">
        <AtField class="w-full" v-if="!disabled" :label="getLabel('price')">
          <AtInput v-model="item.price" number-format :disabled="disabled" />
        </AtField>
        <AtField
          class="w-full"
          v-if="!isFieldHidden('description') && !disabled"
          :label="getLabel('qty')"
        >
          <AtInput v-model="item.quantity" :disabled="disabled" />
        </AtField>
        <AtField class="w-full text-right" v-if="!disabled" :label="getLabel('total')">
          <AtInput
            disabled
            :model-value="item.price * item.quantity"
            number-format
            is-borderless
            class="text-right"
          />
        </AtField>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { AtField, AtInput } from "atmosphere-ui";
import { computed } from "vue";

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    required: false,
  },
  hideFields: {
    type: Array,
  },
  labels: {
    type: Object,
  },
});

const defaultLabels = {
  name: "Service Name",
  price: "Price",
  qty: "Qty",
  total: "Total",
};

const labels = computed(() => {
  return Object.assign(defaultLabels, props.labels);
});

const isFieldHidden = (name) => {
  return props.hideFields.includes(name);
};

const getLabel = (name) => {
  return labels.value[name] || name;
};
</script>
