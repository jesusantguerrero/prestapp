<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { IPaginatedData } from "@/utils/constants";
import { computed, toRefs } from "vue";
import ClientsTable from "./Partials/ClientsTable.vue";
import { router } from "@inertiajs/vue3";

// @ts-ignore: its my template
import LoanSectionNav from "@/Pages/Loans/Partials/LoanSectionNav.vue";
// @ts-ignore: its my template
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { useServerSearch } from "@/utils/useServerSearch";
import { useResponsive } from "@/utils/useResponsive";
import ButtonCircle from "@/Components/mobile/ButtonCircle.vue";

const props = withDefaults(
  defineProps<{
    clients: IClient[] | IPaginatedData<IClient>;
    type?: string;
    serverSearchOptions: Object;
  }>(),
  {
    serverSearchOptions: () => ({
      filters: {},
      dates: {},
      sorts: "",
      limit: 10,
      relationships: "",
      search: "",
      page: 1,
    }),
  }
);

const listData = computed(() => {
  return Array.isArray(props.clients) ? props.clients : props.clients.data;
});

const paginationTotal = computed(
  () => !Array.isArray(props.clients) && props.clients.total
);

const { toggleModal } = useToggleModal("contact");

const sectionTitle = computed(() => {
  const titles = {
    owner: "Dueños de propiedades",
    tenant: "Inquilinos",
    lender: "Clientes de prestamos",
  };

  return titles[props.type] ?? "Clientes";
});

const { serverSearchOptions } = toRefs(props);
const {
  executeSearch,
  updateSearch,
  changeSize,
  paginate,
  state: searchState,
} = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    updateSearch(`/contacts/${props.type}?${finalUrl}`);
  },
  {
    manual: true,
  }
);

const { isMobile } = useResponsive();
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <LoanSectionNav v-if="type == 'lender'">
        <template #actions>
          <AppButton
            variant="inverse-secondary"
            class="hidden md:flex"
            @click="router.visit('/loans/create')"
          >
            Nuevo prestamo
          </AppButton>
          <AppButton
            variant="secondary"
            class="hidden md:flex"
            @click="
              toggleModal({
                data: {
                  type: type,
                },
                isOpen: true,
              })
            "
          >
            Nuevo cliente
          </AppButton>
        </template>
      </LoanSectionNav>
      <PropertySectionNav v-else>
        <template #actions>
          <AppButton
            variant="inverse"
            @click="router.visit(route('properties.create'))"
            v-if="type == 'owner'"
          >
            Agregar Propiedad
          </AppButton>
          <AppButton
            variant="inverse"
            @click="router.visit(route('owners.draw'))"
            v-if="type == 'owner'"
          >
            Pagar distribucion
          </AppButton>
          <AppButton
            variant="inverse-secondary"
            @click="router.visit(route('properties.create'))"
            v-else
          >
            Agregar Contrato
          </AppButton>
          <AppButton
            variant="secondary"
            @click="
              toggleModal({
                data: {
                  type: type,
                },
                isOpen: true,
              })
            "
          >
            Agregar {{ type }}
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>
    <main class="mt-16 bg-white rounded-md">
      <ClientsTable
        :clients="listData"
        :pagination="searchState"
        :total="paginationTotal"
        @search="executeSearch"
        @paginate="paginate"
        @size-change="changeSize"
      />
      <div class="fixed bottom-16 right-5 flex space-x-2" v-if="isMobile">
        <ButtonCircle @click="router.visit('/loans/create')">
          <IMdiDocument />
        </ButtonCircle>
        <ButtonCircle
          @click="
            toggleModal({
              data: {
                type: type,
              },
              isOpen: true,
            })
          "
        >
          <IMdiPlus />
        </ButtonCircle>
      </div>
    </main>
  </AppLayout>
</template>
