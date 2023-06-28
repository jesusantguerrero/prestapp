<script lang="ts" setup>
import { reactive, watch, nextTick, ref, computed } from "vue";
// @ts-ignore
import { AtBackgroundIconCard, AtDatePager } from "atmosphere-ui";
import { Link, router, useForm } from "@inertiajs/vue3";
import { toRefs } from "@vueuse/shared";
import { useI18n } from "vue-i18n";
import { ElMessageBox, ElTag } from "element-plus";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoiceTable from "@/Components/templates/InvoiceTable";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import Simple from "@/Pages/Journal/Invoices/printTemplates/Simple.vue";
import AppSearch from "@/Components/shared/AppSearch/AppSearch.vue";
import SectionNav from "@/Components/SectionNav.vue";

import { formatDate, formatMoney } from "@/utils";
import { IInvoice } from "@/Modules/invoicing/entities";
import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { usePrint } from "@/utils/usePrint";
import {
  clientInteractions,
  InteractionsState,
} from "@/Modules/clients/clientInteractions";
import { getStatus, getStatusColor, getStatusIcon } from "@/Modules/invoicing/constants";
import { useServerSearch } from "@/utils/useServerSearch";
import { getRentStatusColor } from "@/Modules/properties/constants";
import { useResponsive } from "@/utils/useResponsive";
import { format } from "date-fns";
import { es } from "date-fns/locale";

const props = defineProps({
  invoices: {
    type: Object,
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

  const url = `/rents/${invoice.invoiceable_id}/invoices/${invoice?.id}/payments`;

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
const { executeSearchWithDelay, updateSearch, state: pageState } = useServerSearch(
  serverSearchOptions,
  (finalUrl: string) => {
    console.log(finalUrl);
    updateSearch(`/rent-reports/monthly-summary?${finalUrl}`);
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

const { t } = useI18n();
const invoiceTypes = computed(() => Object.keys(props.invoices));
const selectedTab = ref(invoiceTypes.value[0]);
const tabs = computed(() =>
  invoiceTypes.value.reduce((tabs: Record<string, any>, invoiceType: string) => {
    tabs[invoiceType] = {
      label: t(invoiceType),
    };
    return tabs;
  }, {})
);

const sectionLabel = computed(() => {
  return "Reporte rentas de " + formatDate(pageState?.dates?.startDate, "MMMM");
});

const deletePaymentForm = useForm({});
const deleteRentPayments = async (invoice: IInvoice) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar los pagos de factura de ${invoice.client_name}?`,
    "Eliminar pago de factura"
  );
  if (isValid) {
    deletePaymentForm.delete(
      `/rents/${invoice.invoiceable_id}/invoices/${invoice.id}/payments`,
      {
        onSuccess() {
          router.reload();
        },
      }
    );
  }
};

const isLoading = ref(false);
router.on("start", (event) => {
  if (event.detail.visit.method !== "delete") {
    isLoading.value = true;
  }
});

router.on("finish", () => {
  isLoading.value = false;
});

const { isMobile } = useResponsive();

const selectedMonthName = computed(() => {
  try {
    return format(pageState.dates.startDate, "MMMM, yyyy", {
      locale: es,
    });
  } catch (err) {
    return pageState.dates.startDate;
  }
});
</script>

<template>
  <AppLayout :title="sectionLabel">
    <template #title v-if="isMobile">
      <AtDatePager
        class="h-12 border-none bg-base-lvl-1 text-body ml-4 w-44"
        v-model:startDate="pageState.dates.startDate"
        v-model:endDate="pageState.dates.endDate"
        @change="executeSearchWithDelay()"
        controlsClass="bg-transparent text-body hover:bg-base-lvl-1"
        next-mode="month"
      >
        <span class="capitalize"> {{ selectedMonthName }} </span>
      </AtDatePager>
    </template>
    <template #header>
      <PropertySectionNav>
        <template #actions v-if="!isMobile">
          <AtDatePager
            class="w-full h-12 border-none bg-base-lvl-1 text-body"
            v-model:startDate="pageState.dates.startDate"
            v-model:endDate="pageState.dates.endDate"
            @change="executeSearchWithDelay()"
            controlsClass="bg-transparent text-body hover:bg-base-lvl-1"
            next-mode="month"
          />
        </template>
      </PropertySectionNav>
    </template>

    <div class="pt-16 md:py-10 mx-auto sm:px-6 lg:px-8 print:hidden">
      <section class="grid grid-cols-2 gap-2 md:flex md:space-x-4 general-stats">
        <AtBackgroundIconCard
          class="w-full bg-white border md:h-28 text-body-1"
          title="Pagado"
          :value="formatMoney(paid ?? 0)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border md:h-28 text-body-1"
          title="Pendiente"
          :value="formatMoney(outstanding ?? 0)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border md:h-28 text-body-1"
          title="Total del mes"
          :value="formatMoney((outstanding ?? 0) + (paid ?? 0))"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border md:h-28 text-body-1"
          title="Facturas"
          :value="total || 0"
        />
      </section>
      <SectionNav
        class="bg-base-lvl-3 w-full mt-4"
        selected-class="border-primary font-bold text-primary"
        v-model="selectedTab"
        :sections="tabs"
      >
        <template v-slot:title="{ tab, tabName }">
          <h4 class="capitalize text-primary font-bold">
            {{ tab.label }} ({{ invoices[tabName].total }})
          </h4>
        </template>
        <template #actions>
          <section class="flex space-x-4" v-if="false">
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
        </template>
      </SectionNav>
      <section v-for="(invoiceGroup, groupName) in invoices" class="px-4 bg-base-lvl-3">
        <article
          v-for="(ownerInvoices, ownerName) in invoiceGroup.owners"
          :key="ownerName"
          class="mb-5"
        >
          <header class="py-2 font-bold">
            {{
              $t(":owner, (:invoiceCount) invoices", {
                owner: ownerName,
                invoiceCount: ownerInvoices.length,
              })
            }}
          </header>
          <InvoiceTable
            v-if="groupName == selectedTab"
            :invoice-data="ownerInvoices"
            :is-loading="isLoading"
            class="rounded-md bg-base-lvl-3 mt-0"
            :responsive-actions="{
              payment: handlePayment,
              download: printExternal,
              delete: onDelete,
            }"
          >
            <template v-slot:concept="{ row }">
              <section v-if="!isLoading">
                <p>
                  <Link
                    :href="`/${row.type == 'INVOICE' ? 'invoices' : 'bills'}/${row.id}`"
                    class="text-blue-400 inline-flex capitalize border-b justify-between border-blue-400 border-dashed cursor-pointer text-sm"
                    :title="row.description"
                  >
                    <section>
                      {{ row.concept }}
                      <span class="font-bold text-gray-300">
                        {{ row.series }} #{{ row.number }}
                      </span>
                    </section>
                  </Link>
                </p>
                <p class="flex items-center mt-2">
                  <IClarityContractLine class="mr-2" />
                  {{ row.client_name }}
                  <ElTag
                    :type="getRentStatusColor(row.rent_status)"
                    class="ml-2"
                    v-if="row.rent_status"
                  >
                    {{ $t(row.rent_status ?? "") }} {{ row.move_out_at }}
                  </ElTag>
                </p>
              </section>
              <ElSkeleton :rows="1" animated v-else />
            </template>
            <template v-slot:actions="{ row }">
              <div class="flex justify-end items-center space-x-2s group">
                <div
                  class="font-bold capitalize text-sm"
                  :class="getStatusColor(row.status)"
                >
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
                  <AppButton
                    @click="deleteRentPayments(row)"
                    :disabled="deletePaymentForm.processing"
                    variant="error"
                    class="flex items-center justify-center"
                    v-else
                    title="Eliminar pago"
                  >
                    <IMdiReceiptTextRemove />
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
                        clientInteractions.generateOwnerDistribution(
                          row.contact_id,
                          row.id
                        )
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
        </article>
      </section>
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

<style lang="scss">
@media (max-width: 1024px) {
  .general-stats .text-3xl {
    font-size: 1em;
  }
}
</style>
