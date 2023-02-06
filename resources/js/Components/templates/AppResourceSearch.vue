<template>
  <ElPopover
    :visible="state.hasResults"
    :show-arrow="false"
    placement="bottom-end"
    :width="500"
  >
    <template #reference>
      <div v-bind="$attrs">
        <AppSearch v-model="state.searchText" />
      </div>
    </template>

    <section>
      <section class="flex w-full">
        <div
          v-for="tab in state.tabsWithResults"
          class="px-5 cursor-pointer py-1 font-bold w-full capitalize text-center rounded-md bg-base-lvl-2"
          :class="state.selectedTab == tab ? 'bg-primary text-white' : ''"
        >
          {{ tab }}
        </div>
      </section>
      <div class="w-full py-2 mt-4">
        <div v-for="item in state.results[state.selectedTab]">
          {{ item.fullName }}
        </div>
      </div>
    </section>
  </ElPopover>
</template>

<script setup lang="ts">
// @ts-ignore
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { reactive, computed, watch } from "vue";
import axios from "axios";

const state = reactive({
  searchText: "",
  results: {},
  hasResults: computed(() => {
    return !!Object.keys(state.tabsWithResults).length;
  }),
  selectedTab: null,
  tabsWithResults: computed(() => {
    return Object.keys(state.results).filter((name) => state.results[name]);
  }),
});

watch(
  () => state.searchText,
  (search: string) => {
    axios.get(`/search?search=${search}`).then(({ data }) => {
      state.results = data;
      state.selectedTab = state.tabsWithResults[0];
    });

    if (!search) {
      state.results = {};
    }
  }
);
</script>

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
  input {
    outline: none !important;
    box-shadow: none !important;
    border: 0 solid !important;
  }

  label {
    color: var(--primary-color);
    border: 0 solid !important;
  }

  border: 0 solid !important;
  outline-color: var(--primary-color);
  outline: 1px solid var(--primary-color);
  box-shadow: 0 0 0 0.2rem transpaterize(#087a9c, 0.9);
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
