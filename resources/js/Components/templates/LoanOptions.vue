<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import { usePaymentModal } from "@/Modules/transactions/usePaymentModal";
import { ILoan } from "@/Modules/loans/loanEntity";
import AppButton from "../shared/AppButton.vue";

const { openModal } = usePaymentModal();

interface Props {
  loan: ILoan;
}

const props = defineProps<Props>();
const actions = {
  refinance: {
    label: "Reenganche",
  },
  reduceInterest: {
    label: "Reducir interes",
  },
  reduceInstallments: {
    label: "Reducir cuotas",
  },
  changeFrequency: {
    label: "Cambiar tipo",
  },
  cancel: {
    label: "Cancelar",
  },
  delete: {
    label: "Eliminar",
  },
};

const handleActions = (actionName: string, loan: ILoan) => {
  switch (actionName) {
    case "refinance":
      debugger;
      router.visit(`/loans/${props.loan?.id}/refinance`);
      break;
    case "delete":
      // onPayment(invoice);
      break;
    case "cancel":
      // onDownload(invoice);
      break;
  }
};

const refresh = () => {
  router.reload();
};
</script>

<template>
  <ElDropdown v-if="actions" @command="handleActions($event, loan)">
    <AppButton variant="neutral" class="hover:bg-base-lvl-2">
      <i class="fa fa-ellipsis-h" />
    </AppButton>
    <template #dropdown>
      <ElDropdownMenu>
        <ElDropdownItem :command="actionName" v-for="(action, actionName) in actions">
          {{ action.label }}
        </ElDropdownItem>
      </ElDropdownMenu>
    </template>
  </ElDropdown>
</template>
