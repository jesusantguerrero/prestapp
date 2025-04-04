<script setup lang="ts">
import { Link, router, useForm } from "@inertiajs/vue3";
// @ts-ignore
import { AtBackgroundIconCard } from "atmosphere-ui";
import { ElTag } from "element-plus";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import EmptyAddTool from "@/Pages/Properties/Partials/EmptyAddTool.vue";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import LoanSectionNav from "@/Pages/Loans/Partials/LoanSectionNav.vue";

import { formatMoney } from "@/utils";
import { IClientSaved } from "@/Modules/clients/clientEntity";
import { clientInteractions } from "@/Modules/clients/clientInteractions";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { computed } from "vue";

export interface Props {
  clients: IClientSaved;
  currentTab: string;
  hideStatistics: boolean;
  type: string;
  stats?: Record<string, string>;
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
const { openModal: openChargeModal } = useToggleModal("propertyCharge");

const sectionName = computed(() => {
  const clientTypes: Record<string, string> = {
    owners: "Propietarios",
    owner: "Propietarios",
    tenants: "Inquilino",
    lenders: "Cliente",
  };

  return clientTypes[props.type] ?? clientTypes.lenders;
});

const finishAdministrationForm = useForm({});

const finishAdministration = () => {
  finishAdministrationForm.post(route("owners.finish-administration", { client: props.clients.id }), {
    onSuccess: () => {
      router.reload();
    },
  });
};
</script>

<template>
  <AppLayout :title="`${sectionName} / ${clients.fullName}`">
    <template #header>
      <PropertySectionNav v-if="['owner', 'tenant'].includes(type)" />
      <LoanSectionNav v-else />
    </template>

    <main class="p-5 mt-8">
      <AppSectionHeader
        name="Clientes"
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="clients"
        :title="clients?.fullName"
        hide-action
      >
        <template #actions v-if="type == 'tenant'">
          <section class="flex space-x-2">
            <AppButton
              v-if="!contract && !clients.is_lender"
              @click="router.visit(`/rents/create?client=${clients.id}`)"
              variant="success"
              title="Registrar mudanza"
            >
              <IMdiHomePlusOutline class="mr-2" />
              Registrar mudanza
            </AppButton>
            <AppButton
              v-if="contract"
              @click="
                router.visit(`/rents/${contract?.id}/transactions/deposit-refund/create`)
              "
              variant="secondary"
              title="Reembolsar deposito"
            >
              Ret. Deposito
            </AppButton>
            <AppButton
              v-if="contract"
              @click="
                openChargeModal({
                  data: {
                    clientId: clients.id,
                    rentId: contract?.id,
                    hideClientOptions: true,
                  },
                  isOpen: true,
                })
              "
              variant="secondary"
              title="Registrar gasto"
            >
              Registrar Gasto
            </AppButton>
            <AppButton
              title="Editar Inquilino"
              @click="
                openModal({
                  data: {
                    formData: clients,
                  },
                  isOpen: true,
                })
              "
              variant="neutral"
            >
              <IMdiEdit />
            </AppButton>
          </section>
        </template>
        <template #actions v-if="type == 'owner'">
          <AppButton
            title="Finalizar administración"
            variant="neutral"
            :processing="finishAdministrationForm.processing"
            @click="finishAdministration()"
          >
            Finalizar administración
          </AppButton>
        </template>
      </AppSectionHeader>
      <header
        class="w-full px-5 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <section class="flex items-center justify-between py-4">
          <article>
            <p class="flex items-center space-x-2">
              <IconMarker />
              <span> {{ clients.dni }} {{ type }} </span>
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
              :value="formatMoney(stats?.balance)"
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

        <article class="w-full md:w-3/12 mt-4 md:mt-0 space-y-2">
          <div class="px-5 py-10 text-gray-600 bg-gray-200 rounded-md shadow-md">
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
                  Generar factura de propiedades
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
