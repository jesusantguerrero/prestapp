<script setup lang="ts">
import { Link } from "@inertiajs/vue3";
import { AtBackgroundIconCard } from "atmosphere-ui";
import { ElTag } from "element-plus";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import EmptyAddTool from "@/Pages/Properties/Partials/EmptyAddTool.vue";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import LoanSectionNav from "@/Pages/Loans/Partials/LoanSectionNav.vue";

import { formatMoney } from "@/utils";
import { IClient } from "@/Modules/clients/clientEntity";
import { clientInteractions } from "@/Modules/clients/clientInteractions";
import { useToggleModal } from "@/Modules/_app/useToggleModal";

export interface Props {
  clients: IClient;
  currentTab: string;
  hideStatistics: boolean;
  type: string;
  contract?: Record<string, any>;
  tabs: Record<string, string>;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
  hideStatistics: false,
  tabs: {
    // @ts-ignore;
    "": "Detalles",
    contracts: "Contratos",
    transactions: "Transacciones",
  },
});

const getTabUrl = (tab: string = "") => {
  return `/contacts/${props.clients.id}/${props.type}?section=${tab}`;
};

const { openModal } = useToggleModal("contact");
</script>

<template>
  <AppLayout :title="`Clientes / ${clients.fullName}`">
    <template #header>
      <PropertySectionNav v-if="!clients.is_lender">
        <template #actions>
          <AppButton
            @click="
              openModal({
                data: {
                  formData: clients,
                },
                isOpen: true,
              })
            "
            variant="inverse"
          >
            Editar
          </AppButton>
          <AppButton @click="" variant="inverse"> Nueva Propiedad </AppButton>
        </template>
      </PropertySectionNav>
      <LoanSectionNav v-else />
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
            <section class="text-center" v-if="clients.is_owner">
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

      <section
        class="flex-col md:flex-row flex w-full md:space-x-8 rounded-t-none border-t-none"
      >
        <article class="w-full md:w-9/12 space-y-4">
          <section
            class="flex flex-col md:flex-row md:space-y-0 space-y-4 md:space-x-4"
            v-if="!hideStatistics"
          >
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

        <article class="w-full md:w-3/12 mt-4 md:mt-0 space-y-2 rounded-md shadow-md">
          <div class="px-5 py-10 text-gray-600 bg-gray-200 rounded-md">
            <div class="header">
              <h2 class="text-lg font-bold">Manejo de contacto</h2>
              <small>
                Espacio para manejar los detalles de tu contacto y todas sus cosas.</small
              >
            </div>

            <div class="mt-4 space-y-2">
              <section class="flex space-x-4">
                <AppButton
                  v-if="clients.is_owner"
                  class="w-full"
                  @click="clientInteractions.generateOwnerDistribution(clients.id)"
                >
                  Generar de propiedades
                </AppButton>
              </section>
              <slot name="options">
                <EmptyAddTool> Personas de contacto </EmptyAddTool>
                <EmptyAddTool> Imagenes </EmptyAddTool>
                <EmptyAddTool> Notas </EmptyAddTool>
                <EmptyAddTool> Informacion de entrada </EmptyAddTool>
              </slot>
            </div>
          </div>
        </article>
      </section>
    </main>
  </AppLayout>
</template>
