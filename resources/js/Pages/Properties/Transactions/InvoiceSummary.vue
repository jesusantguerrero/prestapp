<script lang="ts" setup>
import { reactive, watch, nextTick, ref } from "vue";
// @ts-ignore
import { AtBackgroundIconCard, AtDatePager } from "atmosphere-ui";
import { router } from "@inertiajs/vue3";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";

import { formatMoney } from "@/utils";
import AppButton from "@/Components/shared/AppButton.vue";
import { IInvoice } from "@/Modules/invoicing/entities";
import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { usePrint } from "@/utils/usePrint";
import Simple from "@/Pages/Journal/Invoices/printTemplates/Simple.vue";
import ButtonGroup from "@/Components/ButtonGroup.vue";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import {
  clientInteractions,
  InteractionsState,
} from "@/Modules/clients/clientInteractions";
import { ElMessageBox } from "element-plus";
import { getStatus, getStatusColor, getStatusIcon } from "@/Modules/invoicing/constants";
import { useServerSearch } from "@/utils/useServerSearch";
import { toRefs } from "@vueuse/shared";

const props = defineProps({
  invoices: {
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
  total: {
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
  serverSearchOptions: Object,
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
    bills: `/owners/${invoice.client_id}/draws/${invoice?.id}/payments`,
    invoices: `/rents/${invoice.invoiceable_id}/invoices/${invoice?.id}/payments`,
  };

  const url = urls[filters.section] ?? urls.invoices;

  nextTick(() => {
    openModal({
      data: {
        title: `Pagar ${invoice.concept}`,
        payment: payment,
        endpoint: url,
        due: payment?.amount,
        defaultConcept: "Pago de " + invoice.concept,
        accountsEndpoint: "/invoices",
        hideAccountSelector: true,
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
const isPrinting = ref(false);
function printExternal(invoice: IInvoice) {
  isPrinting.value = invoice.id;
  axios
    .get(`/invoices/${invoice.id}/preview?json=true`)
    .then(({ data }) => {
      selectedInvoice.value = data;
      nextTick(() => {
        customPrint("invoice-content", {
          beforePrint() {
            selectedInvoice.value = null;
          },
          delay: 800,
        });
      });
    })
    .then(() => {
      isPrinting.value = false;
    });
}

const { serverSearchOptions } = toRefs(props);
const { executeSearch, updateSearch, state: pageState } = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    console.log(finalUrl);
    updateSearch(`/units?${finalUrl}`);
  },
  {
    manual: true,
  }
);

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
      <PropertySectionNav>
        <template #actions>
          <AtDatePager
            class="w-full h-12 border-none bg-base-lvl-1 text-body"
            v-model:startDate="pageState.dates.startDate"
            v-model:endDate="pageState.dates.endDate"
            @change="executeSearch()"
            controlsClass="bg-transparent text-body hover:bg-base-lvl-1"
            next-mode="month"
          />
        </template>
      </PropertySectionNav>
    </template>

    <div class="py-10 mx-auto sm:px-6 lg:px-8">
      <section class="flex space-x-4">
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Pagado"
          :value="formatMoney(paid ?? 0)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Balance de Pendiente"
          :value="formatMoney(outstanding ?? 0)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Total del mes"
          :value="formatMoney((outstanding ?? 0) + (paid ?? 0))"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Facturas"
          :value="total || 0"
        />
      </section>
      <section class="flex space-x-4 mt-4">
        <AppSearch
          v-model.lazy="filters.search"
          class="w-full md:flex"
          :has-filters="true"
        />
        <BaseSelect
          class="min-w-max"
          :size="large"
          :options="owners"
          placeholder="Filtrar por dueño"
          v-model="filters.owner"
        />
        <BaseSelect
          placeholder="Filtrar"
          :options="[]"
          v-model="filters.status"
          label="label"
          track-by="name"
        />
      </section>
      <InvoiceTable :invoice-data="invoices" class="mt-10 rounded-md bg-base-lvl-3">
        <template v-slot:actions="{ row }">
          <div class="flex justify-end items-center space-x-2s group">
            <div class="font-bold capitalize text-sm" :class="getStatusColor(row.status)">
              <i :class="getStatusIcon(row.status)" />
              {{ getStatus(row.status) }}
            </div>
            <div class="flex">
              <Link
                class="relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max"
                :href="`/properties/${row.property_id}?unit=${row.id}`"
              >
                <IMdiChevronRight />
              </Link>
              <AppButton
                @click="handlePayment(row)"
                variant="inverse-secondary"
                class="flex items-center justify-center"
                v-if="row?.status !== 'paid' && filters.section !== 'commissions'"
                title="Pagar"
              >
                <IIcSharpPayment />
              </AppButton>
              <div class="flex space-x-2">
                <AppButton
                  class="hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400"
                  variant="neutral"
                  title="Imprimir"
                  :processing="isPrinting == row.id"
                  :disabled="isPrinting == row.id"
                  @click="printExternal(row)"
                >
                  <IMdiFile />
                </AppButton>
                <AppButton
                  v-if="filters.section == 'bills' && row.status != 'paid'"
                  class="mr-2"
                  variant="neutral"
                  :process="InteractionsState.isGeneratingDistribution"
                  @click="
                    clientInteractions.generateOwnerDistribution(row.contact_id, row.id)
                  "
                >
                  Re-generar
                </AppButton>
              </div>
              <AppButton
                variant="neutral"
                class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
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
