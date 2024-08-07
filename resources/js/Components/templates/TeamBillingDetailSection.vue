<script lang="ts" setup>
import { format } from "date-fns";
import { computed } from "vue";
import { router } from "@inertiajs/vue3";

import DataCard from "@/Components/DataCard.vue";
import DataPlanCard from "@/Components/DataPlanCard.vue";
import DataBillingCard from "@/Components/DataBillingCard.vue";
import InvoiceCard from "./InvoiceCard.vue";

const props = defineProps<{
  sessions?: [];
  teamId: number;
  plans: any[];
  subscriptions: any;
  disableActions: boolean;
}>();

const visibleSubscriptions = computed(() => {
  return props.subscriptions?.filter?.(
    (subs: any) => subs.status.toLowerCase() != "cancelled"
  );
});

const details = computed(() => {
  return visibleSubscriptions.value?.at?.(0);
});

const pendingBalance = computed(() => {
  if (details.value) {
    const nextPayment = details.value.next_payment;
    return nextPayment;
  }
  return 0;
});

const lastPayment = computed(() => {
  if (details.value) {
    return details.value.last_payment;
  }
  return "-";
});

const nextPaymentDate = computed(() => {
  if (details.value) {
    return format(new Date(details.value.next_billing_date), "MMM dd, yyyy");
  }
  return 0;
});

const lastPaymentDate = computed(() => {
  if (details.value) {
    return format(new Date(details.value.last_payment_date), "MMM dd, yyyy");
  }
  return "-";
});

const cards = computed(() => {
  return [
    {
      title: "Current Monthly Bill",
      value: pendingBalance.value,
      links: [
        {
          label: "Payment Details",
          type: "inertia",
          ref: "/user/billing/current",
        },
      ],
    },
    {
      title: "Next Payment Due",
      value: nextPaymentDate.value,
      links: [
        {
          label: "View payment history",
          type: "inertia",
          ref: "/user/billing/current",
        },
      ],
    },
    {
      title: "Last Payment",
      value: lastPayment.value,
      links: [],
    },
    {
      title: "Payment Information",
      value: lastPaymentDate.value,
      links: [
        {
          label: "Redeem coupon",
          type: "inertia",
          ref: "/user/billing/current",
        },
      ],
    },
  ];
});

function sendSubscriptionAction(subscription: any, actionName: string) {
  const url = `/v2/subscriptions/${subscription.id}/agreement/${subscription.agreement_id}/${actionName}`;
  axios.post(url).then(() => {
    router.reload();
  });
}

function isCurrentPlan(plan: any) {
  return visibleSubscriptions.value?.at?.(0)?.id == plan.id;
}

const isBigger = (plan: any) => {
  return (visibleSubscriptions.value?.at?.(0)?.quantity ?? 0) < plan.quantity;
};
function getLabelSubscribe(plan: any) {
  return isBigger(plan) ? "Upgrade" : "Downgrade";
}

const subscribe = (url: string) => {
  router.post(url);
};
</script>

<template>
  <main>
    <div class="py-10">
      <!-- Plan Statistics -->
      <div class="plans__info flex mb-10">
        <DataCard v-for="info in cards" :key="info.title" :info="info" />
      </div>
      <!-- /plan Statistics -->

      <!-- Current Plan -->
      <div class="subscriptions__container mb-10">
        <h4 class="font-bold mx-2 text-lg mb-2 text-gray-400">Current Plan</h4>
        <DataPlanCard
          v-for="plan in visibleSubscriptions"
          :key="plan.id"
          :plan="plan"
          @suspend="sendSubscriptionAction(plan, 'suspend')"
          @reactivate="sendSubscriptionAction(plan, 'reactivate')"
          @cancel="sendSubscriptionAction(plan, 'cancel')"
          :disabled="disableActions"
        />
      </div>
      <!-- /Current Plan -->

      <section class="px-5 space-y-4">
        <InvoiceCard
          v-for="invoice in subscriptions.at(0).invoices"
          :invoice="invoice"
        />
      </section>

      <!-- Plans -->
      <div class="plans__container mt-5">
        <h4 class="font-bold mx-2 text-lg b-2 text-gray-400">Plans</h4>
        <div class="flex space-x-5 mt-5">
          <DataBillingCard
            v-for="plan in plans"
            :key="plan.id"
            :plan="plan"
            :is-current="isCurrentPlan(plan)"
            :subscribe-label="getLabelSubscribe(plan)"
            @selected="subscribe(`/admin/teams/${teamId}/subscribe/${plan.id}`)"
            :disabled="disableActions"
          />
        </div>
      </div>
    </div>
  </main>
</template>
