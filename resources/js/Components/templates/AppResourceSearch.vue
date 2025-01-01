<script setup lang="ts">
// @ts-ignore
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { reactive, computed, watch } from "vue";
import axios from "axios";
import SearchListItem from "../SearchListItem.vue";
import { flatten } from "lodash";

const state = reactive({
  searchText: "",
  results: [],
  selectedTab: null,
});
const hasResults = computed(() => {
  return !!(state.searchText.length >= 3 && Object.keys(state.results).length);
});

const tabsWithResults = computed(() => {
  const tabs = {
    all: flatten(Object.values(state.results)),
    ...state.results,
  };

  return tabs;
});

watch(
  () => state.searchText,
  (search: string) => {
    axios.get(`/search?search=${search}`).then(({ data }) => {
      state.results = data;
      state.selectedTab = "all";
    });

    if (!search) {
      state.results = [];
    }
  }
);
</script>

<template>
  <ElPopover
    :visible="hasResults"
    :show-arrow="false"
    placement="bottom-end"
    :width="500"
    @close="state.searchText = ''"
  >
    <template #reference>
      <div
        v-bind="$attrs"
        class="hover:w-96 transition duration-[3s]"
        :class="{ 'w-96': hasResults }"
      >
        <AppSearch v-model="state.searchText" />
      </div>
    </template>

    <section>
      <section class="flex w-full">
        <div
          v-for="(tab, tabName) in tabsWithResults"
          @click="state.selectedTab = tabName"
          class="px-5 cursor-pointer py-1 font-bold w-full capitalize text-center rounded-md bg-base-lvl-2"
          :class="state.selectedTab == tabName ? 'bg-primary text-white' : ''"
        >
          {{ tabName }}
        </div>
      </section>
      <div class="w-full py-2 mt-4 h-64 overflow-auto">
        <div v-for="item in tabsWithResults[state.selectedTab]">
          <SearchListItem :item="item" />
        </div>
      </div>
    </section>
  </ElPopover>
</template>

<style lang="scss" scoped>
.input-group-prepend label {
  background: white;
  border-right: 0;
  cursor: pointer;
}
.input-group-prepend {
  label {
    background: white;
    border-right: 0;
    border-radius: 0 0 0 0 !important;
    cursor: pointer;
  }
}
.input-group-append {
  label {
    background: white;
    border-left: 0;
    border-right: 0;
    border-radius: 0 0 0 0 !important;
    cursor: pointer;
  }
}

input[type="search"] {
  border-left: 0;
  border-radius: 0 0 0 0 !important;
}

.search-container {
  transition: all ease 0.3s;
}
.search-container.focus {
  border: 0 solid !important;
  outline-color: var(--primary-color);
  outline: 1px solid var(--primary-color);
  box-shadow: 0 0 0 0.2rem transparentize(#087a9c, 0.9);

  input {
    outline: none !important;
    box-shadow: none !important;
    border: 0 solid !important;
  }

  label {
    color: var(--primary-color);
    border: 0 solid !important;
  }
}
</style>

<style lang="scss">
.el-date-editor.el-input {
  width: auto !important;
}
.app-search {
  .el-date-editor.el-input,
  .el-date-editor.el-input__inner {
    width: 100%;
    height: 100% !important;
    input {
      border: 0 !important;
    }
  }
}

.col-search {
  padding-right: 0 !important;
}

.dates-container {
  padding-left: 0 !important;
  height: 38px;
  border: 1px solid #ddd;
  border-left: none;
  overflow: hidden;

  &__group {
    height: 100%;
  }
  .el-input--prefix .el-input__inner {
    border: none !important;
    height: 100%;
    background: transparent;

    &:focus {
      color: var(--primary-color);
    }
  }

  .el-input__icon {
    line-height: 35px;
    background: transparent;
  }
}
</style>
