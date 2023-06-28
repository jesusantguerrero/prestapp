<script lang="ts" setup>
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

import AppButtonCircle from "@/Components/shared/AppButtonCircle.vue";
import NotificationItem from "@/Components/NotificationItem.vue";
import { useResponsive } from "@/utils/useResponsive";

const { isMobile } = useResponsive();

interface INotification {
  id: number;
}

const props = withDefaults(
  defineProps<{
    count: number;
    notifications: INotification[];
    toShow: number;
  }>(),
  {
    toShow: 4,
  }
);

const visibleNotifications = computed(() => {
  return props.notifications
    .filter((notification) => notification.data?.message)
    .slice(0, props.toShow);
});

const seeMoreLength = computed(() => {
  return props.notifications.slice(props.toShow).length;
});

const handleRead = (notification: INotification) => {
  router.put(
    `/notifications/${notification.id}`,
    {
      read_at: new Date(),
    },
    {
      onSuccess() {
        notification.data.link && router.visit(notification.data?.link);
      },
    }
  );
};
</script>

<template>
  <ElPopover placement="bottom-end" :width="300" trigger="click">
    <template #reference>
      <AppButtonCircle type="button">
        <span class="text-xl md:text-md">
          <IMdiBell class="text-white md:text-body-1" v-if="!isMobile" />
          <IMdiBellOutline class="text-white" v-else />
        </span>
        <div
          v-if="notifications.length > 0"
          class="absolute bottom-5 -right-2 md:bottom-0 md:right-0 w-5 h-5 text-xs text-white flex justify-center items-center rounded-full shadow-md bg-error"
        >
          {{ count }}
        </div>
      </AppButtonCircle>
    </template>

    <section class="notification-body">
      <div class="flex items-center justify-between pb-2 font-bold text-center border-b">
        <div class="capitalize">{{ $t("notifications") }}</div>
        <div>
          <Link
            v-if="false"
            href="/settings/notifications"
            class="px-2 py-1 transition rounded-sm hover:bg-gray-100 focus:outline-none"
          >
            <i class="fas fa-sliders-h"></i> Manage preferences
          </Link>
        </div>
      </div>
      <div class="divide-y">
        <NotificationItem
          v-for="notification in visibleNotifications"
          :key="notification.id"
          :notification="notification"
          @read="handleRead"
        />
        <Link
          v-if="seeMoreLength"
          href="/notifications"
          class="w-full py-2 font-bold text-center underline"
        >
          And {{ seeMoreLength }} more
        </Link>
        <div class="flex items-center justify-center h-20" v-if="!notifications.length">
          You are up to date
        </div>
        <div class="w-full py-1">
          <Link
            href="/notifications"
            class="block w-full py-2 text-center transition rounded-sm hover:bg-gray-100 focus:outline-none"
          >
            See all notifications
          </Link>
        </div>
      </div>
    </section>
  </ElPopover>
</template>
