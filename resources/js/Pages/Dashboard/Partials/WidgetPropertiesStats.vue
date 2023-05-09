<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

interface UnitStat {
  label: string;
  value: string | number;
}

defineProps<{
  total: number;
  description: string;
  unitStats: UnitStat[];
}>();
</script>

<template>
  <article class="bg-base-lvl-3 border py-2 px-4">
    <header class="flex justify-between">
      <section class="flex items-center">
        <IMaterialSymbolsHomeWorkOutline class="text-5xl mr-3 text-secondary" />
        <section>
          <h4 class="text-primary font-extrabold text-2xl">{{ total }}</h4>
          <p class="text-body-1">{{ description }}</p>
        </section>
      </section>
      <section>
        <Link href="/properties" class="flex items-center text-primary">
          {{ $t("See all properties") }}
          <IMdiChevronRight />
        </Link>
      </section>
    </header>
    <main class="flex mt-4 bg-secondary/5 rounded-md py-2 mb-4 divide-x">
      <article v-for="stat in unitStats" class="w-full text-center">
        <h4 class="font-bold">{{ stat.value }}</h4>
        <p>{{ stat.label }}</p>
      </article>
    </main>
  </article>
</template>
