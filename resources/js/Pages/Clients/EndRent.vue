<script setup lang="ts">

import { useForm } from "@inertiajs/vue3";
import { AtButton, AtField } from "atmosphere-ui";
import { Action, ElDatePicker, ElMessageBox } from "element-plus";

import AppButton from "../../Components/shared/AppButton.vue";
import InvoiceCard from "../Rents/Partials/InvoiceCard.vue";
import ClientTemplate from "./ClientTemplate.vue";

import { IClient } from "../../Modules/clients/clientEntity";

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
  move_out_at: new Date(),
  move_out_notice: ''
});


const onSubmit = () => {
  ElMessageBox.alert('¿Seguro que desea terminar este contrato?', 'Terminar Contrato', {
    confirmButtonText: 'Si, Terminar Contraro',
    cancelButtonText: 'Cancelar',
    showCancelButton: true,
    callback: (action: Action) => {
      if (action === 'confirm') {
        endRentForm.put(route('tenant.end-rent-action',{ client: props.clients, rent: props.rent }))
      }
    }
  })
}

</script>

<template>
  <ClientTemplate :clients="clients" hide-statistics>
    <main class="w-full px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3">
      <header>
        <h4>Terminar Contrato</h4>
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
        <h4 class="mb-4 text-lg font-bold">Facturas Pendientes</h4>
        <section class="space-y-2">
          <InvoiceCard
              v-for="invoice in pendingInvoices"
              :invoice="invoice"
          />
          <span class="text-body-1" v-if="!pendingInvoices.length"> No hay facturas pendientes </span>
        </section>
        <p class="text-end">
          <AtButton class="font-bold text-success">
            Agregar Factura
          </AtButton>
        </p>
      </section>

      <section>
        <h4 class="mb-4 text-lg font-bold">Retornar Depositos</h4>
        <section>
          <InvoiceCard
              v-for="invoice in depositsToReturn"
              :invoice="invoice"
          />
          <span class="text-body-1" v-if="!depositsToReturn.length"> No hay depositos pendientes </span>
        </section>
        <p class="text-end">
          <AtButton v-if="depositsToReturn.length" class="font-bold text-success">Retornar deposito</AtButton>
        </p>
        <AtButton>Agregar Nota</AtButton>
      </section>
      
      <footer class="flex justify-between">
        <AtButton class="font-bold transition border text-body-1 hover:text-error hover:border-error" rounded>
          Cancelar
        </AtButton>

        <section class="space-x-2">
          <ElDatePicker v-model="endRentForm.move_out_at" size="large" />
          <AppButton variant="error" @click="onSubmit">
            Finalizar Contrato
          </AppButton>
        </section>
      </footer>

    </main>

  </ClientTemplate>
</template>
