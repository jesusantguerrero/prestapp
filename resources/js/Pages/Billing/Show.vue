<script lang="ts" setup>
import AppLayout from "@/Components/templates/AppLayout.vue";
import TeamBillingSection from "@/Components/templates/TeamBillingSection.vue";

const props = defineProps<{
  sessions?: [];
  user: Record<string, any>;
  plans: any[];
  subscriptions: any;
}>();
</script>

<template>
  <AppLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Billing</h2>
    </template>

    <TeamBillingSection
      :sessions="sessions"
      :team-id="user.current_team_id"
      :subscriptions="subscriptions"
      :plans="plans"
      :disable-actions="true"
    />
  </AppLayout>
</template>
