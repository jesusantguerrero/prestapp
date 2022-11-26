<template>
  <TransitionRoot appear :show="isOpenLocal" as="template">
    <Dialog as="div" static :open="isOpenLocal" @close="setIsOpen">
      <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="min-h-screen px-4 text-center">
          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0"
            enter-to="opacity-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100"
            leave-to="opacity-0"
          >
            <DialogOverlay class="fixed inset-0" />
          </TransitionChild>

          <span class="inline-block h-screen align-middle" aria-hidden="true">
            &#8203;
          </span>

          <TransitionChild
            as="template"
            enter="duration-300 ease-out"
            enter-from="opacity-0 scale-95"
            enter-to="opacity-100 scale-100"
            leave="duration-200 ease-in"
            leave-from="opacity-100 scale-100"
            leave-to="opacity-0 scale-95"
          >
            <div
              class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border"
            >
              <DialogTitle
                as="h3"
                class="text-lg font-medium leading-6 text-gray-900 mt-0"
              >
                <slot name="title">
                  {{ title }}
                </slot>
              </DialogTitle>

              <div class="mt-2">
                <slot name="body">
                  <p class="text-sm text-gray-500">
                    {{ text }}
                  </p>
                </slot>
              </div>

              <div class="mt-2">
                <slot name="footer">
                  <div class="mt-4 space-x-2 flex justify-end">
                    <button
                      v-for="(button, index) in buttons"
                      :key="`button-${index}`"
                      type="button"
                      class="inline-flex justify-center px-4 py-2 text-sm font-medium border border-transparent rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
                      :class="button.classes"
                      @click="$emit('command', button.name)"
                    >
                      {{ button.text }}
                    </button>
                  </div>
                </slot>
              </div>
            </div>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { ref, toRefs, watch } from "vue";
import {
  TransitionRoot,
  TransitionChild,
  Dialog,
  DialogOverlay,
  DialogTitle,
} from "@headlessui/vue";

export default {
  props: {
    isOpen: {
      type: Boolean,
    },
    title: {
      type: String,
    },
    message: {
      type: String,
    },
    buttons: {
      type: Array,
    },
  },
  components: {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogOverlay,
    DialogTitle,
  },

  setup(props, { emit }) {
    const isOpenLocal = ref(true);
    const { isOpen } = toRefs(props);

    const setIsOpen = (value) => {
      isOpenLocal.value = value || false;
    };

    watch(
      isOpen,
      (value) => {
        if (value != isOpenLocal) {
          setIsOpen(value);
        }
      },
      { immediate: true }
    );

    watch(isOpenLocal, (value) => {
      emit("update:isOpen", value);
    });

    return {
      isOpen,
      isOpenLocal,
      setIsOpen,
    };
  },
};
</script>
