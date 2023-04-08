<script lang="ts" setup>
import { format as formatDate } from "date-fns";
import parseISO from "date-fns/esm/fp/parseISO/index.js";
import { useForm, router } from "@inertiajs/vue3";
import { AtInput, AtField, AtFieldCheck, AtSelect, AtSimpleSelect } from "atmosphere-ui";
import { computed, reactive, toRefs, watch, inject, toRaw } from "vue";

import AppButton from "@/Components/shared/AppButton.vue";
import InvoiceTotals from "./InvoiceTotals.vue";
import InvoiceGrid from "./InvoiceGrid.vue";

import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { ElCollapse, ElCollapseItem, ElMessageBox, ElNotification } from "element-plus";
import { IPayment } from "@/Modules/loans/loanEntity";

const props = defineProps({
  type: {
    type: String,
    default: "INVOICE",
  },
  user: Object,
  products: Array,
  clients: Array,
  invoiceData: [Object, null],
  availableTaxes: [Array, null],
  isEditing: Boolean,
});

const labels = {
  expense: {
    contact: "Proveedor",
    documentNumber: "Bill Number",
    orderNumber: "P.O/S.O Number",
  },
  invoice: {
    contact: "Cliente",
    documentNumber: "No. factura",
    orderNumber: "No. Orden",
  },
};

const getLabel = (key: string) => labels[props.type.toLowerCase()][key];

const state: any = reactive({
  totalValues: {},
  totals: {
    subtotalField: "subtotal",
    totalField: "amount",
    discountField: "discountTotal",
    subtotalFormula(row: Record<string, number>) {
      return row.quantity * row.price;
    },
    totalFormula(row: Record<string, number>) {
      return row.quantity * row.price;
    },
    discountFormula(row: Record<string, number>) {
      return row.quantity * row.price;
    },
  },
  invoice: useForm({
    id: null,
    number: null,
    concept: null,
    date: new Date(),
    due_date: new Date(),
    client_id: null,
    footer: null,
    notes: null,
    payments: [],
    debt: 0,
    status: "DRAFT",
    created_at: null,
    updated_at: null,
    taxes_included: false,
  }),
  selectedPayment: null,
  isPaymentDialogVisible: false,
  modals: {
    email: {
      value: false,
    },
  },
  activeSections: [],
  tableData: [],
  client: null,
  imageUrl: "",
  isDraft: computed(() => {
    return !state.invoice.status || state.invoice.status.toLowerCase() == "draft";
  }),
  section: computed(() => {
    return props.type.toLowerCase();
  }),
});

const setInvoiceData = (data: Record<string, any>) => {
  if (data) {
    data.date = parseISO(data.date) || new Date();
    data.due_date = parseISO(data.due_date) || new Date();

    Object.keys(data).forEach((key) => {
      state.invoice[key] = data[key];
    });

    state.client = data.client;
    state.tableData =
      data.lines
        .sort((a, b) => (a.index > b.index ? 1 : -1))
        .map((line) => {
          const itemTaxes = line.taxes?.length ? toRaw(line.taxes) : [];
          line.taxes = [...itemTaxes, { id: "new" }];
          return line;
        }) || [];
  }
};

watch(
  () => props.invoiceData,
  (invoiceData) => {
    setInvoiceData(invoiceData);
  },
  { immediate: true }
);

const reload = () => {
  setTimeout(() => {
    router.reload();
  }, 2000);
};

const { openModal } = usePaymentModal();
const editPayment = (payment: IPayment) => {
  openModal({
    data: {
      title: `Pagar ${invoice.concept}`,
      payment: payment,
      endpoint: `/invoices/${payment.payable_id}/payment/${payment.id}`,
      due: payment.amount,
      account_id: payment.account_id,
      defaultConcept: payment.concept,
    },
  });
};

const removePayment = async (payment: Record<string, string>) => {
  const isConfirmed = await ElMessageBox.confirm(
    `Estas seguro de eliminar el pago ${payment.concept}?`,
    "Eliminar pago"
  );

  if (!isConfirmed) return;
  router.delete(`/payments/${payment.id}`, {
    onSuccess() {
      ElNotification({
        message: `Pago ${payment.due_date} borrada con exito`,
        title: "Pago eliminada",
        type: "success",
      });
    },
  });
};

const setRequestData = (data: Record<string, any>): Record<string, any> => {
  const requestData = {
    ...data,
    items: state.tableData
      .map((item, index) => {
        item.index = index;
        item.quantity = parseFloat(item.quantity);
        item.price = parseFloat(item.price);
        return item;
      })
      .filter((item) => item.concept),
  };
  requestData.date = formatDate(data.date || new Date(), "yyyy-MM-dd");
  requestData.due_date = formatDate(data.due_date || requestData.date, "yyyy-MM-dd");

  const type = props.type != "INVOICE" ? "EXPENSE" : props.type;
  requestData.resource_type_id = type;
  requestData.type = type;
  requestData.concept = requestData.concept || state.section;

  requestData.total = state.totalValues.total;
  requestData.discount = state.totalValues.discountTotal;
  requestData.subtotal = state.totalValues.subtotal;
  requestData.taxes = state.totalValues.taxes;
  delete requestData.lines;
  delete requestData.paymentDocs;
  delete requestData.client;

  return requestData;
};

const sendRequest = (
  method: string,
  url: string,
  formData: Record<string, any>,
  message: string
) => {
  return router[method](url, formData, {
    onSuccess() {
      const section = formData.type == "EXPENSE" ? "bills" : "invoices";
      router.replace(`/${section}/${formData.id}`);
    },
  });
};

const saveForm = (status: number) => {
  const formData = setRequestData({
    ...state.invoice,
    type: props.type,
  });
  if (status) {
    formData.status = 2;
  }
  let message = "Invoice created";
  let method = "post";
  let url = `/invoices`;

  if (state.invoice.id) {
    url = `/invoices/${state.invoice.id}`;
    message = "Invoice updated";
    method = "put";
  }

  sendRequest(method, url, formData, message);
};

const markAsPaid = () => {
  const formData = setRequestData(state.invoice);
  formData.status = 1;
  sendRequest(
    "post",
    `/invoices/${state.invoice.id}/mark-as-paid`,
    formData,
    "Invoice marked as paid"
  );
};

const cloneInvoice = (status: number) => {
  const formData = setRequestData(state.invoice);
  if (status) {
    formData.status = 2;
  }

  let message = "Invoice created";
  let method = "post";
  let url = `/invoices/${state.invoice.id}/clone`;

  sendRequest(method, url, formData, message)
    .then((invoice) => {
      getInvoice(invoice.id);
    })
    .catch((err) => {
      console.log(err);
    });
};

const handleImageChange = (file: File) => {
  state.imageUrl = URL.createObjectURL(file.raw);
  state.invoice.logo = file;
};

const onTaxesUpdated = ({ rowIndex, taxes }: { rowIndex: number; taxes: any[] }) => {
  state.tableData[rowIndex].taxes = taxes;
};

const {
  invoice,
  totals,
  tableData,
  totalValues,
  isPaymentDialogVisible,
  isDraft,
} = toRefs(state);

defineExpose({
  saveForm,
  cloneInvoice,
});

const accountsOptions = inject("accountsOptions", []);
</script>

<template>
  <section class="w-full py-2 rounded-md section">
    <div class="section-body">
      <div class="invoice-body">
        <ElCollapse v-model="activeSections" class="w-full">
          <ElCollapseItem title="Logo, concept and description" name="header">
            <div class="invoice-header-details">
              <el-upload
                class="avatar-uploader"
                :v-model="invoice.logo"
                :show-file-list="false"
                :on-change="handleImageChange"
                :auto-upload="false"
              >
                <img v-if="imageUrl" :src="imageUrl" class="avatar" />
                <i v-else class="el-icon-plus avatar-uploader-icon"></i>
              </el-upload>

              <div class="space-y-4 invoice-details">
                <div class="invoice-form-row form-group">
                  <label for="invoice-description">Description: </label>
                  <at-input
                    type="text"
                    class="form-control"
                    name="invoice-description"
                    id="invoice-description"
                    v-model="invoice.description"
                  />
                </div>
                <div class="invoice-form-row form-group">
                  <label for="invoice-description">Concept: </label>
                  <at-input
                    type="text"
                    class="form-control"
                    name="invoice-description"
                    id="invoice-description"
                    v-model="invoice.concept"
                  />
                </div>
              </div>
            </div>
          </ElCollapseItem>
        </ElCollapse>

        <div class="flex space-x-4">
          <div class="w-6/12 text-left">
            <AtField :label="getLabel('contact')">
              <AtSelect
                v-model="invoice.client_id"
                v-model:selected="state.client"
                :options="clients"
                label="fullName"
                key-track="id"
              />
            </AtField>
            <div v-if="state.client">
              <p>
                {{ state.client.fullName }}
              </p>
              <p v-if="state.client.country">{{ state.client.country }}</p>
              <p v-if="state.client.tax_number">
                <strong>CIF/NIF:</strong> <span>{{ state.client.tax_number }}</span>
              </p>
              <p>
                {{ state.client.email }}
              </p>
            </div>
            <AtField label="Cuenta" class="w-4/12">
              <AtSimpleSelect
                v-model="invoice.account_id"
                v-model:selected="invoice.account"
                size="large"
                :default-expand-all="true"
                :options="accountsOptions"
                label="name"
                key-track="id"
              />
            </AtField>
          </div>

          <div class="flex justify-between w-6/12 space-x-4 text-left">
            <div class="w-full">
              <AtField label="Fecha" class="flex flex-col">
                <ElDatePicker
                  v-if="invoice.date"
                  v-model="invoice.date"
                  size="large"
                  id="invoice-date"
                  type="date"
                  title="seleccione una fecha"
                  placeholder="selecciona una fecha"
                />
              </AtField>

              <AtField label="Fecha Limite" class="mt-2">
                <ElDatePicker
                  v-if="invoice.due_date"
                  v-model="invoice.due_date"
                  size="large"
                  id="invoice-due-date"
                  type="date"
                  title="seleccione una fecha"
                  placeholder="selecciona una fecha"
                />
              </AtField>
            </div>

            <div class="w-full">
              <AtField :label="getLabel('documentNumber')">
                <AtInput
                  v-model="invoice.number"
                  type="text"
                  name="invoice-number"
                  id="invoice-number"
                />
              </AtField>
              <AtField :label="getLabel('orderNumber')" class="mt-2">
                <AtInput
                  v-model="invoice.order_number"
                  type="text"
                  name="invoice-order-number"
                  id="invoice-order-number"
                />
              </AtField>
            </div>
          </div>
        </div>
      </div>

      <InvoiceGrid
        :tableData="tableData"
        :products="products"
        :taxes="taxes"
        :is-editing="isEditing"
        :available-taxes="availableTaxes"
        @taxes-updated="onTaxesUpdated"
        class="mt-10"
      />
      <div class="totals-container">
        <div>
          <AtFieldCheck label="Taxes included" v-model="invoice.taxes_included" />
        </div>
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
          :invoice-taxes="invoiceTaxes"
          :is-tax-included="invoice.taxes_included"
          @edit-payment="editPayment"
          @delete-payment="removePayment"
        >
          <template v-slot:add-payment v-if="!state.isDraft">
            <div>
              <AppButton class="w-full" @click="isPaymentDialogVisible = true">
                Add Payment
              </AppButton>
            </div>
          </template>
        </InvoiceTotals>
      </div>
      <div class="flex text-left invoice-footer-details" v-if="invoice.id">
        <div class="w-full">
          <label for=""> Footer </label>
          <textarea
            name=""
            class="w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
            id=""
            cols="30"
            rows="5"
            v-model="invoice.footer"
          ></textarea>
        </div>

        <div class="w-full">
          <label for="" class="block"> Notes </label>
          <textarea
            name=""
            class="w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400"
            id=""
            cols="30"
            rows="5"
            v-model="invoice.notes"
          ></textarea>
        </div>
      </div>
    </div>
  </section>
</template>

<style lang="scss" scoped>
.totals-container {
  background: white;
  display: flex;
  justify-content: flex-end;
  margin-right: 20px;
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

.invoice-header-details,
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
