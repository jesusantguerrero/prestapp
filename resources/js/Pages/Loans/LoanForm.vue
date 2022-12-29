<script setup lang="ts">
import { router } from "@inertiajs/core";
// @ts-ignore
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed, watch } from "vue";
import { addMonths } from "date-fns";
// @ts-ignore
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import { saveLoan, generateInstallments } from "../../Modules/loans/features";
import { loanFrequencies } from "@/Modules/loans/constants";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { formatDate } from "@/utils";

interface Props {
  loans: ILoan;
  clients: IClient[];
}

const props = defineProps<Props>();

const clientOptions = props.clients.map((client) => ({
  label: client.names + " " + client.lastnames,
  id: client.id,
}));

const loanForm = reactive<Record<string, any>>({
  id: null,
  amount: 0,
  repayment_count: 0,
  interest_rate: 0,
  frequency: "MONTHLY",
  disbursement_date: new Date(),
  first_installment_date: addMonths(new Date(), 1),
  grace_days: 0,
  client_id: 1,
  client: undefined,
});

watch(
  () => props.loans,
  (loanData: ILoan) => {
    if (loanData) {
      Object.keys(loanForm).forEach((field) => {
        loanForm[field] = loanData[field] || loanForm[field];
      });
      loanForm.client_id = props.loans.client?.id ?? 1;
      loanForm.client = props.loans.client;
    }
  },
  { immediate: true, deep: true }
);

const formTitle = computed(() => {
  return props.loans.id ? `Prestamo ${props.loans.client?.fullName}` : "Crear Prestamo";
});

const saveButtonLabel = computed(() => {
  return props.loans.id ? "Guardar prestamo" : "Crear Prestamo";
});

const installments = ref<ILoanInstallment[]>([]);

const previewInstallments = () => {
  installments.value = generateInstallments({
    ...loanForm,
    first_installment_date: formatDate(loanForm.first_installment_date, "yyyy-MM-dd"),
  });
};

const hasInstallments = computed(() => {
  return installments.value.length;
});

const canCalculate = computed(() => {
  const { repayment_count, amount, interest_rate, first_installment_date } = loanForm;
  return [repayment_count, amount, interest_rate, first_installment_date].every(
    (val) => val
  );
});

const onSubmit = () => {
  const formData = {
    ...loanForm,
    disbursement_date: formatDate(loanForm.disbursement_date, "yyyy-MM-dd"),
    first_installment_date: formatDate(loanForm.first_installment_date, "y-M-d"),
    client_id: loanForm.client.id,
  };

  saveLoan(formData, installments.value)
    .then(() => {
      close();
      goToList();
    })
    .catch((err) => {
      console.log(err);
    });
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
          <AppButton variant="inverse" @click="onSubmit()">
            {{ saveButtonLabel }}
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <main class="w-full mt-16 bg-white rounded-md p-4">
      <article>
        <h1 class="font-bold">Datos Del Cliente</h1>
        <AtField label="Cliente" class="w-full">
          <AtSimpleSelect
            :options="clientOptions"
            v-model="loanForm.client"
            label="display_name"
            track-by="id"
            placeholder="Selecciona un cliente"
          />
        </AtField>
      </article>
      <article class="w-full">
        <h1 class="font-bold">Datos Generales</h1>
        <section class="flex space-x-4">
          <AtField label="Monto" class="w-full">
            <AtInput v-model="loanForm.amount" />
          </AtField>
          <AtField label="Interés" class="w-full">
            <AtInput v-model="loanForm.interest_rate" />
          </AtField>
          <AtField label="Cuotas" class="w-full">
            <AtInput v-model="loanForm.repayment_count" />
          </AtField>
        </section>
        <section class="flex">
          <AtField label="Fecha de desembolso">
            <ElDatePicker v-model="loanForm.disbursement_date" size="large" />
          </AtField>
          <AtField label="Fecha de primer pago">
            <ElDatePicker v-model="loanForm.first_installment_date" size="large" />
          </AtField>
          <AtField label="Frecuencia" class="w-full">
            <AtSimpleSelect :options="loanFrequencies" v-model="loanForm.frequency" />
          </AtField>
        </section>
        <section v-if="hasInstallments">
          <h4 class="text-xl font-bold">Tabla de amortización</h4>
          <InstallmentTable :installments="installments" />
        </section>
      </article>

      <footer class="flex justify-end space-x-2">
        <AtButton
          class="font-bold text-red-400 bg-gray-100 rounded-md"
          variant="secondary"
          @click="goToList()"
        >
          Cancelar
        </AtButton>
        <AtButton
          class="text-white bg-blue-500 rounded-md"
          @click="previewInstallments()"
          :disabled="!canCalculate"
        >
          Calcular
        </AtButton>
        <AtButton
          class="text-white bg-blue-500 rounded-md"
          @click="onSubmit"
          v-if="hasInstallments"
        >
          Crear
        </AtButton>
      </footer>
    </main>
  </AppLayout>
</template>
