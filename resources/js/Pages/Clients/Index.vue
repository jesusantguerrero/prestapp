<script setup lang="ts">
import { computed, toRefs, reactive } from "vue";
import { router } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import ClientsTable from "./Partials/ClientsTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "@/Pages/Loans/Partials/LoanSectionNav.vue";
// @ts-ignore: its my template
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import ButtonCircle from "@/Components/mobile/ButtonCircle.vue";

import { IClient } from "@/Modules/clients/clientEntity";
import { clientStatus } from "@/Modules/clients/constants";
import { IPaginatedData } from "@/utils/constants";
import { IServerSearchData, useServerSearch } from "@/utils/useServerSearch";
import { useResponsive } from "@/utils/useResponsive";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import { ElMessageBox } from "element-plus";

const props = withDefaults(
  defineProps<{
    clients: IClient[] | IPaginatedData<IClient>;
    type?: string;
    serverSearchOptions: IServerSearchData;
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
    owner: "Propietarios",
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
  reset,
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

const filters = reactive({
  status:
    clientStatus.find((status) => status.name === searchState.filters.status) ??
    clientStatus[0],
});

const onStateSelected = (status: Record<string, string>) => {
  searchState.filters.status = status.name;
  executeSearch();
};

const deleteClient = async (client: IClient) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el cliente ${client.fullName}?`,
    "Eliminar cliente"
  );
  if (isValid) {
    router.delete(route("clients.destroy", client), {
      onSuccess() {
        router.reload();
      },
    });
  }
};
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <LoanSectionNav v-if="type == 'lender'" />
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
    <main class="mt-16">
      <section class="flex space-x-4">
        <AppSearch
          v-model.lazy="searchState.search"
          class="w-full md:flex"
          :has-filters="true"
          @clear="reset()"
          @blur="executeSearch"
        />
        <article class="flex space-x-2" v-if="type == 'lender'">
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
        </article>
        <article v-if="type == 'tenant'">
          <BaseSelect
            placeholder="Filtrar"
            :options="clientStatus"
            v-model="filters.status"
            label="label"
            track-by="name"
            size="large"
            @update:model-value="onStateSelected"
          />
        </article>
      </section>

      <ClientsTable
        class="bg-white rounded-md"
        :clients="listData"
        :pagination="searchState"
        :total="paginationTotal"
        @delete="deleteClient"
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
