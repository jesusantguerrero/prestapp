<script setup lang="ts">
import { router } from "@inertiajs/core";

import AdminTemplate from "../Partials/AdminTemplate.vue";
import TeamBillingDetailSection from "@/Components/templates/TeamBillingDetailSection.vue";

defineProps<{
  teams: Record<string, string | Object>;
  user: Record<string, string>;
  sessions: any;
  plans: any;
  subscriptions: Record<string, string | Object>;
}>();

const impersonateUser = (user: Record<string, string>) => {
  router.post(`/admin/impersonate-user/${user.id}`);
};
</script>

<template>
  <AdminTemplate title="Teams">
    <main class="pb-16">
      <TeamBillingDetailSection
        :sessions="sessions"
        :plans="plans"
        :subscriptions="[subscriptions]"
        :team-id="subscriptions.id"
      />
    </main>
  </AdminTemplate>
</template>
