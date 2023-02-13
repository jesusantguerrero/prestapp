<script setup lang="ts">
import { ref } from "vue";

import AppLayout from "@/Components/templates/AppLayout.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";

defineProps({
  user: {
    type: Object,
    required: true,
  },
});

const pageProps = usePage().props;
const section = ref(pageProps.section);
const sections = {
  general: {
    label: "General",
    link: "/dashboard",
  },
  loans: {
    label: "Prestamos",
    link: "/dashboard/loan",
  },
  realState: {
    label: "Inmobiliaria",
    link: "/dashboard/property",
  },
};
const handleChange = (sectionName: string) => {
  router.get(sections[sectionName].link);
};
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="p-5 pt-0 mx-auto text-gray-500 sm:px-6 lg:px-8">
      <div class="flex justify-between mt-4 md:mt-0 mb-4">
        <h4 class="hidden md:inline-block">Bienvenido, {{ user.name }}</h4>
        <ButtonGroup
          class="w-full md:w-fit"
          @update:modelValue="handleChange"
          :values="sections"
          v-model="section"
        />
      </div>
      <slot />
    </main>
  </AppLayout>
</template>
