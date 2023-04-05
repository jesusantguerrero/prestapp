<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";

defineProps<{
  imageUrl?: string;
  title: string;
  ownerName: string;
  ownerLink?: string;
  tenantName: string | number;
  price?: string;
  detailDisplay?: "col" | "row";
}>();
</script>

<template>
  <section class="flex text-secondary" @click="$emit('click')">
    <div class="flex items-center justify-center w-10 border rounded-md bg-base-lvl-2">
      <IMdiHomeFloor1 class="text-2xl" />
    </div>
    <article class="ml-2">
      <h4 class="text-sm font-bold">
        {{ title }}
        <span v-if="price" class="font-bold text-success">{{ price }}</span>
      </h4>
      <section
        class="flex mt-2 text-xs"
        :class="[detailDisplay == 'col' ? 'flex-col items-start' : 'items-center']"
      >
        <span v-if="!ownerLink"> {{ ownerName }}</span>
        <Link v-else :href="ownerLink"> {{ ownerName }}</Link>
        <template v-if="tenantName">
          <IMdiChevronRight class="text-primary" />
          <span class="text-primary"> {{ tenantName }}</span>
        </template>
      </section>
    </article>
  </section>
</template>
