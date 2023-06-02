<script setup lang="ts">
import { ref, provide } from "vue";
import { router } from "@inertiajs/core";
import { usePage } from "@inertiajs/vue3";
// @ts-ignore
import { AtDatePager } from "atmosphere-ui";

import AppLayout from "@/Components/templates/AppLayout.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";
import TeamApproval from "./TeamApproval.vue";
import { toRefs } from "@vueuse/shared";
import { useServerSearch } from "@/utils/useServerSearch";
import { useLocalStorage } from "@vueuse/core";
import AppButton from "@/Components/shared/AppButton.vue";
import { useResponsive } from "@/utils/useResponsive";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import ResponsiveButtonGroup from "./ResponsiveButtonGroup.vue";

defineProps<{
  user: Record<string, any>;
  isTeamApproved: boolean;
}>();

const pageProps = usePage().props;
const section = ref<string>(pageProps.section ?? "general");
interface IButtonSection {
  label: string;
  link: string;
}

const sections: Record<string, IButtonSection> = {
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

const { serverSearchOptions } = toRefs(pageProps);

const { executeSearchWithDelay, updateSearch, state: pageState } = useServerSearch(
  // @ts-expect-error: unknown is not assignable to ISearch
  serverSearchOptions,
  (finalUrl: string) => {
    console.log(finalUrl);
    updateSearch(`${location.pathname}?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const isOnboardingOpen = useLocalStorage("icloan:isOnboardingOpen", true);
provide("isOnboardingOpen", isOnboardingOpen);

const { isMobile } = useResponsive();
</script>

<template>
  <AppLayout :title="$t('Dashboard')">
    <main class="py-5 pt-0 mx-auto text-gray-500">
      <div class="flex justify-between mt-4 mb-4 md:mt-0">
        <h4 class="hidden md:inline-block">{{ $t("Welcome") }}, {{ user.name }}</h4>
        <section class="flex space-x-4 w-full">
          <ResponsiveButtonGroup
            v-if="isTeamApproved"
            @update:modelValue="handleChange"
            :sections="sections"
            v-model="section"
            :placeholder="$t('Select view')"
            class="w-full"
          />
          <AtDatePager
            v-if="!isMobile"
            class="w-full h-12 border bg-base-lvl-1 text-body border-secondary"
            v-model:startDate="pageState.dates.startDate"
            v-model:endDate="pageState.dates.endDate"
            @change="executeSearchWithDelay()"
            controlsClass="bg-transparent text-body hover:bg-base-lvl-1 hover:text-secondary"
            next-mode="month"
          />
          <AppButton
            @click="isOnboardingOpen = !isOnboardingOpen"
            title="Open onboarding help"
            v-if="!isOnboardingOpen"
          >
            <IIcOutlineMap />
          </AppButton>
        </section>
      </div>
      <slot v-if="isTeamApproved" />
      <TeamApproval v-else />
    </main>
  </AppLayout>
</template>
