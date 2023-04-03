<script setup lang="ts">
import { ref, inject } from "vue";

import AppLayout from "@/Components/templates/AppLayout.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
import TeamApproval from "./TeamApproval.vue";

defineProps({
  user: {
    type: Object,
    required: true,
  },
  isTeamApproved: {
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
  <AppLayout :title="$t('Dashboard')">
    <main class="py-5 pt-0 mx-auto text-gray-500">
      <div class="flex justify-between mt-4 md:mt-0 mb-4">
        <h4 class="hidden md:inline-block">Bienvenido, {{ user.name }}</h4>
        <ButtonGroup
          v-if="isTeamApproved"
          class="w-full md:w-fit"
          @update:modelValue="handleChange"
          :values="sections"
          v-model="section"
        />
      </div>
      <slot v-if="isTeamApproved" />
      <TeamApproval v-else />
    </main>
  </AppLayout>
</template>
