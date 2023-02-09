<script setup>
defineProps({
  ticket: {
    type: Boolean,
  },
});
</script>

<template>
  <main class="print-container text-body" :class="{ 'w-32': ticket }">
    <slot />
  </main>
</template>

<style lang="scss">
.print-container,
body {
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  background: #222;
}

@media print {
  .print-container,
  body {
    width: fit-content !important;
    display: block !important;
    background: transparent !important;

    .pos-ticket {
      box-shadow: unset !important;
    }
  }
}
</style>
