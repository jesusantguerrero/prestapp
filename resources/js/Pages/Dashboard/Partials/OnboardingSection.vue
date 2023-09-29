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
    <section class="mb-8 md:flex items-center justify-between md:space-x-4">
      <div class="mb-4 min-w-max flex justify-center md:block">
        <article class="relative">
          <ApplicationMark class="h-48 w-48 text-body-1" />
          <small class="absolute bottom-5 italic">Property management system</small>
        </article>
      </div>
      <section
        class="flex-col md:flex-row space-y-8 md:space-y-0 md:flex bg-base-lvl-3 px-4 py-4 rounded-md shadow-md overflow-auto items-center w-full md:space-x-12"
      >
        <article v-for="(step, index) in steps" class="text-body-1">
          <h4 class="font-bold text-secondary">
            {{ index + 1 }} -
            {{ $t(step.title) }}
          </h4>
          <p class="text-body mt-4">{{ $t(step.description) }}</p>
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
