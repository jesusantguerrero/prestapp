<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";

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
  <article class="bg-base-lvl-3 rounded-lg shadow-sm ring-1 ring-neutral/10 p-4">
    <header class="flex items-start justify-between cursor-pointer hover:text-primary group" @click="router.visit('/rents')">
      <div class="flex items-start space-x-3">
        <div class="p-2 bg-secondary/10 rounded-lg">
          <IClarityContractLine class="h-6 w-6 text-secondary group-hover:scale-110 transition-all" />
        </div>
        <div>
          <div class="flex items-baseline space-x-2">
            <h4 class="text-xl font-semibold text-primary group-hover:text-primary-light transition-all">{{ total }}</h4>
            <p class="text-sm text-body-1 group-hover:text-primary-light transition-all">{{ description }}</p>
          </div>
        </div>
      </div>
          <Link 
            href="/rents" 
            class="inline-flex items-center mt-1 text-sm text-primary group-hover:text-secondary group-hover:scale-110 transition-all">
            {{ $t("See all") }}
            <IMdiChevronRight class="h-4 w-4" />
          </Link>
    </header>

    <div class="flex gap-2 mt-4">
      <div 
        v-for="stat in unitStats" 
        :key="stat.label"
        class="bg-base-lvl-2 rounded-md p-2 text-center">
        <p class="text-lg font-semibold text-body">{{ stat.value }}</p>
        <p class="text-xs text-body-1 mt-0.5">{{ stat.label }}</p>
      </div>
    </div>
  </article>
</template>
