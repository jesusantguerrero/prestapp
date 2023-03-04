<script setup lang="ts">
import { reactive, watch, Ref } from "vue";
import { AtBackgroundIconCard } from "atmosphere-ui";
import { router, useForm } from "@inertiajs/vue3";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "../Partials/PropertySectionNav.vue";

import { formatMoney, formatDate } from "@/utils";
import BaseTable from "@/Components/shared/BaseTable.vue";
import { ElNotification, TableColumnCtx } from "element-plus";
import AppButton from "@/Components/shared/AppButton.vue";

const props = defineProps({
  invoices: {
    type: Array,
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
});

const filters = reactive({
  owner: null,
  property: null,
});

watch(
  () => filters,
  () => {
    const selectedFilters = Object.entries(filters).reduce(
      (acc, [filterName, filter]) => {
        acc[filterName] = filter?.value;
        return acc;
      },
      {}
    );

    router.get(
      location.pathname,
      {
        filters: selectedFilters,
      },
      { preserveState: true }
    );
  },
  { deep: true }
);

interface IInvoice {
  id: number;
  total: number;
}

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
    width: 300,
  },
  {
    name: "due_date",
    label: "Fecha",
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
  {
    name: "Debt",
    label: "Pago pendiente",
    render(row: any) {
      return formatMoney(row.debt);
    },
  },
  {
    name: "total",
    label: "Monto a pagar",
    render(row: any) {
      return formatMoney(row.total);
    },
  },
];

const formData = useForm({
  client: null,
  invoices: [] as IInvoice[],
});

function handleSelection(selectedInvoices: IInvoice[]) {
  console.log(selectedInvoices);
  formData.invoices = selectedInvoices.map((invoice) => ({
    id: invoice.id,
    total: invoice.total,
  }));
}

function createOwnerDistribution() {
  formData.post(`/owners/${filters.owner?.id}/draws`, {
    onSuccess() {
      ElNotification({
        title: "Creada",
        message: "Factura de propiedad creada con exito",
        type: "success",
      });
    },
  });
}
</script>

<template>
  <AppLayout title="Centro de pago">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <BaseSelect
            v-model="filters.owner"
            endpoint="/api/clients?filter[is_owner]=1"
            placeholder="Selecciona un dueño"
            label="display_name"
            track-by="id"
          />
        </template>
      </PropertySectionNav>
    </template>

    <div class="py-10 mx-auto sm:px-6 lg:px-8">
      <section class="flex space-x-4">
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Pagado"
          :value="formatMoney(paid)"
        />
        <button v-if="deposits">Renbolsar Deposito</button>
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Balance de Pendiente"
          :value="formatMoney(outstanding)"
        />
        <AtBackgroundIconCard
          class="w-full bg-white border h-28 text-body-1"
          title="Dias de mora"
          :value="lateDays || 0"
        />
      </section>

      <div class="mt-4 bg-base-lvl-3 rounded-md overflow-hidden px-1">
        <BaseTable
          v-for="client in invoices"
          class="mt-0"
          table-class="px-0"
          show-summary
          selectable
          @selection-change="handleSelection"
          :summary-method="getSummaries"
          :cols="drawCols"
          :table-data="client.invoices"
        />
        <footer class="flex justify-end px-4 py-2">
          <AppButton variant="secondary" @click="createOwnerDistribution">
            Crear Factura de distribucion
          </AppButton>
        </footer>
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
