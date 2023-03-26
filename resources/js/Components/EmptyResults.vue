<script lang="ts" setup>
defineProps<{
  message: string;
}>();
</script>

<template>
  <div class="flex text-body-1 flex-col justify-center items-center w-full">
    <IMdiNoteOff class="text-8xl" />
    <p class="mt-8 w-full mx-auto text-center">
      {{ message }}
    </p>
  </div>
</template>
