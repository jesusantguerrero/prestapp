<script setup lang="ts">
import { router } from "@inertiajs/core";
import { ref, computed } from "vue";
// @ts-ignore
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { refinanceLoan } from "@/Modules/loans/features";
import { formatDate } from "@/utils";
import LoanForm from "./Partials/LoanForm.vue";

interface Props {
  loans: ILoan;
}

const props = defineProps<Props>();

const formTitle = computed(() => {
  return props.loans?.id ? `Prestamo ${props.loans.client?.fullName}` : "Crear Prestamo";
});

const saveButtonLabel = computed(() => {
  return props.loans?.id ? "Guardar prestamo" : "Registrar Prestamo";
});

const loanFormRef = ref();
const canSave = ref(false);
const isLoading = ref(false);

const onSubmit = (loanForm: Record<string, any>) => {
  if (isLoading.value) return;
  const formData = {
    ...loanForm,
    date: formatDate(loanForm.disbursement_date, "yyyy-MM-dd"),
    disbursement_date: formatDate(loanForm.disbursement_date, "yyyy-MM-dd"),
    first_repayment_date: formatDate(loanForm.first_repayment_date, "y-M-d"),
    client_id: loanForm.client.id,
    source_type: loanForm.sourceType?.id,
    source_account_id: loanForm.sourceAccount?.id,
  };
  isLoading.value = true;

  refinanceLoan(props.loans.id, formData)
    .then(() => {
      close();
      goToList();
    })
    .catch((err) => {
      console.log(err);
    })
    .finally(() => {
      isLoading.value = false;
    });
};

const onSave = () => {
  loanFormRef.value.submit();
};
const goToList = () => {
  router.visit("/loans");
};
</script>

<template>
  <AppLayout :title="formTitle">
    <template #header>
      <LoanSectionNav>
        <template #actions>
          <AppButton
            variant="secondary"
            class="hidden md:flex"
            @click="onSave"
            :disabled="!canSave"
          >
            {{ saveButtonLabel }}
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <LoanForm
      class="flex flex-col md:flex-row w-full pb-10 mt-24 md:mt-16 md:space-x-4"
      ref="loanFormRef"
      :loans="loans"
      v-model:is-loading="isLoading"
      v-model:can-save="canSave"
      @submit="onSubmit"
    />
  </AppLayout>
</template>
