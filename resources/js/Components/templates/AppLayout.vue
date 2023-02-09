<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
// @ts-ignore
import { AtSide } from "atmosphere-ui";
import { ElNotification } from "element-plus";

import AppShell from "./AppShell.vue";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import AppButton from "../shared/AppButton.vue";
import TheGlobals from "../TheGlobals.vue";
import AppNotificationBell from "./AppNotificationBell.vue";
import WatchlistButton from "./WatchlistButton.vue";
// @ts-ignore
import AppResourceSearch from "./AppResourceSearch.vue";

import { useAppMenu } from "@/Modules/_app";
import { useSelect } from "@/Modules/shared/useSelects";
import { useLocalStorage } from "@vueuse/core";
import MobileMenuBar from "../mobile/MobileMenuBar.vue";

defineProps({
  title: String,
  showBackButton: Boolean,
  isOnboarding: Boolean,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team: Record<string, any>) => {
  router.put(
    route("current-team.update"),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};

const currentPath = computed(() => {
  return document?.location?.pathname;
});

const isExpanded = useLocalStorage("isMenuExpanded", true);

const logout = () => {
  router.post(route("logout"));
};

const { appMenu: currentMenu, headerMenu, mobileMenu } = useAppMenu();

//  categories
const pageProps = usePage().props;
const { categoryOptions: transformCategoryOptions } = useSelect();
transformCategoryOptions(pageProps?.categories, "sub_categories", "categoryOptions");
transformCategoryOptions(
  pageProps?.accounts,
  "accounts",
  "accountsOptions",
  (account) => {
    return {
      ...account,
      name: account.id,
    };
  }
);

watch(
  () => pageProps.errors,
  (errors) => {
    Object.keys(errors).forEach((error) => {
      ElNotification({
        message: errors[error],
        title: "Ha ocurrido un error",
        type: "error",
      });
    });
  },
  {
    deep: true,
    immediate: true,
  }
);

const handleActions = (action) => {
  // const actions = {
  //   openAddModal: {
  //     handler: openTransactionModal,
  //   },
  // };
  // actions[action]?.handler();
};
</script>

<template>
  <div>
    <Head :title="title" />
    <Banner />
    <AppShell :is-expanded="isExpanded" :nav-class="[!$slots.header && `border-b`]">
      <template #navigation>
        <!-- Primary Navigation Menu -->
        <div
          class="flex items-center justify-between h-16 pr-4 mx-auto sm:pr-6 lg:pr-8 text-body-1/80"
        >
          <div class="flex items-center">
            <AppButton @click="$emit('back')" v-if="showBackButton">
              <IconBack />
            </AppButton>
            <h4
              :class="[showBackButton ? 'lg:ml-2' : 'lg:ml-6']"
              class="pl-4 md:pl-0 text-lg font-bold text-secondary"
            >
              {{ title }}
            </h4>
          </div>

          <div class="flex justify-end h-16">
            <div class="flex items-center sm:ml-6">
              <AppResourceSearch class="hidden md:block mr-2" />
              <AppButton class="hidden md:flex px-1 items-center mr-4 ml-2">
                <IMdiPlus class="mr-2" />
                Nuevo
              </AppButton>

              <WatchlistButton class="hidden mr-2 md:inline-block" v-if="!isOnboarding" />
              <WatchlistButton class="hidden mr-2 md:inline-block" v-if="!isOnboarding" />
              <AppNotificationBell
                :notifications="pageProps.unreadNotifications"
                @click="$router.visit('/notifications')"
              />
              <!-- Settings Dropdown -->
              <div class="relative ml-3">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="$page.props.jetstream.managesProfilePhotos"
                      class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300"
                    >
                      <img
                        class="object-cover w-8 h-8 rounded-full"
                        :src="$page.props.user.profile_photo_url"
                        :alt="$page.props.user.name"
                      />
                    </button>

                    <span v-else class="inline-flex rounded-md">
                      <button
                        type="button"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none"
                      >
                        {{ $page.props.user.name }}

                        <svg
                          class="ml-2 -mr-0.5 h-4 w-4"
                          xmlns="http://www.w3.org/2000/svg"
                          viewBox="0 0 20 20"
                          fill="currentColor"
                        >
                          <path
                            fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd"
                          />
                        </svg>
                      </button>
                    </span>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Account
                    </div>

                    <DropdownLink :href="route('profile.show')"> Profile </DropdownLink>

                    <DropdownLink
                      v-if="$page.props.jetstream.hasApiFeatures"
                      :href="route('api-tokens.index')"
                    >
                      API Tokens
                    </DropdownLink>

                    <div class="border-t border-gray-100" />

                    <!-- Authentication -->
                    <form @submit.prevent="logout">
                      <DropdownLink as="button"> Log Out </DropdownLink>
                    </form>
                  </template>
                </Dropdown>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template #aside>
        <AtSide
          class="border-none shadow-none text-bold bg-primary"
          title="ICLoan"
          :is-expanded="isExpanded"
          @update:isExpanded="isExpanded = $event"
          :menu="currentMenu"
          :header-menu="headerMenu"
          :current-path="currentPath"
          brand-container-class="py-2"
          nav-container-class="px-2 pt-1 space-y-2 border-t border-base-lvl-3/20"
          icon-class="text-gray-100 transition hover:text-primary hover:bg-base-lvl-3/10"
          item-class="px-5 py-[0.80rem] rounded-md font-bold text-gray-100 w-54 hover:text-primary-shade-1 hover:bg-base-lvl-3/10"
          item-active-class="text-primary bg-base-lvl-3/10"
          is-expandable
        >
          <template #brand>
            <!-- Logo -->
            <h1 class="flex items-center w-full shrink-0 px-7 text-gray-100">
              <Link :href="route('dashboard')" class="flex items-center space-x-2">
                <ApplicationMark class="block w-auto h-9" />
                <span class="text-xl font-bold"> ICLoan </span>
              </Link>
            </h1>
          </template>
        </AtSide>
      </template>
      <template #main-section>
        <!-- Page Heading -->
        <header
          v-if="$slots.header"
          :class="[isExpanded ? 'lg:pr-56' : 'lg:pr-20']"
          class="fixed z-30 w-full border-b bg-neutral base-deep-1"
        >
          <slot name="header" />
        </header>

        <!-- Page Content -->
        <main class="pt-0 md:pt-8 mx-auto md:px-24"><slot /></main>
        <MobileMenuBar :menu="mobileMenu" @action="handleActions" />
      </template>
    </AppShell>
    <TheGlobals />
  </div>
</template>

<style>
@media screen {
  #print {
    display: none;
  }
}

@media print {
  body * {
    visibility: hidden;
  }
  #print,
  #print * {
    visibility: visible;
  }
  #print {
    position: absolute;
    left: 0;
    top: 0;
  }

  .printable {
    width: 210mm;
  }
}
</style>
