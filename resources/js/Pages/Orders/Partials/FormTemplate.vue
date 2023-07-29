<template>
  <article class="relative flex pt-16 pb-20 mx-auto space-x-2 max-w-screen-2xl">
      <main class="w-full pr-5 md:w-10/12 md:pl-8">
          <slot />
      </main>

      <aside class="relative hidden h-screen px-2 overflow-auto md:block" :class="panelStyles">
          <section class="fixed aside-content">
              <slot name="panel">

              </slot>
          </section>
      </aside>
  </article>
</template>

<script setup>
  import {  computed } from 'vue';
  import { PANEL_SIZES } from '@/utils/constants';

  const props = defineProps({
      title: {
          type: String
      },
      categories: {
          type: Array,
          default() {
              return []
          }
      },
      accounts: {
          type: Array,
          default() {
              return []
          }
      },
      panelSize: {
          type: String,
          validator(value) {
              return Object.keys(PANEL_SIZES).includes(value)
          }
      }
  });

  // Styling
  const panelStyles = computed(() => {
      const sizes = PANEL_SIZES[props.panelSize] || PANEL_SIZES.small;
      return [sizes];
  })

</script>


<style lang="scss" scoped>
.aside-content {
  width: -webkit-fill-available;
  padding-right: 32px;
}

</style>
