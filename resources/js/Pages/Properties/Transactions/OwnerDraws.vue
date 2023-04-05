<script setup lang="ts">
import { reactive, watch, computed, ref } from "vue";
import { AtBackgroundIconCard } from "atmosphere-ui";
import { Link, router, useForm } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "../Partials/PropertySectionNav.vue";

import { formatMoney, formatDate } from "@/utils";
import BaseTable from "@/Components/shared/BaseTable.vue";
import { ElNotification, TableColumnCtx } from "element-plus";
import AppButton from "@/Components/shared/AppButton.vue";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { IInvoice } from "@/Modules/invoicing/entities";

const props = defineProps({
  invoices: {
    type: Array,
  },
  owner: {
    type: Array,
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
});

const filters = reactive({
  owner: props.owner ? props.owner : null,
});

const sectionLabel = computed(() => {
  return props.owner
    ? `Facturas de ${props.invoices?.at?.(0)?.owner_name}`
    : "Distribucion de propietarios";
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc: Record<string, string | undefined>, [filterName, filter]) => {
        acc[filterName] = filter;
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

interface SummaryMethodProps<T = IInvoice> {
  columns: TableColumnCtx<T>[];
  data: T[];
}

const getSummaries = (param: SummaryMethodProps) => {
  const { columns, data } = param;
  const sums: string[] = [];
  columns.forEach((column, index) => {
    if (index === 1) {
      sums[index] = "Monto a pagar desde cuenta de immobiliaria";
      return;
    }
    const values = data.map((item) => Number(item[column.property]));
    if (!values.every((value) => Number.isNaN(value))) {
      const amount = values.reduce((prev, curr) => {
        const value = Number(curr);
        if (!Number.isNaN(value)) {
          return prev + curr;
        } else {
          return prev;
        }
      }, 0);

      sums[index] = formatMoney(amount);
    } else {
      sums[index] = "";
    }
  });

  return sums;
};

const drawCols = [
  {
    name: "description",
    label: "Descripcion / Cliente",
    width: 400,
  },
  {
    name: "due_date",
    label: "Fecha",
    align: "center",
    class: "text-center",
    width: 120,
    render(row: any) {
      return formatDate(row.due_date);
    },
  },
  {
    name: "property_name",
    label: "Propiedad",
  },

  {
    name: "total",
    label: "Disponible para pago",
    render(row: any) {
      return formatMoney(row.total);
    },
  },
];

const formData = useForm({
  client: null,
  invoices: [] as Record<string, string | number>[],
});

function handleSelection(selectedInvoices: IInvoice[]) {
  formData.invoices = selectedInvoices.map((invoice) => ({
    id: invoice.id,
    total: invoice.total,
  }));
}

function createOwnerDistribution() {
  if (!filters.owner) {
    ElNotification({
      title: "Sin Propietario",
      message: "Por favor seleccione el propietario para generar la factura",
      type: "error",
    });
    return;
  }
  formData.post(`/owners/${filters.owner}/draws`, {
    onSuccess() {
      ElNotification({
        title: "Creada",
        message: "Factura de propiedad creada con exito",
        type: "success",
      });
    },
  });
}

const { openModal: openInvoiceModal } = useToggleModal("invoice");

const emptyLabel = computed(() => {
  return !filters.owner
    ? "Seleccione propietario para ver sus facturas"
    : "No hay facturas para pagar al propietario";
});

const canSubmitForm = computed(() => {
  return filters.owner && !formData.processing && formData.invoices.length;
});

const isLoading = ref(false);
router.on("start", (event) => {
  isLoading.value = true;
});

router.on("finish", () => {
  isLoading.value = false;
});
</script>

<template>
  <AppLayout :title="sectionLabel">
    <template #title>
      <header class="flex space-x-2 px-4 py-2 items-center">
        <AppButton
          variant="neutral"
          @click="filters.owner = null"
          v-if="owner"
          :processing="isLoading"
          :disabled="isLoading"
        >
          <IMdiChevronLeft />
        </AppButton>
        <h4 class="font-bold text-xl text-body-1">
          {{ sectionLabel }}
        </h4>
      </header>
    </template>
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <section class="flex justify-end px-4 py-2 space-x-2">
            <AppButton
              variant="error"
              @click="
                !formData.processing &&
                  openInvoiceModal({
                    data: {},
                    isOpen: true,
                  })
              "
              :disabled="formData.processing"
            >
              <IMdiBankMinus class="mr-2" />
              Crear Gasto
            </AppButton>
            <AppButton
              variant="secondary"
              @click="createOwnerDistribution"
              :processing="formData.processing"
              :disabled="!canSubmitForm"
              :title="
                !canSubmitForm && 'Debe seleccionar facturas para aplicar la distribucion'
              "
            >
              Crear Factura de distribucion ({{ formData.invoices.length }})
            </AppButton>
          </section>
        </template>
      </PropertySectionNav>
    </template>

    <div class="mt-16 mx-auto sm:px-6 lg:px-8">
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
          title="Dias de mora"
          :value="lateDays || 0"
        />
      </section>

      <div class="mt-4 bg-base-lvl-3 rounded-md overflow-hidden">
        <AppFormField label="Propietario" v-if="!owner && !isLoading">
          <section
            v-for="owner in invoices"
            @click="filters.owner = owner.owner_id"
            class="flex justify-between group mb-2 cursor-pointer transition-colors items-center px-2 hover:bg-primary/20"
          >
            <h4>{{ owner.owner_name }} ({{ owner.totalMonths }}/ {{ owner.total }})</h4>
            <button class="group-hover:text-primary h-10">
              <IMdiChevronRight />
            </button>
          </section>
        </AppFormField>

        <template v-if="invoices?.length && owner && !isLoading">
          <section v-for="invoiceInMonth in invoices">
            <header class="bg-primary text-center py-2 text-white capitalize">
              {{ formatDate(invoiceInMonth?.monthName, "MMMM") }}
              {{ formatDate(invoiceInMonth?.date, "yyyy") }} ({{
                invoiceInMonth.invoices.length
              }})
            </header>
            <BaseTable
              class="mt-0"
              table-class="px-0"
              show-summary
              selectable
              @selection-change="handleSelection"
              :summary-method="getSummaries"
              :cols="drawCols"
              :table-data="invoiceInMonth.invoices"
            >
              <template v-slot:description="{ scope: { row } }">
                <section>
                  <p>
                    <Link
                      :href="`/${row.type == 'INVOICE' ? 'invoices' : 'bills'}/${row.id}`"
                      class="text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-sm"
                    >
                      {{ row.description }}
                      <span class="font-bold text-gray-300">
                        {{ row.series }} #{{ row.number }}
                      </span>
                    </Link>
                  </p>
                  <p>
                    <Link
                      class="text-sm text-body-1 mt-2"
                      :href="`/clients/${row.client_id || row.contact_id}`"
                    >
                      <i class="fa fa-user text-xs" />
                      {{ row.owner_name }}
                    </Link>
                  </p>
                </section>
              </template>
            </BaseTable>
          </section>
          <footer class="flex justify-end px-4 py-2 space-x-2">
            <AppButton
              variant="error"
              @click="
                !formData.processing &&
                  openInvoiceModal({
                    data: {},
                    isOpen: true,
                  })
              "
              :disabled="formData.processing"
            >
              <IMdiBankMinus class="mr-2" />
              Crear Gasto
            </AppButton>
            <AppButton
              variant="secondary"
              @click="createOwnerDistribution"
              :processing="formData.processing"
              :disabled="!canSubmitForm"
            >
              Crear Factura de distribucion
            </AppButton>
          </footer>
        </template>

        <p class="h-48 flex items-center justify-center" v-else-if="!isLoading">
          {{ emptyLabel }}
        </p>
        <section class="py-4 px-2" v-if="isLoading">
          <ElSkeleton :rows="5" animated />
        </section>
      </div>
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
