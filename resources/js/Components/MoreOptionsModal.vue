<script setup lang="ts">
import ResponsiveModal from "./ResponsiveModal.vue";

withDefaults(
  defineProps<{
    show: boolean;
    maxWidth: string;
    closeable: boolean;
    fullHeight: boolean;
    title: string;
    actions: Record<string, { label: string }>;
  }>(),
  {
    maxWidth: "2xl",
    closeable: true,
  }
);

const emit = defineEmits(["close", "saved", "action"]);

const emitAction = (actionName: string) => {
  emit("action", actionName);
  emit("close");
};
</script>

<template>
  <ResponsiveModal
    :show="show"
    :max-width="maxWidth"
    :full-height="fullHeight"
    :closeable="closeable"
    @close="$emit('update:show', false)"
  >
    <div class="pb-4 bg-base-lvl-3 sm:p-6 sm:pb-4 text-body flex-1">
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <header class="font-bold border-b pb-4 text-lg">{{ title }}</header>

        <div class="mt-2 space-y-2">
          <button
            v-ripple
            class="w-full py-2 font-bold capitalize hover:bg-base-lvl-2 overflow-hidden"
            v-for="(action, actionName) in actions"
            @click="emitAction(actionName)"
          >
            {{ $t(action?.label) }}
          </button>
        </div>
      </div>
    </div>
  </ResponsiveModal>
</template>
