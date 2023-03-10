<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";

import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { IInvoice } from "@/Modules/loans/loanEntity";
import { ElMessageBox } from "element-plus";
import { formatMoney } from "@/utils";

const { openModal } = usePaymentModal();

interface Props {
  invoice: Object;
  accountsEndpoint: string;
}

const props = withDefaults(defineProps<Props>(), {
  accountsEndpoint: "/api/accounts",
});
const actions = {
  payment: {
    label: "Registrar Pago",
  },
  send: {
    label: "Enviar Correo",
  },
  download: {
    label: "Descargar PDF",
  },
  view: {
    label: "Ver factura",
  },
  delete: {
    label: "Eliminar Factura",
  },
};

const selectedPayment = ref<Object | null>(null);
const paymentConcept = computed(() => {
  return selectedPayment.value && `Pago ${props.invoice.concept}`;
});

const onPayment = (invoice: Object) => {
  selectedPayment.value = {
    ...invoice,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(invoice.debt) || invoice.total,
    id: undefined,
    invoice_id: invoice.id,
  };

  const invoiceEndpoints = {
    ''
  }
  nextTick(() => {
    openModal({
      data: {
        title: `Pagar ${invoice.concept}`,
        payment: selectedPayment.value,
        endpoint: `/invoices/${props.invoice?.id}/payment`,
        due: selectedPayment.value?.amount,
        defaultConcept: paymentConcept.value,
        accountsEndpoint: props.accountsEndpoint,
      },
    });
  });
};

const linkToPrint = ref("");
const invoiceLink = ref();
const onDownload = (invoice: IInvoice) => {
  linkToPrint.value = `/invoices/${invoice.id}/print`;
  nextTick(() => {
    invoiceLink.value.click();
    linkToPrint.value = "";
  });
};

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

const handleActions = (actionName, invoice) => {
  switch (actionName) {
    case "payment":
      onPayment(invoice);
      break;
    case "download":
      onDownload(invoice);
      break;
    case "delete":
      onDelete(invoice);
      break;
  }
};

const refresh = () => {
  router.reload();
};
</script>

<template>
  <ElDropdown v-if="actions" @command="handleActions($event, invoice)">
    <button class="px-5 py-2 rounded-md hover:bg-base-lvl-2">
      <i class="fa fa-ellipsis-h" />
    </button>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem :command="actionName" v-for="(action, actionName) in actions">
          {{ action.label }}
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>

  <a :href="linkToPrint" target="_blank" ref="invoiceLink" type="hidden"></a>
</template>
