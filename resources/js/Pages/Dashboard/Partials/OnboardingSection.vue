<script setup lang="ts">
import ApplicationMark from "@/Components/ApplicationMark.vue";
import AppButton from "@/Components/shared/AppButton.vue";

defineProps<{
  steps: any[];
  title: string;
  description?: string;
}>();

defineEmits(["close"]);
</script>

<template>
  <main>
    <header class="flex justify-between mb-2">
      <h4 class="font-bold">
        {{ title }}
        <small v-if="description"> {{ description }}</small>
      </h4>
      <AppButton variant="neutral" @click="$emit('close')">
        <IMdiClose />
      </AppButton>
    </header>
    <section class="items-center justify-between mb-8 md:flex md:space-x-4">
      <div class="flex justify-center mb-4 min-w-max md:block">
        <article class="relative">
          <ApplicationMark class="w-48 h-48 text-body-1" />
          <small class="absolute italic bottom-5">Property management system</small>
        </article>
      </div>
      <section
        class="flex-col items-center w-full px-4 py-4 space-y-8 overflow-auto rounded-md shadow-md md:flex-row md:space-y-0 md:flex bg-base-lvl-3 md:space-x-12"
      >
        <article v-for="(step, index) in steps" class="text-body-1">
          <h4 class="font-bold text-secondary">
            {{ index + 1 }} -
            {{ $t(step.title) }}
          </h4>
          <p class="mt-4 text-body">{{ $t(step.description) }}</p>
          <AppButton
            :variant="step.action.variant"
            class="mt-6"
            @click="step.action.event()"
          >
            {{ $t(step.action.label) }}
          </AppButton>
        </article>
      </section>
    </section>
  </main>
</template>
