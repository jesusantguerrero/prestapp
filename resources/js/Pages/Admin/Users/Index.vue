<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { computed, ref, toRefs } from "vue";
import { router } from "@inertiajs/core";

// @ts-ignore: its my template
import AtTable from "@/Components/shared/BaseTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import cols from "./cols";
import { IRent } from "@/Modules/property/propertyEntity";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";
import AdminTemplate from "../Partials/AdminTemplate.vue";

interface IPaginatedData {
  data: IRent[];
}

const props = defineProps<{
  users: IRent[] | IPaginatedData;
  serverSearchOptions: IServerSearchData;
  user: Record<string, string>;
}>();

const { serverSearchOptions } = toRefs(props);
const {
  executeSearch,
  updateSearch,
  changeSize,
  paginate,
  reset,
  state: searchState,
} = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/admin/users?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const listData = computed(() => {
  return Array.isArray(props.users) ? props.users : props.users.data;
});

const tableConfig = {
  selectable: true,
  searchBar: true,
  pagination: true,
};

const approveTeam = (team: Record<string, string>) => {
  router.post(route("admin.teams.approve", team));
};

const deleteTeam = () => {};
</script>

<template>
  <AdminTemplate title="Teams">
    <main class="pb-16">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <AppButton @click="router.visit(route('rents.create'))">
          {{ $t("add rent") }}</AppButton
        >
      </section>
      <AtTable
        class="bg-white rounded-md text-body-1 mt-4"
        :table-data="listData"
        :cols="cols"
        :pagination="searchState"
        :total="users.total"
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
        :config="tableConfig"
      >
        <template v-slot:actions="{ scope: { row } }" class="flex">
          <div class="flex justify-end items-center">
            <UnitTag :status="row.status" />

            <Link
              class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
              :href="`/admin/teams/${row.id}`"
            >
              <IMdiChevronRight />
            </Link>
            <AppButton
              variant="neutral"
              class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
              @click="deleteTeam(row)"
              title="Approve team"
            >
              <IMdiTrash />
            </AppButton>
          </div>
        </template>
      </AtTable>
    </main>
  </AdminTemplate>
</template>
