<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
import { AtButton, AtField } from "atmosphere-ui";
import { Action, ElDatePicker, ElMessageBox } from "element-plus";

import AppButton from "../../../Components/shared/AppButton.vue";
import InvoiceCard from "../../../Components/templates/InvoiceCard.vue";
import ClientTemplate from "../Partials/ClientTemplate.vue";

import { IClient } from "../../../Modules/clients/clientEntity";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { parseISO } from "date-fns";

export interface Props {
  clients: IClient;
  currentTab: string;
  rent: Object;
  property: Object;
  pendingInvoices: any[];
  depositsToReturn: any[];
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const endRentForm = useForm({
  end_date: parseISO(props.rent.end_date),
  amount: props.rent.amount,
});

const onSubmit = () => {
  ElMessageBox.alert("¿Seguro que desea extender este contrato?", "Extender Contrato", {
    confirmButtonText: "Si, Extender Contraro",
    cancelButtonText: "Cancelar",
    showCancelButton: true,
    callback: (action: Action) => {
      if (action === "confirm") {
        endRentForm.put(
          route("tenant.renew-rent-action", {
            client: props.clients,
            rent: props.rent,
          })
        );
      }
    },
  });
};
</script>

<template>
  <ClientTemplate
    type="tenant"
    :current-tab="currentTab"
    :clients="clients"
    hide-statistics
  >
    <main class="w-full px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3">
      <header>
        <h4 class="text-2xl font-bold text-body-1">Extender Contrato</h4>
      </header>

      <section class="grid grid-cols-3">
        <AtField label="Propiedad">
          <p>{{ property.name }}</p>
        </AtField>
        <AtField label="Tipo de Propiedad">
          <p>{{ property.property_type }}</p>
        </AtField>
        <AtField label="Fecha de inicio">
          <p>{{ rent.date }}</p>
        </AtField>
      </section>

      <section>
        <h4 class="mb-4 text-lg font-bold">Datos de renovacion</h4>
        <section class="flex">
          <AppFormField label="Fecha de finalización">
            <ElDatePicker v-model="endRentForm.end_date" size="large" />
          </AppFormField>
          <AppFormField
            label="Mensualidad"
            class="w-full"
            :number-format="true"
            v-model="endRentForm.amount"
          />
        </section>
      </section>

      <footer class="flex justify-between">
        <AtButton
          class="font-bold transition border text-body-1 hover:text-error hover:border-error"
          rounded
        >
          Cancelar
        </AtButton>

        <section class="space-x-2">
          <AppButton variant="secondary" @click="onSubmit"> Extender Contrato </AppButton>
        </section>
      </footer>
    </main>
  </ClientTemplate>
</template>
