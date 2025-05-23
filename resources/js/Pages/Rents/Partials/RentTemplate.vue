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
import { IInvoice } from "@/Modules/invoicing/entities";

const { openModal: openInvoiceModal } = useToggleModal("propertyCharge");

interface Props {
  rents: IRent;
  currentTab: string;
  hidePanel?: boolean;
  allowEdit?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  "": "Detalles",
  payments: "Pagos",
  invoices: "Facturas De Renta",
  deposits: "Depositos",
  notes: "Notas de credito",
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

const isGeneratingInvoices = ref(false);
const generateNextInvoice = () => {
  if (isGeneratingInvoices.value) return;
  isGeneratingInvoices.value = true;
  router.post(
    `/rents/${props.rents.id}/generate-next-invoice`,
    {},
    {
      onSuccess() {
        refresh();
      },
      onFinish() {
        isGeneratingInvoices.value = false;
      },
    }
  );
};

const { openModal } = useToggleModal("invoice");
const onEdit = (invoice: IInvoice) => {
  openModal({
    data: {
      invoiceData: invoice,
    },
    isOpen: true,
  });
};
</script>

<template>
  <AppLayout :title="sectionTitle">
    <template #header>
      <PropertySectionNav />
    </template>

    <main class="px-0 md:p-5 mt-16 md:mt-8">
      <AppSectionHeader
        :name="`Alquiler #${rents.id}: ${clientName}`"
        class="px-5 bg-white border-2 border-white rounded-md rounded-b-none"
        :resource="rents"
        :title="$t(rents.status)"
        hide-action
        @create="router.visit('/loans/create')"
      >
        <template #actions>
          <section
            class="flex flex-col md:justify-end md:flex-row md:space-x-2 mt-4 md:mt-0 w-full space-y-2 md:space-y-0"
          >
            <AppButton
              v-if="rents.status !== 'CANCELLED'"
              variant="error"
              class="w-full md:w-fit"
              @click="
                openInvoiceModal({
                  data: {
                    type: 'expense',
                    clientId: rents.client_id,
                    propertyId: rents.property_id,
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
              <IMdiEdit v-if="allowEdit" />
            </AppButton>
          </section>
        </template>
      </AppSectionHeader>
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white rounded-b-md"
      >
        <header class="flex justify-between"></header>
        <div class="md:flex md:space-x-2 grid grid-cols-2 gap-2">
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
        class="flex flex-col w-full rounded-t-none md:flex-row md:space-x-8 border-t-none"
      >
        <article class="w-full space-y-2" :class="[hidePanel ? '' : 'md:w-8/12']">
          <slot />
        </article>

        <article
          v-if="!hidePanel"
          class="w-full p-4 mt-4 space-y-2 overflow-hidden rounded-md md:w-4/12 md:mt-0 bg-base-lvl-3 h-max invoice-panel"
        >
          <section class="flex space-x-4">
            <AppButton
              class="w-full text-center justify-center"
              variant="secondary"
              v-if="rents.status !== 'CANCELLED'"
              @click="generateNextInvoice"
              :disabled="isGeneratingInvoices"
              :processing="isGeneratingInvoices"
            >
              Generar proximo pago
            </AppButton>
          </section>

          <section class="py-4 mt-8 space-y-2 ic-scroller h-[initial] overflow-y-auto">
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
              :allow-edit="true"
              @edit="onEdit(invoice)"
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

<style lang="scss">
.invoice-panel {
  height: 460px;
  display: grid;
  grid-template-rows: 40px 1fr;
}
</style>
