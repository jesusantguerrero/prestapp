<script lang="ts" setup>
import { computed } from "vue";
import AppButton from "@/Components/shared/AppButton.vue";

const props = defineProps({
  title: {
    type: String,
    default: "",
  },
  extractTitle: {
    type: String,
    default: "",
  },
  name: {
    type: String,
    required: true,
  },
  resource: {
    type: Object,
    default() {
      return {};
    },
  },
  isEditing: {
    type: Boolean,
    default: false,
  },
  showEdit: {
    type: Boolean,
    default: false,
  },
  hideAction: {
    type: Boolean,
    default: false,
  },
});

const sectionTitle = computed(() => {
  if (props.resource && props.extractTitle) {
    return props.resource[props.extractTitle];
  }
  return props.title ? props.title : `Create a new ${props.name}`;
});

const saveButtonTitle = computed(() => {
  return props.resource && props.resource.id
    ? `Update ${props.name}`
    : `Save ${props.name}`;
});
const editButtonTitle = computed(() => {
  return `Edit ${props.name}`;
});
const createButtonTitle = computed(() => {
  return `Create ${props.name}`;
});
</script>

<template>
  <header
    class="flex flex-col md:flex-row items-center justify-between w-full px-5 py-2 border rounded-lg bg-base-lvl-3 border-base-lvl-3 no-print"
  >
    <h2
      class="w-full flex items-center text-sm md:text-xl font-semibold leading-tight text-gray-500"
    >
      <span class="mr-2 font-bold text-primary capitalize"> {{ name }} </span>
      <span v-if="resource && resource.id">
        {{ sectionTitle }}
      </span>
      <span
        class="px-2 py-1 ml-2 text-xs font-bold text-green-600 capitalize bg-green-100 rounded-3xl"
        v-if="isEditing"
        >editing</span
      >
    </h2>
    <div class="w-full">
      <slot name="actions" v-if="$slots.actions || !hideAction">
        <div class="button-container">
          <div v-if="!resource || resource.id" class="flex">
            <AppButton @click="$emit('saved')" v-if="!showEdit">
              {{ saveButtonTitle }}
            </AppButton>
            <AppButton @click="$emit('edit')" v-if="showEdit">
              {{ editButtonTitle }}
            </AppButton>
          </div>
          <AppButton v-else @click="$emit('create')">
            {{ createButtonTitle }}
          </AppButton>
        </div>
      </slot>
    </div>
  </header>
</template>
