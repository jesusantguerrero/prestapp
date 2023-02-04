<script setup lang="ts">
import { router } from "@inertiajs/vue3";
// @ts-ignore: my lib
import { AtBackgroundIconCard, AtButton } from "atmosphere-ui";
interface ICard {
  label: string;
  value: [string, number];
  accent: boolean;
  icon: string;
}
interface Props {
  message: string;
  username?: string;
  cards?: ICard[];
  actionLabel?: string;
  actionLink?: string;
  sectionClass?: string;
}

withDefaults(defineProps<Props>(), {
  sectionClass:
    "flex flex-col md:flex-row py-4 space-y-2 md:space-y-0 md:space-x-4 divide-x-2 rounded-md divide-base-lvl-2 bg-base-lvl-3",
});
</script>

<template>
  <article
    class="px-5 pt-3 transition border divide-y rounded-lg divide-base border-base bg-base-lvl-3"
  >
    <section class="items-center pb-2 justify-between flex">
      <h1 class="font-bold text-body-1">
        {{ message }} <span class="text-primary">{{ username }}</span>
      </h1>
      <div class="space-x-2" v-if="actionLabel && actionLink">
        <AtButton
          class="text-sm text-primary px-0"
          rounded
          @click="actionLink && router.visit(actionLink)"
        >
          <i class="fa fa-home" />
          {{ actionLabel }}
        </AtButton>
      </div>
    </section>
    <slot name="content">
      <section :class="sectionClass">
        <AtBackgroundIconCard
          v-for="card in cards"
          class="w-full h-24 shadow-none"
          :class="[card.accent ? 'bg-primary text-white' : 'bg-white text-primary']"
          :icon="`fas ${card.icon}`"
          :value="card.value"
          :title="card.label"
        />
      </section>
    </slot>
  </article>
</template>
