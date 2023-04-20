<script lang="ts" setup>
import ClientCard from "@/Pages/Clients/Partials/ClientCard.vue";
import UnitTitle from "./realState/UnitTitle.vue";
import { router } from "@inertiajs/vue3";

defineProps<{
  item: Object;
}>();
</script>

<template>
  <section class="hover:bg-gray-100 cursor-pointer py-2">
    <article v-if="item.type == 'clients'">
      <ClientCard :client="item.searchable" />
    </article>
    <article
      v-else-if="item.type == 'rents'"
      class="px-4"
      @click="router.visit(`/rents/${item.searchable.id}`)"
    >
      <UnitTitle
        :title="item.searchable.address"
        :owner-name="item.searchable.owner_name"
        detail-display="col"
        :owner-link="''"
        :tenant-name="item.searchable.client_name"
      />
    </article>
    <article v-else>
      {{ item.type }}
    </article>
  </section>
</template>
