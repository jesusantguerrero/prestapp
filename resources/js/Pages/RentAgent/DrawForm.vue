<script setup lang="ts">
import { reactive, watch, computed, ref, h } from "vue";
// @ts-expect-error:  no types definitions
import { AtBackgroundIconCard } from "atmosphere-ui";
import { Link, router, useForm } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import DrawSectionNav from "./Partials/AgentSectionNav.vue";

import { formatMoney, formatDate } from "@/utils";
import BaseTable from "@/Components/shared/BaseTable.vue";
import { ElMessageBox, ElNotification, TableColumnCtx } from "element-plus";
import AppButton from "@/Components/shared/AppButton.vue";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { IInvoice } from "@/Modules/invoicing/entities";
import InvoiceCard from "@/Components/templates/InvoiceCard.vue";

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
        // @ts-ignore
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
  description: "",
});

function handleSelection(selectedInvoices: IInvoice[]) {
  formData.invoices = selectedInvoices.map((invoice) => ({
    id: invoice.id,
    due_date: invoice.due_date,
    total: invoice.total,
  }));
}

const isSelected = (invoiceId: number) => {
  return formData.invoices.map((invoice) => invoice.id == invoiceId);
};

const selectedItems = ref<Record<number, boolean | IInvoice>>({});
const toggleSelection = (rows: IInvoice[]) => {
  rows.map((invoice) => {
    if (!selectedItems.value[invoice.id]) {
      selectedItems.value[invoice.id] = invoice;
    } else {
      selectedItems.value[invoice.id] = false;
    }
  });

  handleSelection(
    Object.values(selectedItems.value).filter((value) => value) as IInvoice[]
  );
};

function createOwnerDistribution() {
  if (!filters.owner) {
    ElNotification({
      title: "Sin Propietario",
      message: "Por favor seleccione el propietario para generar la factura",
      type: "error",
    });
    return;
  }

  const monthName = formatDate(formData.invoices.at(0).due_date, "MMMM, yyyy");

  formData.description = ` ${props.invoices?.at?.(0)?.owner_name} (${monthName})`;

  ElMessageBox({
    title: "Descripcion",
    message: () =>
      h(AppFormField, {
        label: "Descripcion de factura",
        modelValue: formData.description,
        onUpdateModelValue: (val: string) => {
          formData.description = val;
        },
      }),
  }).then(() => {
    formData.post(`/owners/${filters.owner}/draws`, {
      onSuccess() {
        ElNotification({
          title: "Creada",
          message: "Factura de propiedad creada con exito",
          type: "success",
        });
      },
    });
  });
}

const { openModal: openInvoiceModal } = useToggleModal("propertyCharge");

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
      <header class="flex items-center px-4 py-2 space-x-2">
        <AppButton
          variant="neutral"
          @click="filters.owner = null"
          v-if="owner"
          :processing="isLoading"
          :disabled="isLoading"
        >
          <IMdiChevronLeft />
        </AppButton>
        <h4 class="text-xl font-bold md:text-body-1 text-white">
          {{ sectionLabel }}
        </h4>
      </header>
    </template>
    <template #header>
      <DrawSectionNav>
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
              <span class="hidden md:inline-block"> Crear Gasto </span>
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
              <IMdiBankPlus />
              <span class="hidden md:inline-block">
                Crear Factura de distribucion ({{ formData.invoices.length }})
              </span>
              <span class="md:hidden"> ({{ formData.invoices.length }}) </span>
            </AppButton>
          </section>
        </template>
      </DrawSectionNav>
    </template>

    <div class="mx-auto mb-32 mt-16 sm:px-6 lg:px-8">
      <section class="md:flex md:space-x-4" v-if="!owner">
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

      <div class="mt-4 overflow-hidden rounded-md bg-base-lvl-3">
        <AppFormField label="Propietario" v-if="!owner && !isLoading">
          <article
            v-for="owner in invoices"
            @click="filters.owner = owner.owner_id"
            class="flex items-center border shadow-md rounded-md py-2 justify-between px-2 mb-2 transition-colors cursor-pointer group hover:bg-primary/20"
          >
            <section>
              <h4>
                {{ owner.owner_first_name }}
                <br />
                <small class="text-body-1">
                  {{ owner.owner_lastnames }}
                </small>
                ({{ owner.totalMonths }}/ {{ owner.total }})
              </h4>
              <span
                class="capitalize mt-2 inline-block bg-primary text-xs text-white px-2 py-1 rounded-2xl"
              >
                {{ $t("owner") }}
              </span>
            </section>
            <section class="flex items-center">
              <span class="capitalize">
                {{ formatDate(owner.last_invoice_date, "MMMM yyyy") }}</span
              >
              <button class="h-10 group-hover:text-primary">
                <IMdiChevronRight />
              </button>
            </section>
          </article>
        </AppFormField>

        <template v-if="invoices?.length && owner && !isLoading">
          <section v-for="invoiceInMonth in invoices">
            <header class="py-2 text-center text-white capitalize bg-primary">
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
              responsive
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
                      class="text-sm text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer"
                    >
                      {{ row.description }}
                      <span class="font-bold text-gray-300">
                        {{ row.series }} #{{ row.number }}
                      </span>
                    </Link>
                  </p>
                  <p>
                    <Link
                      class="mt-2 text-sm text-body-1"
                      :href="`/clients/${row.client_id || row.contact_id}`"
                    >
                      <i class="text-xs fa fa-user" />
                      {{ row.owner_name }}
                    </Link>
                  </p>
                </section>
              </template>
              <template v-slot:card="{ row: invoice }">
                <InvoiceCard
                  :invoice="invoice"
                  @click="toggleSelection([invoice.id])"
                  class="mb-6 border-b py-6 px-2 cursor-pointer"
                  :classs="{
                    'bg-primary/20': isSelected(invoices.id),
                  }"
                  hide-actions
                />
              </template>
            </BaseTable>
          </section>
          <footer class="md:flex justify-end px-4 py-2 space-x-2 hidden">
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

        <p class="flex items-center justify-center h-48" v-else-if="!isLoading">
          {{ emptyLabel }}
        </p>
        <section class="px-2 py-4" v-if="isLoading">
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
