<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import { formatMoney } from "@/utils/formatMoney";
import { ILoanInstallment } from "../../Modules/loans/loanInstallmentEntity";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import { IProperty } from "../../Modules/properties/propertyEntity";

export interface Props {
  properties: IProperty;
  currentTab: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  summary: "Detalles",
  reports: "Reportes",
  contracts: "Contratos",
  clients: "Inquilinos",
  notes: "Notas",
  documents: "Documentos",
  settings: "Configuracion",
};

type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);
const onPayment = (installment: ILoanInstallment) => {
  selectedPayment.value = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_due) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  isPaymentModalOpen.value = true;
};

const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.properties.id} pago #${selectedPayment.value.installment_id}`
  );
});

const refresh = () => {
  router.reload();
};
</script>

<template>
  <AppLayout title="Alquiler">
    <template #header>
      <PropertySectionNav> 
          <template #actions>
            <AppButton @click="router.visit(route('properties.create'))">
              Nuevo Contrato
            </AppButton>
          </template>
        </PropertySectionNav>
    </template>

    <main class="p-5 mt-8">
      <AppSectionHeader
        name="Alquiler"
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="properties"
        :title="properties.address"
        @create="router.visit('/properties/create')"
        hide-action
      />
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <div>
          {{ properties.address }}
        </div>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-gray-300': tab == currentTab }"
            :href="`/rents/${properties.id}?current-tab=${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </div>
      <section class="flex w-full space-x-8 rounded-t-none border-t-none ">
        <article class="w-9/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <span> Cliente: {{ properties.names }} {{ properties.lastnames }} </span>
          <p>
            Monto Prestado:
            {{ properties.amount }}
          </p>
          <p>
            Fecha Primer Pago:
            {{ properties.first_invoice_date }}
          </p>
          <p>
            Estatus:
            {{ properties.status }}
          </p>

          <InstallmentTable
            :installments="properties.installments"
            accept-payment
            @pay="onPayment"
          />
        </article>

        <article class="w-3/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <AppButton class="w-full"> Agregar Pago </AppButton>
          <AppButton class="w-full"> Recibo Multiple </AppButton>

          <section class="py-4 mt-8 space-y-2">
            <div v-for="payment in properties.payment_documents" class="text-sm">
              Pagado
              <span class="font-bold text-green-500">
                {{ formatMoney(payment.amount) }}
              </span>
              en
              <span class="font-bold text-primary">
                {{ payment.payment_date }}
              </span>
            </div>
          </section>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        :payment="selectedPayment"
        :endpoint="`/loans/${properties.id}/installments/${selectedPayment.installment_id}/pay`"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
      />
    </main>
  </AppLayout>
</template>
