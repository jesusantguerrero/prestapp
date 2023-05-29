<script lang="ts" setup>
import { ElNotification } from "element-plus";
import { ref, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import AppButton from "./shared/AppButton.vue";

const props = defineProps({
  plan: {
    type: Object,
    required: true,
  },
  isCurrent: {
    type: Boolean,
    default: false,
  },
  subscribeLink: {
    type: String,
    required: true,
  },
  contactLink: {
    type: String,
  },
  subscribeLabel: {
    type: String,
    default: "subscribe",
  },
});
const emit = defineEmits(["selected"]);

const buttonsContainer = ref();
onMounted(() => {
  paypal
    ?.Buttons({
      createSubscription(data: any, actions: any) {
        return actions.subscription.create({
          plan_id: props.plan.paypal_plan_id,
        });
      },

      onApprove(data: any, actions: any) {
        data.plan_id = props.plan.paypal_plan_id;
        createSubscription(data);
      },
    })
    .render(buttonsContainer.value);
});

const { t } = useI18n();
function createSubscription(data: any) {
  console.log(data);
  axios({
    method: "POST",
    url: `/v2/subscriptions/${data.subscriptionID}/save`,
    data,
  }).then(() => {
    ElNotification({
      type: "success",
      message: t(`Team has been subscribed to plan ${props.plan.display_name}`),
    });
  });
}
</script>

<template>
  <div
    class="bg-white w-4/12 px-5 py-10 mb-5 mx-6 shadow-md rounded-md"
    :class="{ 'border-purple-400 border-2': isCurrent }"
  >
    <div class="prose prose-xl">
      <h3 class="text-center">
        {{ plan.display_name ?? plan.name }}
        <div v-if="isCurrent" class="rounded-md text-purple-600 px-1 py-1 text-xs">
          Current Plan
        </div>
      </h3>
    </div>

    <div class="px-5 py-2 my-2 rounded-md">
      <h2 class="text-5xl text-center">
        <span class="font-extrabold">
          {{ plan.quantity }}
        </span>
        <small class="text-md"> USD </small>
      </h2>

      <div class="mt-5">
        <div class="prose prose-md flex flex-col">
          <span
            v-for="feature in plan.features"
            class="capitalize text-secondary font-bold"
          >
            {{ feature }}
          </span>
        </div>
      </div>
    </div>

    <div class="text-center" v-if="!isCurrent">
      <a
        v-if="contactLink"
        class="border-2 border-purple-500 bg-white text-blue-500 px-5 py-2 inline-block rounded-md"
        :href="contactLink"
      >
        Contact Sales
      </a>

      <div ref="buttonsContainer" v-if="plan.paypal_plan_id"></div>
      <AppButton
        class="bg-primary justify-center text-white w-full"
        @click="$emit('selected', plan)"
      >
        {{ subscribeLabel }}
      </AppButton>
    </div>
  </div>
</template>
