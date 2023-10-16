<script lang="ts" setup>
import { reactive, watch, nextTick, ref } from "vue";
import { router } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";

import { formatMoney } from "@/utils";
import AppButton from "@/Components/shared/AppButton.vue";
import { IInvoice } from "@/Modules/invoicing/entities";
import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { usePrint } from "@/utils/usePrint";
import Simple from "@/Pages/Journal/Invoices/printTemplates/Index.vue";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import { InteractionsState } from "@/Modules/clients/clientInteractions";
import { ElMessageBox } from "element-plus";
import { getStatus, getStatusColor, getStatusIcon } from "@/Modules/invoicing/constants";
import axios from "axios";
import DropshippingSectionNav from "./Partials/DropshippingSectionNav.vue";

const props = defineProps({
  orders: {
    type: Array,
  },
  type: {
    type: String,
  },
  outstanding: {
    type: Number,
  },
  paid: {
    type: Number,
  },
  lateDays: {
    type: Number,
  },
  properties: {
    type: Array,
    default() {
      return [];
    },
  },
  owners: {
    type: Array,
    default() {
      return [];
    },
  },
  businessData: {
    type: Object,
    required: true,
  },
  user: {
    type: Object,
  },
  section: {
    type: String,
  },
});

interface IFilter {
  [key: string]: null | string | Record<string, string>;
}

const filters = reactive<IFilter>({
  owner: null,
  property: null,
  section: props.section,
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc: Record<string, string | undefined>, [filterName, filter]) => {
        acc[filterName] = filter?.value ?? filter;
        return acc;
      },
      {}
    );

    router.get(
      location.pathname,
      {
        // @ts-ignore
        filters: selectedFilters,
      },
      { preserveState: true }
    );
  },
  { deep: true }
);

const { openModal } = usePaymentModal();

const handlePayment = (invoice: IInvoice) => {
  const payment = {
    ...invoice,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(invoice.debt) || invoice.total,
    id: undefined,
    invoice_id: invoice.id,
  };

  const urls: Record<string, string> = {
    invoices: `/invoices/${invoice?.id}/payment`,
  };

  const url = urls[filters.section] ?? urls.invoices;

  nextTick(() => {
    openModal({
      data: {
        title: `Pagar ${invoice.concept}`,
        payment: payment,
        endpoint: url,
        due: invoice.debt,
        defaultConcept: "Pago de " + invoice.concept,
        accountsEndpoint: "/api/accounts",
      },
    });
  });
};

interface InvoiceResponse {
  invoice: IInvoice;
  businessData: Record<string, string>;
}

const selectedInvoice = ref<InvoiceResponse | null>(null);

const { customPrint } = usePrint();
const isPrinting = ref<number | boolean>(false);
function printExternal(invoice: IInvoice) {
  isPrinting.value = invoice.id;
  axios
    .get(`/invoices/${invoice.id}/preview?json=true`)
    .then(({ data }) => {
      selectedInvoice.value = data;
      nextTick(() => {
        customPrint(
          "invoice-content",
          {
            beforePrint() {
              selectedInvoice.value = null;
            },
            delay: 800,
          },
          `${data.invoice?.concept}-${data.invoice?.client?.fullName}`
        );
      });
    })
    .then(() => {
      isPrinting.value = false;
    });
}

const onDelete = async (invoice: IInvoice) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar la factura ${invoice.concept} por ${formatMoney(
      invoice.total
    )}?`,
    "Eliminar factura"
  );

  if (isValid) {
    router.delete(`/invoices/${invoice.id}`, {
      onSuccess() {
        router.reload({
          preserveState: true,
          preserveScroll: true,
        });
      },
    });
  }
};
</script>

<template>
  <AppLayout title="Centro de pago">
    <template #header>
      <DropshippingSectionNav />
    </template>

    <div class="py-10 mx-auto sm:px-6 lg:px-8 print:hidden">
      <section class="flex justify-end mt-4 space-x-4">
        <section>
          <AppSearch
            v-model.lazy="filters.search"
            class="w-3/12 md:flex"
            :has-filters="true"
          />
        </section>
        <AppButton @click="$inertia.visit(route('dropshipping.invoices.create'))">
          {{ $t("Create invoice") }}
        </AppButton>
      </section>
      <InvoiceTable :invoice-data="orders" class="mt-10 rounded-md bg-base-lvl-3">
        <template v-slot:actions="{ row }">
          <div class="flex items-center justify-end space-x-2s group">
            <div class="text-sm font-bold capitalize" :class="getStatusColor(row.status)">
              <i :class="getStatusIcon(row.status)" />
              {{ getStatus(row.status) }}
            </div>
            <div class="flex">
              <Link
                class="relative inline-block px-5 py-2 ml-4 overflow-hidden font-bold transition rounded-md cursor-pointer hover:bg-primary hover:text-white text-body focus:outline-none hover:bg-opacity-80 min-w-max"
                :href="`/properties/${row.property_id}?unit=${row.id}`"
              >
                <IMdiChevronRight />
              </Link>
              <AppButton
                @click="handlePayment(row)"
                variant="inverse-secondary"
                class="flex items-center justify-center"
                v-if="row?.status !== 'paid'"
                :title="$t('Pay')"
              >
                <IIcSharpPayment />
              </AppButton>
              <div class="flex space-x-2">
                <AppButton
                  class="flex flex-col items-center justify-center transition hover:text-primary hover:border-primary-400"
                  variant="neutral"
                  title="Imprimir"
                  :processing="isPrinting == row.id"
                  :disabled="isPrinting == row.id"
                  @click="printExternal(row)"
                >
                  <IMdiFile />
                </AppButton>
                <AppButton
                  class="mr-2"
                  variant="neutral"
                  :process="InteractionsState.isGeneratingDistribution"
                  @click="route('dropshipping.invoices.edit', row)"
                >
                  {{ $t("edit") }}
                </AppButton>
              </div>
              <AppButton
                variant="neutral"
                class="flex flex-col items-center justify-center transition hover:text-error hover:border-red-400"
                @click="onDelete(row)"
                title="Eliminar"
              >
                <IMdiTrash />
              </AppButton>
            </div>
          </div>
        </template>
      </InvoiceTable>
    </div>
    <div id="invoice-content" v-if="selectedInvoice">
      <Simple
        v-if="selectedInvoice?.invoice"
        :user="user"
        :type="type"
        :imageUrl="$page.props.user.current_team?.profile_photo_url"
        :business-data="selectedInvoice.businessData"
        :invoice-data="selectedInvoice.invoice"
      />
    </div>
  </AppLayout>
</template>

<style lang="scss" scoped>
.body-section {
  background: white;
  padding: 15px;
}

.el-table th {
  font-weight: bolder;
  color: #222 !important;
}

.section-actions {
  display: flex;

  .app-search__container {
    width: 80%;
    margin-right: 15px;
  }

  .action-buttons {
    width: 20%;
    display: flex;

    button {
      margin-left: auto;
    }
  }
}
</style>
