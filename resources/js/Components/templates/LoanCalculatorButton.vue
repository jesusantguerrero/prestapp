<template>
  <JetDropdown align="right" width="64" v-model:open="isOpen">
    <template #trigger>
      <AppButtonCircle class="hover:text-primary">
        <IMdiStarOutline />
      </AppButtonCircle>
    </template>

    <template #content>
      <div class="w-64 py-1">
        <header class="flex justify-between">
          <h4 class="px-2 text-body-1/80">Watchlist</h4>
          <button class="px-2 text-primary">See all</button>
        </header>
        <LogerButtonTab
          class="flex items-center justify-between w-full font-bold"
          v-for="item in items"
          @click="
            openTransactionModal({
              mode: DEPOSIT,
            })
          "
        >
          <span class="flex items-center">
            <IMdiStar class="mr-2 text-md text-secondary" />
            {{ item.name }}
          </span>
          <span class="text-secondary">
            {{ formatMoney(item?.data?.month.total) }}
          </span>
        </LogerButtonTab>
      </div>
    </template>
  </JetDropdown>
</template>

<script setup>
import { ref } from "vue";
import JetDropdown from "@/Components/Dropdown.vue";
import AppButtonTab from "@/Components/shared/AppButtonTab.vue";
import AppButtonCircle from "@/Components/shared/AppButtonCircle.vue";
import axios from "axios";
import { watchOnce } from "@vueuse/core";
import { formatMoney } from "@/utils";

const getWatchlist = () => {
  return axios.get("/api/watchlist/").then(({ data }) => data.data);
};

const items = ref();
const isOpen = ref();
watchOnce(isOpen, async () => {
  items.value = await getWatchlist();
});
</script>
