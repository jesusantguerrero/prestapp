<script setup lang="ts">
import { computed, useSlots } from "vue";
import SectionTitle from "./SectionTitle.vue";

defineEmits(["submitted"]);

defineProps<{
  title?: string;
  description?: string;
}>();

const hasActions = computed(() => !!useSlots().actions);
</script>

<template>
  <div class="space-y-4">
    <SectionTitle>
      <template #title>
        <slot name="title">
          {{ title }}
        </slot>
      </template>
      <template #description>
        <slot name="description">
          {{ description }}
        </slot>
      </template>
    </SectionTitle>

    <div class="mt-5 md:mt-0 md:col-span-2">
      <section>
        <div
          class="px-4 py-5 bg-white sm:p-6 shadow"
          :class="hasActions ? 'sm:rounded-tl-md sm:rounded-tr-md' : 'sm:rounded-md'"
        >
          <div class="grid grid-cols-6 gap-6">
            <slot name="form" />
          </div>
        </div>

        <div
          v-if="hasActions"
          class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md"
        >
          <slot name="actions" />
        </div>
      </section>
    </div>
  </div>
</template>
