<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref, computed, nextTick } from "vue";
import { ILoanInstallment } from "../../Modules/loans/loanInstallmentEntity";
import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";

const { openModal  } = usePaymentModal() 

interface Props {
  invoice: Object;
}

const props = defineProps<Props>();
const actions = {
        payment: {
          label: 'Registrar Pago',
        },
        send: {
          label: 'Enviar Correo'
        },
        download: {
          label: 'Descargar PDF'
        },
        view: {
          label: 'Ver factura'
        },
        delete: {
          label: 'Eliminar Factura'
        }
};

const selectedPayment = ref<Object | null>(null);
const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.invoice.id} pago`
  );
});

const onPayment = (invoice: Object) => {
  selectedPayment.value = {
    ...invoice,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(invoice.debt) || invoice.total,
    id: undefined,
    invoice_id: invoice.id,
  };

  nextTick(() => {
    openModal({
      data: {
        title: 'Pagar Renta',
        payment: selectedPayment.value,
        endpoint: `/invoices/${props.invoice?.id}/payment`,
        due: selectedPayment.value?.amount,
        defaultConcept: paymentConcept.value
      }
    })
  })
};

const linkToPrint = ref('');
const invoiceLink = ref();
const onDownload = (invoice) => {
  linkToPrint.value = `/invoices/${invoice.id}/print`
  console.log(linkToPrint.value, invoice);
  nextTick(() => {
    invoiceLink.value.click()
    linkToPrint.value = ''
  })
}

const handleActions = (actionName, invoice) => {
  switch(actionName) {
    case 'payment': 
      onPayment(invoice);
    break;
    case 'download': 
      onDownload(invoice);
    break;
  }
  if (actionName == 'payment') {
  }
}


const refresh = () => {
  router.reload();
};
</script>

<template>
  <ElDropdown v-if="actions" @command="handleActions($event, invoice)">
    <button class="px-5 rounded-md hover:bg-base-lvl-2">
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

  <a :href="linkToPrint" download target="_blank" ref="invoiceLink" type="hidden"></a>
</template>
