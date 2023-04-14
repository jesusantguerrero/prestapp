<script lang="ts" setup>
import { toDate, differenceInDays } from "date-fns";
import { reactive, computed, watch, toRefs } from "vue";
import { parseISO } from "date-fns";

import InvoiceTotals from "../Partials/InvoiceTotals.vue";
import InvoiceGrid from "../Partials/InvoiceGrid.vue";
import ClientCard from "./ClientCard.vue";
import BusinessCard from "./BusinessCard.vue";

import { IClient } from "@/Modules/clients/clientEntity";
import { formatDate } from "@/utils";
import { IInvoice } from "@/Modules/invoicing/entities";

const props = withDefaults(
  defineProps<{
    imageUrl: string;
    type: string;
    user: Record<string, string>;
    businessData: Record<string, string>;
    products?: Record<string, string>[];
    clients?: IClient[];
    invoiceData: IInvoice;
  }>(),
  {
    type: "INVOICE",
    imageUrl: "/logo.png",
  }
);

interface ILineItem {
  quantity: number;
  price: number;
}

const state: any = reactive({
  totalValues: {},
  totals: {
    subtotalField: "subtotal",
    totalField: "amount",
    discountField: "discountTotal",
    subtotalFormula(row: ILineItem) {
      return row.quantity * row.price;
    },
    totalFormula(row: ILineItem) {
      return row.quantity * row.price;
    },
    discountFormula(row: ILineItem) {
      return row.quantity * row.price;
    },
  },
  invoice: {},
  selectedPayment: null,
  isPaymentDialogVisible: false,
  modals: {
    email: {
      value: false,
    },
  },
  tableData: [],
  client: null,
  imageUrl: "",
  dueDays: computed(() => {
    return differenceInDays(state.invoice.due_date, state.invoice.date);
  }),
});

const setInvoiceData = (data: Record<string, any>) => {
  if (data) {
    data.date = toDate(parseISO(data.date) || new Date());
    data.due_date = toDate(parseISO(data.due_date) || new Date());
    state.invoice = data;
    state.client = data.client;
    state.tableData =
      data.lines.sort((a: Record<string, string>, b: Record<string, string>) =>
        a.index > b.index ? 1 : -1
      ) || [];
  }
};

watch(
  () => props.invoiceData,
  (data) => {
    if (data) {
      setInvoiceData(data);
    }
  },
  { immediate: true }
);

const { tableData, client, invoice, totals, totalValues, dueDays } = toRefs(state);
</script>

<template>
  <section class="w-full py-2 rounded-md section">
    <div class="section-body">
      <div class="pt-8 invoice__header">
        <div class="flex w-full invoice-header-details">
          <div class="flex justify-between px-4 w-full space-y-4 invoice-details">
            <section class="flex items-center">
              <div v-if="imageUrl" class="rounded-md">
                <img :src="imageUrl" class="w-96" />
              </div>
              <BusinessCard :business="businessData" class="w-full text-left" />
            </section>

            <div class="w-full text-right">
              <h4 class="px-5 text-primary text-2xl font-bold">
                Factura {{ invoice.series }}-{{ invoice.number }}
              </h4>
              <h5 class="text-md">
                <span class="font-bold">Concepto:</span> {{ invoice.concept }}
              </h5>
              <div>
                <p>
                  <span class="font-bold">Fecha</span>
                  {{ formatDate(invoice.date) }}
                </p>
                <p>
                  <span class="font-bold">Fecha Limite</span>
                  {{ formatDate(invoice.due_date) }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="flex px-4 mt-4 space-x-4">
          <div class="w-8/12 text-left">
            <ClientCard label="Cliente" :client="client" />
          </div>
        </div>
      </div>

      <InvoiceGrid
        :tableData="tableData"
        :is-editing="false"
        :hidden-cols="['quantity', 'discount']"
        class="mt-10 main-grid w-full"
      >
        <template #prepend>
          <div class="text-body-1 font-bold text-center text-lg py-1">
            {{ invoice.concept }} {{ invoice.description }}
          </div>
        </template>
      </InvoiceGrid>

      <div class="flex justify-end px-4 mt-10 text-gray-600">
        <InvoiceTotals
          :tableData="tableData"
          :subtotal-field="totals.subtotalField"
          :discount-field="totals.discountField"
          :payments="invoice.payments"
          :total-values="totalValues"
          :total-field="totals.totalField"
          :subtotalFormula="totals.subtotalFormula"
          :discountFormula="totals.discountFormula"
          :totalFormula="totals.totalFormula"
        />
      </div>

      <div class="flex text-center invoice-footer-details mt-14" v-if="invoice.id">
        <div class="w-full text-gray-600">
          <p class="font-bold text-gray-600">Gracias por su preferencia!</p>
          <div class="mt-5 font-bold text-gray-600" v-if="invoice.type == 'INVOICE'">
            Terminos y condiciones
          </div>
          <div v-if="invoice.type == 'INVOICE'">
            El pago debe ser dentro de {{ dueDays }} dias
          </div>
        </div>

        <section class="w-full text-right justify-center flex flex-col items-end">
          <div class="font-serif invoice__signature w-96 border-b-2 mb-2 mx-auto" />
          <article class="text-center w-full justify-center">
            <div class="font-bold">{{ user?.name }}</div>
            <div>Firma</div>
          </article>
        </section>
      </div>

      <span class="stamp is-approved mt-8" v-if="invoice.debt == 0">Pagado</span>
    </div>
  </section>
</template>

<style lang="scss" scoped>
.totals-container {
  background: white;
  display: flex;
  justify-content: flex-end;
}

.invoice-title {
  padding-left: 15px;
}

.section-body {
  padding: 0 15px;
}

.invoice-actions {
  margin-bottom: 15px;

  .btn {
    height: 38px;
  }

  [class*="col-md"] {
    padding: 0 0 0 0;

    &:first-child {
      padding-left: 15px;
    }

    &:last-child {
      padding-right: 15px;
    }
  }

  .btn,
  button,
  input {
    border-radius: 0 0 0 0 !important;
  }

  .btn-primary {
    background: dodgerblue;
  }
}

.main-grid {
  thead th {
    @apply text-white;
  }
  .el-table__body-wrapper td {
    font-size: 1.5em !important;
  }
}

.invoice-totals {
  &__add-payment {
    width: 100%;
    height: 34px;
    background: dodgerblue;
    color: white;
    border: none;
    font-weight: bolder;
    transition: all ease 0.3s;

    &:hover {
      font-size: 1.01em;
    }

    &:focus {
      outline: none;
    }
  }
}

.invoice-form-row {
  display: inline-grid;
  width: 100%;
  grid-column-gap: 0;
  grid-template-columns: 20% 80%;

  label {
    display: flex;
    align-items: center;
  }
}

.invoice-header-container {
  position: inherit;
}

.invoice-footer-details {
  display: grid;
  grid-template-columns: 300px 1fr;
  grid-column-gap: 15px;
  padding: 0 15px;
}

.el-collapse {
  margin-bottom: 15px;
}

section {
  padding-bottom: 25px;
  background: white;
}
</style>

<style lang="scss">
.main-grid {
  thead th.el-table__cell {
    @apply text-white;
  }
  .el-table__body-wrapper td {
    font-size: 1.2em !important;
  }
}

.stamp {
  transform: rotate(12deg);
  color: #555;
  font-size: 3rem;
  font-weight: 700;
  border: 0.25rem solid #555;
  display: inline-block;
  padding: 0.25rem 1rem;
  text-transform: uppercase;
  border-radius: 1rem;
  font-family: "Courier";
  -webkit-mask-image: url("/grunge.png");
  -webkit-mask-size: 944px 604px;
  mix-blend-mode: multiply;
}

.is-approved {
  color: #0a9928;
  border: 0.5rem solid #0a9928;
  -webkit-mask-position: 13rem 6rem;
  transform: rotate(-14deg);
  font-size: 2.5rem;
  border-radius: 0;
}

.is-draft {
  color: #c4c4c4;
  border: 1rem double #c4c4c4;
  transform: rotate(-5deg);
  font-size: 1.5rem;
  font-family: "Open sans", Helvetica, Arial, sans-serif;
  border-radius: 0;
  padding: 0.5rem;
}

@media print {
  .section-body {
    width: 100%;
    display: block;
    font-size: 12px;
  }
  @page {
    marks: cross;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }

  tbody,
  .client-card {
    font-size: 12px;
  }
} ;
</style>
