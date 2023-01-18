<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { IClient } from "@/Modules/clients/clientEntity.ts";
import { IPaginatedData } from "@/utils/constants";
import { computed, ref } from "vue";
import ClientsTable from "./Partials/ClientsTable.vue";

// @ts-ignore: its my template
import LoanSectionNav from "@/Pages/Loans/Partials/LoanSectionNav.vue";
// @ts-ignore: its my template
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";

const props = defineProps<{
  clients: IClient[] | IPaginatedData<IClient>;
  type?: string;
}>();

const listData = computed(() => {
  return Array.isArray(props.clients) ? props.clients : props.clients.data;
});

const isModalOpen = ref(false);

const sectionTitle = computed(() => {
  const titles = {
    owner: "Dueños de propiedades",
    tenant: "Inquilinos",
    lender: "Clientes de prestamos",
  };

  return titles[props.type] ?? "Clientes";
});
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <LoanSectionNav v-if="type == 'lender'">
        <template #actions>
          <AppButton variant="inverse" @click="router.visit('/loans/create')">
            Nuevo prestamo
          </AppButton>
          <AppButton variant="success" @click="isModalOpen = true">
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
            @click="router.visit(route('properties.create'))"
            v-else
          >
            Agregar Contrato
          </AppButton>
          <AppButton variant="success" @click="isModalOpen = true">
            Agregar cliente
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>
    <main class="mt-16 bg-white rounded-md">
      <ClientsTable :clients="listData" />
    </main>
  </AppLayout>
</template>
