<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Head, Link } from "@inertiajs/vue3";
import { AtShell, AtSide } from "atmosphere-ui";
import { ElNotification } from "element-plus";

import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";

import { useAppMenu } from "@/Modules/_app";
import { useSelect } from "@/Modules/shared/useSelects";
import AppButton from "../shared/AppButton.vue";
import TheGlobals from "../TheGlobals.vue";

defineProps({
  title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
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
const isExpanded = ref(true);
const logout = () => {
  router.post(route("logout"));
};

const { appMenu: currentMenu, headerMenu } = useAppMenu();

//  categories
const pageProps = usePage().props;
const { categoryOptions: transformCategoryOptions } = useSelect();
transformCategoryOptions(pageProps.value.categories, "sub_categories", "categoryOptions");
transformCategoryOptions(
  pageProps.value.accounts,
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
  () => pageProps.value.errors,
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
</script>

<template>
  <div>
    <Head :title="title" />
    <Banner />
    <AtShell
      :is-expanded="isExpanded"
      :nav-class="[!$slots.header && `${panelShadow} border-b`]"
    >
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
              class="text-lg font-bold"
            >
              {{ title }}
            </h4>
          </div>

          <div class="flex justify-end h-16">
            <div class="hidden sm:flex sm:items-center sm:ml-6">
              <AppButton>Agregar Nuevo</AppButton>
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

            <!-- Hamburger -->
            <div class="flex items-center -mr-2 sm:hidden">
              <button
                class="inline-flex items-center justify-center p-2 text-gray-400 transition rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500"
                @click="showingNavigationDropdown = !showingNavigationDropdown"
              >
                <svg
                  class="w-6 h-6"
                  stroke="currentColor"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <path
                    :class="{
                      hidden: showingNavigationDropdown,
                      'inline-flex': !showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"
                  />
                  <path
                    :class="{
                      hidden: !showingNavigationDropdown,
                      'inline-flex': showingNavigationDropdown,
                    }"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div
          :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
          }"
          class="sm:hidden"
        >
          <div class="pt-2 pb-3 space-y-1">
            <ResponsiveNavLink
              :href="route('dashboard')"
              :active="route().current('dashboard')"
            >
              Dashboard
            </ResponsiveNavLink>
          </div>

          <!-- Responsive Settings Options -->
          <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="flex items-center px-4">
              <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="mr-3 shrink-0"
              >
                <img
                  class="object-cover w-10 h-10 rounded-full"
                  :src="$page.props.user.profile_photo_url"
                  :alt="$page.props.user.name"
                />
              </div>

              <div>
                <div class="text-base font-medium text-gray-800">
                  {{ $page.props.user.name }}
                </div>
                <div class="text-sm font-medium text-gray-500">
                  {{ $page.props.user.email }}
                </div>
              </div>
            </div>

            <div class="mt-3 space-y-1">
              <ResponsiveNavLink
                :href="route('profile.show')"
                :active="route().current('profile.show')"
              >
                Profile
              </ResponsiveNavLink>

              <ResponsiveNavLink
                v-if="$page.props.jetstream.hasApiFeatures"
                :href="route('api-tokens.index')"
                :active="route().current('api-tokens.index')"
              >
                API Tokens
              </ResponsiveNavLink>

              <!-- Authentication -->
              <form method="POST" @submit.prevent="logout">
                <ResponsiveNavLink as="button"> Log Out </ResponsiveNavLink>
              </form>

              <!-- Team Management -->
              <template v-if="$page.props.jetstream.hasTeamFeatures">
                <div class="border-t border-gray-200" />

                <div class="block px-4 py-2 text-xs text-gray-400">Manage Team</div>

                <!-- Team Settings -->
                <ResponsiveNavLink
                  :href="route('teams.show', $page.props.user.current_team)"
                  :active="route().current('teams.show')"
                >
                  Team Settings
                </ResponsiveNavLink>

                <ResponsiveNavLink
                  v-if="$page.props.jetstream.canCreateTeams"
                  :href="route('teams.create')"
                  :active="route().current('teams.create')"
                >
                  Create New Team
                </ResponsiveNavLink>

                <div class="border-t border-gray-200" />

                <!-- Team Switcher -->
                <div class="block px-4 py-2 text-xs text-gray-400">Switch Teams</div>

                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                  <form @submit.prevent="switchToTeam(team)">
                    <ResponsiveNavLink as="button">
                      <div class="flex items-center">
                        <svg
                          v-if="team.id == $page.props.user.current_team_id"
                          class="w-5 h-5 mr-2 text-green-400"
                          fill="none"
                          stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ team.name }}</div>
                      </div>
                    </ResponsiveNavLink>
                  </form>
                </template>
              </template>
            </div>
          </div>
        </div>
      </template>
      <template #aside>
        <AtSide
          class="border-none shadow-none text-bold bg-base-lvl-3"
          title="Prestapp"
          :class="panelShadow"
          v-model:isExpanded="isExpanded"
          :menu="currentMenu"
          :header-menu="headerMenu"
          :current-path="currentPath"
          brand-container-class="py-2"
          nav-container-class="px-2 pt-1 space-y-2 border-t"
          icon-class="text-gray-400 transition hover:text-primary"
          item-class="px-5 py-[0.80rem] rounded-md font-bold text-gray-400 w-54 hover:text-primary hover:bg-base-lvl-1"
          item-active-class="text-primary bg-base-lvl-1/70"
          is-expandable
        >
          <template #brand>
            <!-- Logo -->
            <h1 class="flex items-center w-full shrink-0 px-7">
              <Link :href="route('dashboard')" class="flex items-center space-x-2">
                <ApplicationMark class="block w-auto h-9" />
                <span class="text-xl font-bold"> Prestapp </span>
              </Link>
            </h1>
          </template>
        </AtSide>
      </template>
      <template #main-section>
        <!-- Page Heading -->
        <header
          v-if="$slots.header"
          :class="[isExpanded ? 'lg:pr-56' : 'lg:pr-20', panelShadow]"
          class="fixed z-30 w-full overflow-hidden border-b bg-base-lvl-3 base-deep-1"
        >
          <slot name="header" />
        </header>

        <!-- Page Content -->
        <main class="pt-8 mx-auto md:px-24">
          <slot />
        </main>
      </template>
    </AtShell>
    <TheGlobals />
  </div>
</template>
