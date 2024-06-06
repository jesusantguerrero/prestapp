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
          <template #header-actions v-if="notifications.length">
            <div class="flex items-center ml-auto space-x-2 justify-end mr-2">
                <TabButton @click="markAllAsRead" :keep-active-mode="false" class="hover:text-primary">
                    <span class="text-lg">
                        <IMdiEmailCheck  />
                    </span>
                </TabButton>

            </div>
        </template>
          <template #data="{ scope }">
            <section class="w-full">
              <article class="py-3">
                {{ scope.row.data.message }}
              </article>
              <footer class="mt-2 text-body-1">
                {{ formatDate(scope.row.created_at) }}
              </footer>
            </section>
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

              <AppButton
                :href="scope.row.data.link"
                class="ml-auto text-white transition-colors rounded-md bg-primary"
                @click="markAsRead(scope.row)"
                v-if="!scope.row.read_at"
                :processing="scope.row.isLoading"
              >
                {{ $t("Mark as read") }}
              </AppButton>
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
import { formatDate } from "@/utils";
import AppButton from "@/Components/shared/AppButton.vue";

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
  },
  {
    label: "Actions",
    name: "actions",
    type: "custom",
    align: "right",
    class: "text-right",
    headerClass: "text-right px-2",
    width: 300,
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
  notification.isLoading = true;
  router.put(
    `/notifications/${notification.id}`,
    {
      read_at: new Date(),
    },
    {
      onSuccess() {
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
      },
    }
  );
};
</script>
