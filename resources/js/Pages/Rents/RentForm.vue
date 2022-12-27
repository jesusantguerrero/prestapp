<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtButton, AtField, AtFieldCheck, AtInput, AtSelect } from "atmosphere-ui";
import { addMonths, format as formatDate } from "date-fns";
import { ILoan } from "@/Modules/loans/loanEntity";
import { router } from "@inertiajs/core";
import { IProperty } from "@/Modules/properties/propertyEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import { useForm } from "@inertiajs/vue3";
import FormTemplate from "./Partials/FormTemplate.vue";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import ServiceBlock from "./Partials/ServiceBlock.vue";
import { watch } from "vue";
import FormSection from "./Partials/FormSection.vue";
import TaxTypeSelector from "../Settings/TaxTypeSelector.vue";

defineProps<{
  loanData: ILoan[];
  properties: IProperty[];
  clients: IClient[];
}>();

const rentForm = useForm({
  property_id: null,
  property: null,
  client_id: null,
  date: new Date(),
  deposit: 0,
  deposit_due: new Date(),
  is_deposit_received: false,
  amount: 0,
  first_invoice_date: addMonths(new Date(), 1),
  next_invoice_date: addMonths(new Date(), 1),
  commission: 10,
  commission_type: "",
  late_fee: 10,
  late_fee_type: "",
  grace_days: 0,
  frequency: "MONTHLY",
  additional_fees: [],
});

watch(
  () => rentForm.property,
  (property) => {
    if (property) {
      rentForm.amount = property.price;
    }
  }
);

const onSubmit = () => {
  rentForm
    .transform((data) => ({
      ...rentForm.data(),
      deposit_due: formatDate(rentForm.deposit_due, "y-M-d"),
      date: formatDate(rentForm.date, "yyyy-MM-dd"),
      first_invoice_date: formatDate(rentForm.first_invoice_date, "y-M-d"),
      next_invoice_date: formatDate(rentForm.next_invoice_date, "y-M-d"),
      property: undefined,
    }))
    .submit("post", route("rents.store"), {
      onSuccess() {
        router.visit(`/properties/`);
      },
    });
};

const goToList = () => {
  router.visit("/loans");
};

const addAdditionalFee = () => {
  const index = rentForm.additional_fees.length + 1;
  rentForm.additional_fees.push({
    index: index,
    concept: "",
    description: "",
    price: 0,
    quantity: 1,
    total: "",
  });
};
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
            <AppButton variant="inverse" @click="onSubmit"> Crear Contrato </AppButton>
          </section>
        </template>
      </PropertySectionNav>
    </template>

    <FormTemplate>
      <main class="w-full p-5 bg-white rounded-md text-body-1">
        <article class="w-full">
          <h1 class="font-bold">Detalles Generales</h1>
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

        <article class="w-full space-y-4">
          <FormSection title="Datos de deposito">
            <AtField label="Deposito" class="w-full">
              <AtInput :number-format="true" v-model="rentForm.deposit" rounded>
                <template #suffix>
                  <button
                    class="w-32 px-2 transition"
                    :class="[
                      rentForm.is_deposit_received
                        ? 'bg-success text-white font-bold'
                        : 'bg-base-lvl-2 text-body-1',
                    ]"
                    @click="rentForm.is_deposit_received = !rentForm.is_deposit_received"
                  >
                    {{ rentForm.is_deposit_received ? "Pagado" : "No Pagado" }}
                  </button>
                </template>
              </AtInput>
            </AtField>
            <AtField label="Fecha de pago deposito" class="flex flex-col w-full">
              <ElDatePicker v-model="rentForm.deposit_due" size="large" class="w-full" />
            </AtField>
          </FormSection>

          <FormSection title="Datos de renta" section-class="flex space-x-4">
            <AtField label="Precio de renta" class="w-full">
              <AtInput :number-format="true" v-model="rentForm.amount" />
            </AtField>
            <AtField label="Comisión" class="w-full">
              <AtInput v-model="rentForm.commission">
                <template #suffix>
                  <TaxTypeSelector v-model="rentForm.commission_type" />
                </template>
              </AtInput>
            </AtField>
            <AtField label="Fecha de Inicio" class="flex flex-col">
              <ElDatePicker v-model="rentForm.date" size="large" />
            </AtField>
            <AtField label="Fecha de primer pago" class="flex flex-col">
              <ElDatePicker v-model="rentForm.first_invoice_date" size="large" disabled />
            </AtField>
          </FormSection>

          <FormSection title="Moras" section-class="space-y-4">
            <section class="mt-4 flex space-x-4">
              <AppButton
                v-if="!rentForm.has_late_fees"
                @click="rentForm.has_late_fees = true"
              >
                Aplicar Moras</AppButton
              >
              <template v-else>
                <AtField label="Comisión de mora" class="w-full">
                  <AtInput v-model="rentForm.late_fee">
                    <template #suffix>
                      <TaxTypeSelector v-model="rentForm.late_fee_type" />
                    </template>
                  </AtInput>
                </AtField>
                <AtField label="Dias de gracia" class="w-full">
                  <AtInput v-model="rentForm.grace_days" />
                </AtField>
              </template>
            </section>

            <section>
              <ServiceBlock
                v-if="rentForm.additional_fees.length"
                v-model:items="rentForm.additional_fees"
                subtitle=""
                :hide-fields="['qty', 'description']"
                :labels="{
                  name: 'Servicio',
                  price: 'Precio',
                }"
                class="py-2"
              />
              <AtButton class="text-gray-400" @click="addAdditionalFee()">
                <i class="mr-2 fa fa-plus-circle"></i>
                Add service
              </AtButton>
            </section>
          </FormSection>
        </article>
      </main>
    </FormTemplate>
  </AppLayout>
</template>
