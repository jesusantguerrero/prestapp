<script setup lang="ts">
import { useForm } from "@inertiajs/vue3";
// @ts-ignore
import { AtButton, AtField } from "atmosphere-ui";
import { Action, ElDatePicker, ElMessageBox } from "element-plus";
import { parseDate, formatDate } from "@/utils";

import AppButton from "@/Components/shared/AppButton.vue";
import ClientTemplate from "../Partials/ClientTemplate.vue";

import { IClientSaved } from "@/Modules/clients/clientEntity";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { IProperty, IRent } from "@/Modules/properties/propertyEntity";
import { ref, watchEffect } from "vue";
import { differenceInMonths } from "date-fns";
import { generateInstallments } from "@/Modules/loans/features";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

export interface Props {
  clients: IClientSaved;
  currentTab: string;
  rent: IRent;
  property: IProperty;
  pendingInvoices: any[];
  depositsToReturn: any[];
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const formData = useForm({
  end_date: parseDate(props.rent.end_date),
  amount: props.rent.amount,
  has_paid: false,
  paid_until: null,
});

const paymentSchedule = ref();
watchEffect(() => {
  const date = formatDate(parseDate(props.rent.end_date), "yyyy-MM-dd");
  const count = differenceInMonths(formData.end_date, parseDate(props.rent.end_date));
  if (formData.amount && count) {
    paymentSchedule.value = generateInstallments({
      interest_rate: 0,
      amount: formData.amount,
      repayment_count: count,
      first_repayment_date: date,
      frequency: "MONTHLY",
    }).payments;
  }
});

const onSubmit = () => {
  if (formData.processing) return;
  ElMessageBox.alert("¿Seguro que desea extender este contrato?", "Extender Contrato", {
    confirmButtonText: "Si, Extender Contraro",
    cancelButtonText: "Cancelar",
    showCancelButton: true,
    callback: (action: Action) => {
      if (action === "confirm") {
        formData
          .transform((data) => ({
            ...data,
            paid_until: data.paid_until?.due_date,
          }))
          .put(
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
        <section class="flex space-x-2">
          <AppFormField label="Fecha de finalización">
            <section class="h-10 form-custom-group">
              <ElDatePicker v-model="formData.end_date" size="large" />
              <button
                class="w-32 h-full px-2 transition"
                :class="[
                  formData.has_paid
                    ? 'bg-success text-white font-bold'
                    : 'bg-base-lvl-2 text-body-1',
                ]"
                @click="formData.has_paid = !formData.has_paid"
              >
                {{ formData.has_paid ? "Con Pagos" : "Sin Pagos" }}
              </button>
            </section>
          </AppFormField>
          <AppFormField
            label="Ultimo pago registrado"
            class="w-full"
            v-if="paymentSchedule && formData.has_paid"
          >
            <BaseSelect
              placeholder="Ultimo pago"
              :options="paymentSchedule"
              v-model="formData.paid_until"
              label="due_date"
              track-by="due_date"
              size="large"
            />
          </AppFormField>
          <AppFormField
            label="Mensualidad"
            class="w-full"
            :number-format="true"
            v-model="formData.amount"
          />
        </section>
      </section>

      <footer class="flex justify-between">
        <AtButton
          class="font-bold transition border text-body-1 hover:text-error hover:border-error"
          rounded
          :disabled="formData.processing"
        >
          Cancelar
        </AtButton>

        <section class="space-x-2">
          <AppButton
            variant="secondary"
            @click="onSubmit"
            :processing="formData.processing"
            :disabled="formData.processing"
          >
            Extender Contrato
          </AppButton>
        </section>
      </footer>
    </main>
  </ClientTemplate>
</template>
