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
  realState: {
    label: "Inmobiliaria",
    link: "/dashboard/properties",
  },
  loans: {
    label: "Prestamos",
    link: "/dashboard/loans",
  },
};
const handleChange = (sectionName: string) => {
  router.get(sections[sectionName].link);
};
</script>

<template>
  <AppLayout title="Dashboard">
    <main class="p-5 pt-0 mx-auto text-gray-500 sm:px-6 lg:px-8">
      <div class="flex justify-between mb-4">
        <h4>Bienvenido, {{ user.name }}</h4>
        <ButtonGroup @change="handleChange" :values="sections" v-model="section" />
      </div>
      <slot />
    </main>
  </AppLayout>
</template>
