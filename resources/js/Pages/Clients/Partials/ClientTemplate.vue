<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";

import { formatMoney } from "@/utils";
import PropertySectionNav from "../../Properties/Partials/PropertySectionNav.vue";
import { ElTag } from "element-plus";
import { AtBackgroundIconCard } from "atmosphere-ui";
import { IClient } from "@/Modules/clients/clientEntity";
import EmptyAddTool from "@/Pages/Properties/Partials/EmptyAddTool.vue";
import { clientInteractions } from "@/Modules/clients/clientInteractions";

export interface Props {
  clients: IClient;
  currentTab: string;
  hideStatistics: boolean;
  type: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
  hideStatistics: false,
});

const tabs = {
  "": "Detalles",
  contracts: "Contratos",
  transactions: "Transacciones",
};

const getTabUrl = (tab: string = "") => {
  return `/contacts/${props.clients.id}/${props.type}?section=${tab}`;
};
</script>

<template>
  <AppLayout :title="`Clientes / ${clients.fullName}`">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton @click="" variant="inverse"> Editar </AppButton>
          <AppButton @click="" variant="inverse"> Nueva Propiedad </AppButton>
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mt-8">
      <AppSectionHeader
        name="Clientes"
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="clients"
        :title="clients?.fullName"
        hide-action
      />
      <header
        class="w-full px-5 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <section class="flex items-center justify-between py-4">
          <article>
            <p class="flex items-center space-x-2">
              <IconMarker />
              <span>
                {{ clients.dni }}
              </span>
            </p>
            <p class="flex items-center space-x-2 cursor-pointer text-primary group">
              <IconPersonSafe />
              <span class="group-hover:underline underline-offset-4">
                {{ clients.account?.name }}
              </span>
            </p>
          </article>
          <article class="flex space-x-5">
            <section class="text-center">
              <div class="flex items-center w-full text-center">
                <IconCoins class="mr-2 text-yellow-600" />
                <span class="mx-auto font-bold text-success">
                  {{ clients.properties?.length }}
                </span>
              </div>
              <p class="text-bold text-body-1">Propiedades</p>
            </section>
            <ElTag> {{ clients.status }}</ElTag>
          </article>
        </section>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-gray-300': tab == currentTab }"
            :href="getTabUrl(tab)"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </header>

      <section class="flex w-full space-x-8 rounded-t-none border-t-none">
        <article class="w-9/12 space-y-4">
          <section class="flex space-x-4" v-if="!hideStatistics">
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Balance de Cuenta"
              :value="formatMoney(0)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Balance de Pendiente"
              :value="formatMoney(0)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Dias de mora"
              :value="0"
            />
          </section>

          <slot />
        </article>

        <article class="w-3/12 space-y-2 rounded-md shadow-md">
          <div class="px-5 py-10 text-gray-600 bg-gray-200 rounded-md">
            <div class="header">
              <h2 class="text-lg font-bold">Manejo de Cliente</h2>
              <small>
                Private place for you and your internal team to manage this project</small
              >
            </div>

            <div class="mt-4 space-y-2">
              <section class="flex space-x-4">
                <AppButton
                  class="w-full"
                  @click="clientInteractions.generateOwnerDistribution(clients.id)"
                >
                  Generar de propiedades
                </AppButton>
              </section>
              <EmptyAddTool> Personas de contacto </EmptyAddTool>
              <EmptyAddTool> Imagenes </EmptyAddTool>
              <EmptyAddTool> Notas </EmptyAddTool>
              <EmptyAddTool> Informacion de entrada </EmptyAddTool>
            </div>
          </div>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
