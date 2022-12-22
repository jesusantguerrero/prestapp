<template>
  <section class="w-full py-2 rounded-md section">
    <div class="section-body">
        <div class="pt-8 invoice__header">
            <div class="flex w-full invoice-header-details" :class="[imageUrl? 'justify-between' : '']">
                <div v-if="imageUrl" class="bg-gray-500 rounded-md avatar-uploader w-28 h-28">
                    <img  :src="imageUrl" class="avatar" />
                </div>

                <div class="flex justify-between w-full space-y-4 text-right invoice-details">
                    <h4 class="w-full px-5 text-2xl font-bold text-left">
                        INVOICE {{ invoice.series}}-{{ invoice.number}}
                    </h4>
                    <div class="w-full">
                        <h5 class="text-md">
                            <span class="font-bold">Concept:</span>  {{ invoice.concept }}
                        </h5>
                        <div>
                            <p><span class="font-bold">Invoice Date</span> {{ formatDate(invoice.date)}}</p>
                            <p><span class="font-bold">Due Date</span> {{ formatDate(invoice.due_date)}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex px-4 mt-4 space-x-4">
                <div class="w-8/12 text-left">
                    <div class="">
                        <p class="font-bold">Billing To: </p>
                        <div> {{ client.fullName }} <span v-if="client.phone">- {{ client.phone || '000-000-0000' }}</span> </div>
                        <div v-if="client.address || client.address_details"> {{ client.address || client.address_details }}</div>
                        <div v-if="client.email"> {{ client.email }}</div>
                    </div>
                </div>
                <div class="w-8/12 text-left">
                    <div class="">
                        <p class="font-bold">From: </p>
                        <div> {{ businessData.business_name }} </div>
                        <div v-if="businessAddress"> {{ businessAddress }}</div>
                    </div>
                    <div class="mt-5">
                        <p class="font-bold">Total due: </p>
                        <div> {{ formatMoney(invoice.debt, 'USD') }} </div>
                    </div>
                </div>
            </div>
        </div>

        <invoice-grid
            :tableData="tableData"
            :products="products"
            :is-editing="false"
            class="px-4 mt-10"
        />

      <div class="flex justify-end px-4 mt-10 text-gray-600">
        <invoice-totals
          :tableData="tableData"
          :subtotal-field="totals.subtotalField"
          :discount-field="totals.discountField"
          :payments="invoice.payments"
          :total-values="totalValues"
          :total-field="totals.totalField"
          :subtotalFormula="totals.subtotalFormula"
          :discountFormula="totals.discountFormula"
          :totalFormula="totals.totalFormula"
          @edit-payment="editPayment"
        />
      </div>

      <div class="flex text-left invoice-footer-details mt-14" v-if="invoice.id">
        <div class="w-full text-gray-600">
           <p class="font-bold text-gray-600">
            Thanks For your business!
           </p>
          <div class="mt-5 font-bold text-gray-600">Terms and conditions</div>
          <div> Payment is due within {{ dueDays}} days </div>
        </div>

        <div class="w-full text-right" v-if="showSign">
            <div class="font-serif invoice__firm">Jesus Guerrero</div>
            <div class="font-bold"> Jesus Antonio Guerrero Alvarez</div>
            <div> Software Engineer</div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { format, toDate, differenceInDays } from "date-fns";
import { reactive, computed, watch, toRefs } from "vue";
import { formatMoney } from "@/utils";
import parseISO from 'date-fns/esm/fp/parseISO/index.js';

import InvoiceTotals from "../Partials/InvoiceTotals.vue";
import InvoiceGrid from "../Partials/InvoiceGrid.vue";

const defaultInvoice = {
  concept: "Invoice",
  lineItems: []
};

const props = defineProps({
    type: {
      type: String,
      default: "INVOICE"
    },
    user: Object,
    businessData: Object,
    products: Array,
    clients: Array,
    invoiceData: [Object, null]
})

const state = reactive({
      totalValues: {},
      totals: {
        subtotalField: "subtotal",
        totalField: "amount",
        discountField: "discountTotal",
        subtotalFormula(row) {
          return row.quantity * row.price;
        },
        totalFormula(row) {
          return row.quantity * row.price;
        },
        discountFormula(row) {
          return row.quantity * row.price;
        }
      },
      invoice: {},
      selectedPayment: null,
      isPaymentDialogVisible: false,
      modals: {
        email: {
          value: false
        }
      },
      tableData: [],
      client: null,
      imageUrl: '',
      dueDays: computed(() => {
          return differenceInDays(state.invoice.due_date, state.invoice.date);
      }),
      businessAddress: computed(() => {
          return `${props.businessData.business_street}, ${props.businessData.business_city}, ${props.businessData.business_state}, ${props.businessData.business_country}`;
      })
  });

const formatDate = (date) => {
    return format(date, 'MMM dd, yyyy')
}

const setInvoiceData = (data) => {
    if (data) {
        data.date = toDate(parseISO(data.date) || new Date());
        data.due_date = toDate(parseISO(data.due_date) || new Date());
        state.invoice = data;
        state.client = data.client;
        state.tableData = data.lines.sort((a, b) => (a.index > b.index ? 1 : -1)) || [];
    }
}

watch(
    () => props.invoiceData,
    () => {
        setInvoiceData(props.invoiceData);
    },
    { immediate: true}
);

const { tableData, client, invoice, totals, totalValues, dueDays, businessAddress } = toRefs(state);
</script>

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

  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
