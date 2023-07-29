<script setup lang="ts">
import { ref, computed, onUnmounted, nextTick, onMounted, provide } from "vue";
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
import AddNewButton from "./AddNewButton.vue";
// @ts-ignore
import AppResourceSearch from "./AppResourceSearch.vue";

import { useAppMenu } from "@/Modules/_app";
import { useSelect } from "@/Modules/shared/useSelects";
import { useLocalStorage } from "@vueuse/core";
import MobileMenuBar from "../mobile/MobileMenuBar.vue";
import { useI18n } from "vue-i18n";
import AppButtonCircle from "../shared/AppButtonCircle.vue";
import FastAccessOptions from "@/Pages/Dashboard/Partials/FastAccessOptions.vue";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import ResponsiveModal from "../ResponsiveModal.vue";

defineProps({
  title: String,
  showBackButton: Boolean,
  isOnboarding: Boolean,
  isTeamApproved: Boolean,
});

const currentPath = computed(() => {
  return document?.location?.pathname;
});

const isExpanded = useLocalStorage("isMenuExpanded", true);

const logout = () => {
  router.post(route("logout"));
};

//  categories
const pageProps = usePage().props;
const isTeamApproved = computed(() => {
  return pageProps?.isTeamApproved;
});

provide("isTeamApproved", isTeamApproved);

const { t } = useI18n();
const { appMenu: currentMenu, headerMenu, mobileMenu } = useAppMenu(isTeamApproved, t);
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

const catchErrors = (errors: Record<string, string>) => {
  Object.keys(errors).forEach((error) => {
    ElNotification({
      message: errors[error],
      title: "Ha ocurrido un error",
      type: "error",
    });
  });
};

const routerEvent = ref<null | Function>(null);
onMounted(() => {
  routerEvent.value = router.on("error", (event) => {
    if (event.detail.errors) catchErrors(event.detail.errors);
  });
});

onUnmounted(() => {
  routerEvent.value && routerEvent.value();
});

const { isOpen, openModal, closeModal } = useToggleModal("fastAccess");

const actions: Record<
  string,
  {
    handler: Function;
  }
> = {
  openAddModal: {
    handler: openModal,
  },
};

const handleActions = (action: string) => {
  actions[action]?.handler();
};

function refresh() {
  nextTick(() => {
    router.reload({
      preserveScroll: true,
      preserveState: true,
    });
  });
}
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
          <slot name="title">
            <div class="flex items-center">
              <AppButton @click="$emit('back')" v-if="showBackButton">
                <IMdiChevronLeft />
              </AppButton>
              <h4
                :class="[showBackButton ? 'lg:ml-2' : 'lg:ml-6']"
                class="pl-4 text-lg font-bold md:pl-0 md:text-secondary capitalize text-white"
              >
                {{ title }}
              </h4>
            </div>
          </slot>

          <div class="flex justify-end h-16">
            <div class="flex items-center sm:ml-6">
              <AppResourceSearch class="hidden mr-2 md:block" />

              <AddNewButton
                class="hidden mr-2 md:inline-block"
                v-if="!isOnboarding && isTeamApproved"
              >
                <AppButton class="items-center hidden px-1 ml-2 mr-4 md:flex">
                  <IMdiPlus class="mr-2" />
                  {{ $t("commons.new") }}
                </AppButton>
              </AddNewButton>
              <AppResourceSearch v-if="false" />
              <AppNotificationBell
                :count="pageProps.unreadNotifications.count"
                :notifications="pageProps.unreadNotifications.data"
                @click="$router.visit('/notifications')"
              />
              <!-- Settings Dropdown -->
              <div class="relative ml-3">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <AppButtonCircle type="button">
                      <span class="text-white md:text-body-1">
                        <IMdiQuestionMark />
                      </span>
                    </AppButtonCircle>
                  </template>

                  <template #content>
                    <!-- Account Management -->
                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Manage Account
                    </div>

                    <DropdownLink href="/help"> Guia de inicio </DropdownLink>

                    <DropdownLink href="/help"> Ayuda </DropdownLink>
                    =
                    <div class="border-t border-gray-100" />
                  </template>
                </Dropdown>
              </div>
              <!-- Settings Dropdown -->
              <div class="relative ml-3">
                <Dropdown align="right" width="48">
                  <template #trigger>
                    <button
                      v-if="$page.props.jetstream.managesProfilePhotos"
                      class="flex text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300"
                    >
                      <img
                        class="object-cover w-8 h-8 min-w-min rounded-full"
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
                    <DropdownLink href="/admin" v-if="$page.props.isAdmin">
                      Admin
                    </DropdownLink>

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
          v-auto-animate
          class="border-none shadow-none text-secondary text-bold bg-secondary"
          title="ICLoan"
          :is-expanded="isExpanded"
          @update:isExpanded="isExpanded = $event"
          :menu="currentMenu"
          :header-menu="headerMenu"
          :current-path="currentPath"
          brand-container-class="py-2"
          nav-container-class="px-2 pt-1 space-y-2 border-t border-base-lvl-3/20"
          icon-class="text-gray-100 py-2.5 transition hover:text-primary hover:bg-base-lvl-3/10"
          item-class="px-5 rounded-md font-bold text-sm text-gray-200 w-54 hover:text-white hover:bg-base-lvl-3/20"
          item-active-class="text-white rounded-md text-sm bg-base-lvl-3/10"
          is-expandable
        >
          <template #brand>
            <!-- Logo -->
            <h1
              class="flex items-center w-full text-gray-100"
              :class="{ ' md:shrink-0 md:px-7': isExpanded }"
            >
              <Link
                :href="route('dashboard')"
                class="flex items-center justify-center space-x-2"
                :class="{ 'mx-auto': !isExpanded }"
              >
                <ApplicationMark class="flex justify-center w-10 h-14" />
                <span class="text-xl font-bold text-white" v-if="isExpanded">
                  ICLoan
                </span>
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
        <main class="px-4 pt-0 mx-auto md:pt-8">
          <slot />
        </main>
      </template>
    </AppShell>
    <MobileMenuBar :menu="mobileMenu" @action="handleActions" />
    <TheGlobals @reload="refresh" />

    <ResponsiveModal
      :show="isOpen"
      max-width="mobile"
      :closeable="true"
      @close="closeModal"
    >
      <FastAccessOptions />
    </ResponsiveModal>
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
