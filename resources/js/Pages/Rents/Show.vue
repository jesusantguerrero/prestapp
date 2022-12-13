<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";
import PaymentFormModal from "./Partials/PaymentFormModal.vue";
import { formatMoney, formatDate } from "@/utils";
import { ILoanInstallment } from "../../Modules/loans/loanInstallmentEntity";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";

export interface Props {
  rents: ILoanWithInstallments;
  currentTab: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  summary: "Detalles",
  documents: "Tabla de Amortización",
  transactions: "Pagos",
  details: "Details",
};

const clientName = computed(() => props.rents.client.names + " " + props.rents.client?.lastnames )
const sectionTitle = computed(() => `Alquiler - ${clientName.value}`)

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
    `Pago ${props.rents.id} pago #${selectedPayment.value.installment_id}`
  );
});

const refresh = () => {
  router.reload();
};
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <PropertySectionNav> 
          <template #actions>
            <AppButton variant="inverse" @click="router.visit(route('rents.create'))">Agregar Contrato</AppButton>
          </template>
        </PropertySectionNav>
    </template>

    <main class="p-5 mt-8">
      <AppSectionHeader
        name="Contrato de Alquiler a"
        class="px-5 bg-white border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="rents"
        :title="`${clientName}`"
        hide-action
        @create="router.visit('/loans/create')"
      />
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <div>
          Alquiler #{{ rents.id }} para {{ clientName }}
        </div>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-gray-300': tab == currentTab }"
            :href="`/rents/${rents.id}?current-tab=${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </div>
      <section class="flex w-full space-x-8 rounded-t-none border-t-none ">
        <article class="w-9/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <span> Cliente: {{ clientName }} </span>
          <p>
            Mensualidad:
            {{ rents.amount }}
          </p>
          <p>
            Fecha de Inicio:
            {{ formatDate(rents.date) }}
          </p>
          <p>
            Proximo pago:
            {{ formatDate(rents.next_invoice_date) }}
          </p>
          <p>
            Estatus:
            {{ rents.status }}
          </p>

          <InstallmentTable
            :installments="rents.installments"
            accept-payment
            @pay="onPayment"
          />
        </article>

        <article class="w-3/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <AppButton class="w-full"> Agregar Pago </AppButton>
          <AppButton class="w-full"> Recibo Multiple </AppButton>

          <section class="py-4 mt-8 space-y-2">
            <div class="text-sm" v-if="rents.transaction">
              {{ rents.transaction.description}}
              <span class="font-bold text-green-500">
                {{ formatMoney(rents.transaction.total) }}
              </span>
              en
              <span class="font-bold text-primary">
                {{ formatDate(rents.date) }}
              </span>
            </div>
            <div v-for="payment in rents.invoices" class="text-sm">
              {{ payment.concept }} {{ payment.description }}
              <span class="font-bold text-green-500">
                {{ formatMoney(payment.total) }}
              </span>
              en
              <span class="font-bold text-primary">
                {{ payment.due_date }}
              </span>
            </div>
          </section>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        :payment="selectedPayment"
        :endpoint="`/loans/${rents.id}/installments/${selectedPayment.installment_id}/pay`"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
      />
    </main>
  </AppLayout>
</template>
