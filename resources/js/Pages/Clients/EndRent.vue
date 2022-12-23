<script setup lang="ts">

import { AtField } from "atmosphere-ui";
import { ElDatePicker } from "element-plus";
import AppButton from "../../Components/shared/AppButton.vue";
import { IClient } from "../../Modules/clients/clientEntity";
import InvoiceCard from "../Rents/Partials/InvoiceCard.vue";
import ClientTemplate from "./ClientTemplate.vue";

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
          <p>{{ property.property_type}}</p>
        </AtField>
        <AtField label="Fecha de inicio">
          <p>{{ rent.date }}</p>
        </AtField>
      </section>

      <section>
        <h4 class="mb-4 text-lg font-bold">Facturas Pendientes</h4>
        <InvoiceCard
            v-for="invoice in pendingInvoices"
            :invoice="invoice"
        />
        <AppButton>
          Agregar Factura
        </AppButton>
      </section>

      <section>
        <h4 class="mb-4 text-lg font-bold">Retornar Depositos</h4>
        <InvoiceCard
            v-for="invoice in depositsToReturn"
            :invoice="invoice"
        />
        <AppButton v-if="depositsToReturn.length">Retornar deposito</AppButton>
        <AppButton>Agregar Nota</AppButton>
      </section>
      
      <footer class="flex justify-between">
        <AppButton type="error">
          Cancelar
        </AppButton>

        <section>
          <ElDatePicker v-model="rent.end_date" size="large" />
          <AppButton type="error">
            Finalizar Contrato
          </AppButton>
        </section>

      </footer>

    </main>

  </ClientTemplate>
</template>
