<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtButton, AtField, AtInput, AtSelect, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed } from "vue";
import { add, addMonths, format as formatDate } from "date-fns";
import { createLoan, generateInstallments } from "../../Modules/loans/features";
import { ILoan } from "../../Modules/loans/loanEntity";
import { router } from "@inertiajs/core";
import InstallmentTable from "./Partials/InstallmentTable.vue";
import { IProperty } from "../../Modules/properties/propertyEntity";
import { IClient } from "../../Modules/clients/clientEntity";
import { useForm } from "@inertiajs/vue3";

defineProps<{
  loanData: ILoan[];
  properties: IProperty[];
  clients: IClient[];
}>();

const rentForm = useForm({
  property_id: null,
  client_id: null,
  date: new Date(),
  first_invoice_date: addMonths(new Date(), 1) ,
  amount: 0,
  commission_rate: 0,
  commission_type: '',
  frequency: "MONTHLY",
});

const onSubmit = () => {
    rentForm.transform(data => ({
      ...rentForm.data(),
      date: formatDate(rentForm.date, "yyyy-MM-dd"),
      first_invoice_date: formatDate(rentForm.first_invoice_date, 'y-M-d')
    }))
    .submit('post', route('rents.store'), {
      onSuccess: () => {
        router.visit(`/loans/`);
      }
    });
};

const goToList = () => {
  router.visit("/loans");
};
</script>

<template>
  <AppLayout title="Crear alquiler">
    <main class="w-full p-5 bg-white rounded-md">
      <h1 class="font-bold">Detalles Generales</h1>
      <article class="w-full">
        <section class="flex space-x-4">
          <AtField label="Inquilino" class="w-full">
            <AtSelect
                v-model="rentForm.client_id"
                v-model:selected="rentForm.client"
                :options="clients"
                placeholder="Selecciona un inquilino"
                label="display_name"
                key-track="id"
            />
          </AtField>
          <AtField class="w-6/12" label="Propiedad">
            <AtSelect
                v-model="rentForm.property_id"
                v-model:selected="rentForm.property"
                :options="properties"
                placeholder="Selecciona una propiedad"
                label="address"
                key-track="id"
            />
          </AtField>
        </section>
      </article>

      <article class="w-full">
        <h1 class="font-bold">Datos Alquiler</h1>
        <section class="flex space-x-4">
          <AtField label="Precio de renta" class="w-full">
            <AtInput v-model="rentForm.amount" />
          </AtField>
          <AtField label="Comisión" class="w-full">
            <AtInput v-model="rentForm.commission_rate" />
          </AtField>
          <AtField class="w-full" label="Tipo de comision">
            <AtSelect
                v-model="rentForm.commision_type"
                :options="properties"
                placeholder="Selecciona una propiedad"
                label="address"
                key-track="id"
            />
          </AtField>
        </section>
        <section class="flex space-x-4">
          <AtField label="Fecha de desembolso" class="flex flex-col">
            <ElDatePicker v-model="rentForm.date" size="large" />
          </AtField>
          <AtField label="Fecha de primer pago" class="flex flex-col">
            <ElDatePicker v-model="rentForm.first_invoice_date" size="large" />
          </AtField>
        </section>
      </article>

      <footer class="flex justify-end space-x-2">
        <AtButton
          class="font-bold text-red-400 bg-gray-100 rounded-md"
          variant="secondary"
          @click="goToList()"
        >
          Cancelar
        </AtButton>
        <AtButton
          class="text-white bg-blue-500 rounded-md"
          @click="onSubmit"
        >
          Crear Contrato
        </AtButton>
      </footer>
    </main>
  </AppLayout>
</template>
