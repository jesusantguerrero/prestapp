<script setup lang="ts">
import { ICard } from "@/types";
import { router } from "@inertiajs/vue3";
// @ts-ignore: my lib
import { AtBackgroundIconCard, AtButton } from "atmosphere-ui";
import { computed } from "vue";

interface Props {
  message: string;
  username?: string;
  cards?: ICard[];
  actionLabel?: string;
  actionLink?: string;
  sectionClass?: string;
  borderless?: boolean;
  rounded: boolean;
  size: string;
}

const props = withDefaults(defineProps<Props>(), {
  rounded: true,
  size: "large",
  sectionClass:
    "flex flex-col md:flex-row py-4 space-y-2 md:space-y-0 md:space-x-4 divide-x-2 rounded-md divide-base-lvl-2 bg-base-lvl-3",
});

const cardSize = computed(() => {
  const sizes: Record<string, string> = {
    large: "h-24",
    normal: "h-16",
    small: "h-14",
  };
  return sizes[props.size] ?? sizes["large"];
});
</script>

<template>
  <article
    class="px-5 pt-3 transition divide-y divide-base bg-base-lvl-3"
    :class="[!borderless && 'border-base border', rounded && 'rounded-lg ']"
  >
    <section class="items-center justify-between flex" :class="!$slots.title && 'pb-2'">
      <slot name="title">
        <h1 class="font-bold text-body-1">
          {{ message }} <span class="text-primary">{{ username }}</span>
        </h1>
      </slot>

      <div>
        <slot name="actions">
          <div class="space-x-2" v-if="actionLabel && actionLink">
            <AtButton
              class="flex text-sm text-primary px-0 items-center"
              rounded
              @click="actionLink && router.visit(actionLink)"
            >
              {{ actionLabel }}
              <IMdiChevronRight />
            </AtButton>
          </div>
        </slot>
      </div>
    </section>
    <slot name="content">
      <section :class="sectionClass">
        <AtBackgroundIconCard
          v-for="card in cards"
          class="w-full shadow-none"
          :class="[
            card.accent ? 'bg-primary-shade-2 text-white' : 'bg-white text-primary',
            cardSize,
          ]"
          :icon="`fas ${card.icon}`"
          :value="card.value"
          :title="card.label"
        />
      </section>
    </slot>
    <slot name="append" />
  </article>
</template>
