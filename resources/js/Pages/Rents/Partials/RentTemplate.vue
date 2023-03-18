<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";
import PaymentFormModal from "@/Pages/Loans/Partials/PaymentFormModal.vue";

import { formatMoney, formatDate } from "@/utils";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { IRent } from "@/Modules/properties/propertyEntity";

const { openModal: openInvoiceModal } = useToggleModal("invoice");

interface Props {
  rents: IRent;
  currentTab: string;
  hidePanel?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  "": "Detalles",
  payments: "Pagos",
  invoices: "Facturas De Renta",
  deposits: "Depositos",
  expenses: "Gastos",
};

const clientName = computed(
  () => props.rents.client?.names + " " + props.rents.client?.lastnames
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
      <PropertySectionNav />
    </template>

    <main class="p-5 mt-16 md:mt-8">
      <AppSectionHeader
        name="Contrato de Alquiler a"
        class="px-5 bg-white border-2 border-white rounded-md rounded-b-none"
        :resource="rents"
        :title="`${clientName}`"
        hide-action
        @create="router.visit('/loans/create')"
      >
        <template #actions>
          <section class="flex space-x-2">
            <AppButton
              v-if="rents.status !== 'CANCELLED'"
              variant="error"
              @click="
                openInvoiceModal({
                  data: {
                    type: 'expense',
                    clientId: rents.client_id,
                    rentId: rents.id,
                    hideClientOptions: true,
                  },
                  isOpen: true,
                })
              "
            >
              <IMdiBankMinus class="mr-2" />
              Crear Gasto
            </AppButton>
            <AppButton
              v-if="rents.status !== 'CANCELLED'"
              variant="success"
              @click="
                openInvoiceModal({
                  data: {
                    type: 'fee',
                    clientId: rents.client_id,
                    rentId: rents.id,
                    hideClientOptions: true,
                    title: 'Crear cargo de mora',
                  },
                  isOpen: true,
                })
              "
            >
              <IMdiCashPlus class="mr-2" />
              Crear Mora
            </AppButton>
            <AppButton
              variant="neutral"
              @click="router.visit(route('rents.edit', rents))"
            >
              <IMdiEdit />
            </AppButton>
          </section>
        </template>
      </AppSectionHeader>
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white rounded-b-md"
      >
        <div>Alquiler #{{ rents.id }} para {{ clientName }}</div>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-primary/10 text-primary font-bold': tab == currentTab }"
            :href="`/rents/${rents.id}/${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </div>
      <section
        class="flex flex-col md:flex-row w-full md:space-x-8 rounded-t-none border-t-none"
      >
        <article class="w-full space-y-2" :class="[hidePanel ? '' : 'md:w-9/12']">
          <slot />
        </article>

        <article
          v-if="!hidePanel"
          class="w-full md:w-3/12 mt-4 md:mt-0 p-4 space-y-2 rounded-md bg-base-lvl-3"
        >
          <section class="flex space-x-4">
            <AppButton class="w-full" variant="secondary" @click="generateNextInvoice">
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
