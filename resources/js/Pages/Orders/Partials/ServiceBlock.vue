<script setup lang="ts">
import ServiceBlockItem from "./ServiceBlockItem.vue";

defineProps({
  disabled: {
    type: Boolean,
    required: false,
  },
  title: {
    type: String,
  },
  subtitle: {
    type: String,
    default: "describe and price the services you'll be delivering.",
  },
  items: {
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
</script>

<template>
  <div
    class="w-full text-gray-600 rounded-md form-field"
    :class="[disabled ? 'pb-4' : ' bg-white space-y-1']"
  >
    <h1 class="mb-0 text-xl font-bold" v-if="title">{{ title }}</h1>
    <p class="my-0 first-letter:uppercase" v-if="subtitle">{{ $t(subtitle) }}</p>

    <section>
      <ServiceBlockItem
        v-for="(item, index) in items"
        :key="index"
        :item="item"
        @update:item="$emit('update:item', $event)"
        :disabled="disabled"
        :hide-fields="hideFields"
        :labels="labels"
        @set-item="$emit('set-item', index, $event)"
      />
    </section>
  </div>
</template>
