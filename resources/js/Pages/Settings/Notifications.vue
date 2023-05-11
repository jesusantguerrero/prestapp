<template>
  <AppLayout :title="$t('Notifications')">
    <div class="px-2 py-10 mx-auto sm:px-6 lg:px-8">
      <div class="w-full rounded-md bg-base-lvl-3">
        <BaseTable
          ref="AtTable"
          :config="state.tableConfig"
          :cols="cols"
          :table-data="notifications"
        >
          <template #data="{ scope }">
            <div class="flex w-full py-3 pl-4 space-between">
              {{ scope.row.data.message }}
            </div>
          </template>
          <template #actions="{ scope }">
            <div class="flex items-center ml-auto space-x-2">
              <Link
                :href="scope.row.data.link"
                class="ml-auto transition-colors rounded-md text-primary"
                @click="markAsRead(scope.row)"
              >
                {{ scope.row.data.cta }}
              </Link>

              <AtButton
                :href="scope.row.data.link"
                class="ml-auto text-white transition-colors rounded-md bg-primary"
                @click="markAsRead(scope.row)"
                v-if="!scope.row.read_at"
              >
                {{ $t("Mark as read") }}
              </AtButton>
            </div>
          </template>
        </BaseTable>
      </div>
    </div>
  </AppLayout>
</template>

<script lang="ts" setup>
import { Link } from "@inertiajs/vue3";
import { reactive } from "vue";
// @ts-ignore
import { AtButton } from "atmosphere-ui";

import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/vue3";
import BaseTable from "@/Components/shared/BaseTable.vue";

defineProps({
  notifications: {
    type: Array,
    default() {
      return [];
    },
  },
});

const cols = [
  {
    label: "Notification Message",
    name: "data",
    class: "text-left",
    sortable: true,
    headerClass: "text-left px-2",
    render(row: any) {
      return row.data.message || "N/D";
    },
    width: 300,
  },
  {
    label: "Actions",
    name: "actions",
    type: "custom",
    class: "text-right",
    headerClass: "text-right px-2",
    minWidth: 100,
  },
];

const state = reactive({
  tableSearchOptions: {
    resourceUrl: "/projects?sort=surename",
  },
  tableConfig: {
    resourceUrl: "/projects",
    selectable: true,
    pagination: true,
    searchBar: ["search", "filter", "dates", "add", "actions"],
    dataTemplate: {
      name: "week-pager",
      filter: "date",
    },
  },
});

const markAsRead = (notification: any) => {
  router.put(`/notifications/${notification.id}`, {
    read_at: new Date(),
  });
};
</script>
