<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";

defineProps<{
  imageUrl?: string;
  title: string;
  ownerName: string;
  ownerLink?: string;
  tenantName: string;
  price?: string;
}>();
</script>

<template>
  <section class="flex text-secondary">
    <div class="rounded-md bg-base-lvl-2 w-10 border flex justify-center items-center">
      <IMdiHomeFloor1 class="text-2xl" />
    </div>
    <article class="ml-2">
      <h4 class="font-bold">
        {{ title }}
        <span v-if="price" class="text-success font-bold">{{ price }}</span>
      </h4>
      <section class="flex items-center">
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
