<script setup lang="ts">
// @ts-ignore: srouter should be exported by inertia
import { router } from '@inertiajs/vue3';
import { AtBackgroundIconCard, AtButton } from 'atmosphere-ui';
interface ICard {
    label: string;
    value: [string, number];
    accent: boolean;
    icons: string;

}
interface Props {
    message: string;
    username: string;
    cards: ICard[];
}

defineProps<Props>()
</script>

<template>
    <article class="px-5 pt-3 transition border divide-y rounded-lg divide-base border-base bg-base-lvl-3">
        <section class="items-center pb-2 md:justify-between md:flex">
            <h1 class="font-bold text-body-1">
                {{ message }} <span class="text-primary">{{ username }}</span>
            </h1>
            <div class="space-x-2">
                <AtButton class="text-sm text-primary" rounded @click="router.visit(route('budgets.index'))">
                    <i class="fa fa-home"></i>
                    Datos de alquiler
                </AtButton>
            </div>
        </section>
        <section class="flex py-4 space-x-4 divide-x-2 rounded-md divide-base-lvl-2 bg-base-lvl-3">
          <AtBackgroundIconCard
            v-for="card in cards"
            class="w-full h-24 shadow-none"
            :class="[card.accent ? 'bg-primary text-white' : 'bg-white text-primary']"
            :icon="`fas ${card.icon}`"
            :value="card.value"
            :title="card.label"
          />
        </section>
    </article>
</template>
