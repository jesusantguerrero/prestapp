<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import { formatMoney, formatDate } from "@/utils";
import { ILoanInstallment } from "../../Modules/loans/loanInstallmentEntity";
import PropertySectionNav from "../Properties/Partials/PropertySectionNav.vue";
import { AtButton } from "atmosphere-ui";
import InvoiceCard from "../../Components/templates/InvoiceCard.vue";
import PaymentFormModal from "../Loans/Partials/PaymentFormModal.vue";

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

const clientName = computed(
  () => props.rents.client.names + " " + props.rents.client?.lastnames
);
const sectionTitle = computed(() => `${clientName.value.split(",")[0]}`);

type IPaymentMetaData = ILoanInstallment & {
  invoice_id?: number;
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);

const onPayment = (invoice: ILoanInstallment) => {
  selectedPayment.value = {
    ...invoice,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(invoice.debt) || invoice.total,
    id: undefined,
    invoice_id: invoice.id,
  };

  isPaymentModalOpen.value = true;
};

const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.rents.id} pago #${selectedPayment.value.installment_id}`
  );
});

const linkToPrint = ref("");
const invoiceLink = ref();

const onDownload = (invoice) => {
  linkToPrint.value = `/invoices/${invoice.id}/print`;
  console.log(linkToPrint.value, invoice);
  nextTick(() => {
    invoiceLink.value.click();
    linkToPrint.value = "";
  });
};

const handleActions = (actionName, invoice) => {
  switch (actionName) {
    case "payment":
      onPayment(invoice);
      break;
    case "download":
      onDownload(invoice);
      break;
  }
  if (actionName == "payment") {
  }
};

const refresh = () => {
  router.reload();
};

const generateNextInvoice = () => {
  router.post(`/rents/${props.rents.id}/generate-next-invoice`, {
    onSuccess() {
      refresh();
    },
  });
};
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton variant="inverse" @click="router.visit(route('rents.create'))">
            Crear Gasto
          </AppButton>
          <AppButton variant="inverse" @click="router.visit(route('rents.create'))">
            Crear Mora
          </AppButton>
          <AppButton variant="inverse" @click="router.visit(route('rents.create'))">
            Crear Cargo Extra
          </AppButton>
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
        <div>Alquiler #{{ rents.id }} para {{ clientName }}</div>
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
      <section class="flex w-full space-x-8 rounded-t-none border-t-none">
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
          <AtButton class="hover:bg-base-lvl-1" rounded @click="generarDeposito()">
            Deposito {{ formatMoney(rents.deposit) }}
          </AtButton>
        </article>

        <article class="w-3/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <section class="flex space-x-4">
            <AppButton class="w-full" @click="generateNextInvoice">
              Generar proximo pago
            </AppButton>
          </section>

          <section class="py-4 mt-8 space-y-2">
            <div class="text-sm" v-if="rents.transaction">
              {{ rents.transaction.description }}
              <span class="font-bold text-green-500">
                {{ formatMoney(rents.transaction.total) }}
              </span>
              en
              <span class="font-bold text-primary">
                {{ formatDate(rents.date) }}
              </span>
            </div>
            <InvoiceCard
              v-for="invoice in rents.invoices"
              :invoice="invoice"
              :actions="{
                payment: {
                  label: 'Registrar Pago',
                },
                send: {
                  label: 'Enviar Correo',
                },
                download: {
                  label: 'Descargar PDF',
                },
                view: {
                  label: 'Ver factura',
                },
                delete: {
                  label: 'Eliminar Factura',
                },
              }"
              @action="handleActions($event, invoice)"
            />
          </section>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        title="Pagar Renta"
        :payment="selectedPayment"
        :endpoint="`/rents/${rents.id}/invoices/${selectedPayment.invoice_id}/pay`"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
      />

      <a :href="linkToPrint" download target="_blank" ref="invoiceLink" type="hidden"></a>
    </main>
  </AppLayout>
</template>
