<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtButton, AtField, AtFieldCheck, AtInput, AtSelect } from "atmosphere-ui";
import {  addMonths, format as formatDate } from "date-fns";
import { ILoan } from "../../Modules/loans/loanEntity";
import { router } from "@inertiajs/core";
import { IProperty } from "../../Modules/properties/propertyEntity";
import { IClient } from "../../Modules/clients/clientEntity";
import { useForm } from "@inertiajs/vue3";
import FormTemplate from "./Partials/FormTemplate.vue";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import AppButton from "../../Components/shared/AppButton.vue";
import ServiceBlock from "./Partials/ServiceBlock.vue";

defineProps<{
  loanData: ILoan[];
  properties: IProperty[];
  clients: IClient[];
}>();

const rentForm = useForm({
  property_id: null,
  client_id: null,
  date: new Date(),
  deposit: 0,
  deposit_date: addMonths(new Date(), 1),
  is_deposit_received: false,
  amount: 0,
  first_invoice_date: addMonths(new Date(), 1) ,
  next_invoice_date: addMonths(new Date(), 1) ,
  commission_rate: 10,
  commission_type: '',
  frequency: "MONTHLY",
  additional_fees: [],
});

const onSubmit = () => {
    rentForm.transform(data => ({
      ...rentForm.data(),
      date: formatDate(rentForm.date, "yyyy-MM-dd"),
      first_invoice_date: formatDate(rentForm.first_invoice_date, 'y-M-d'),
      next_invoice_date: formatDate(rentForm.next_invoice_date, 'y-M-d')
    }))
    .submit('post', route('rents.store'), {
      onSuccess() {
        debugger
        router.visit(`/properties/`);
      }
    });
};

const goToList = () => {
  router.visit("/loans");
};

const addAdditionalFee = () => {
    const index = rentForm.additional_fees.length + 1
    rentForm.additional_fees.push({
        index: index,
        concept: '',
        description: '',
        price: 0,
        quantity: 1,
        total: ''
    })
}
</script>

<template>
  <AppLayout title="Crear contrato">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <section class="flex justify-end space-x-2">
            <AtButton
              class="font-bold text-red-400 bg-gray-100 rounded-md"
              variant="secondary"
              @click="goToList()"
            >
              Cancelar
            </AtButton>
            <AppButton
              variant="inverse"
              @click="onSubmit"
            >
              Crear Contrato
            </AppButton>
          </section>
        </template>
      </PropertySectionNav>
    </template>

    <FormTemplate>
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
          <section class="grid grid-cols-2 gap-2">
            <AtField label="Deposito" class="w-full">
              <AtInput :number-format="true" v-model="rentForm.deposit" />
            </AtField>
            <AtField label="Fecha de pago deposito" class="flex flex-col w-full">
              <ElDatePicker v-model="rentForm.deposit_date" size="large" class="w-full" />
            </AtField>
            <AtFieldCheck v-model="rentForm.is_deposit_paid" size="large" label="Recibido" />
          </section>
          <section class="flex space-x-4">
            <AtField label="Precio de renta" class="w-full">
              <AtInput :number-format="true" v-model="rentForm.amount" />
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
            <AtField label="Fecha de Inicio" class="flex flex-col">
              <ElDatePicker v-model="rentForm.date" size="large" />
            </AtField>
            <AtField label="Fecha de primer pago" class="flex flex-col">
              <ElDatePicker v-model="rentForm.first_invoice_date" size="large" disabled />
            </AtField>
          </section>
          <section>
            <ServiceBlock
              v-if="rentForm.additional_fees.length"
              v-model:items="rentForm.additional_fees"
              subtitle=""
              :hide-fields="['qty', 'description']"
              :labels="{
                name: 'Servicio',
                price: 'Precio'
              }"
              class="py-2"
            />
            <AtButton
                class="text-gray-400"
                @click="addAdditionalFee()"
            >
                <i class="mr-2 fa fa-plus-circle"></i>
                Add service
            </AtButton>
          </section>
        </article>
      </main>
    </FormTemplate>
  </AppLayout>
</template>
