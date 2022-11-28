<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed } from "vue";
import { format as formatDate } from "date-fns";
import { createLoan, generateInstallments } from "../../Modules/loans/features";
import { ILoan } from "../../Modules/loans/loanEntity";
import { loanFrequencies } from "../../Modules/loans/constants";
import { router } from "@inertiajs/core";
import InstallmentTable from "./Partials/InstallmentTable.vue";

defineProps<{
  loanData: ILoan[];
}>();

const loanForm = reactive<ILoan>({
  amount: 0,
  repayment_count: 0,
  interest_rate: 0,
  frequency: "MONTHLY",
  disbursement_date: formatDate(new Date(), "yyyy-MM-dd"),
  first_installment_date: "",
  grace_days: 0,
  client_id: 1,
});

const installments = ref<ILoanInstallment[]>([]);

const previewInstallments = () => {
  installments.value = generateInstallments({
    ...loanForm,
    first_installment_date:
      loanForm.first_installment_date &&
      formatDate(loanForm.first_installment_date, "yyyy-MM-dd"),
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
  createLoan(loanForm, installments.value)
    .then(() => {
      close();
      router.visit(`/loans/`);
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
  <AppLayout title="Crear Prestamos">
    <main class="p-5 w-full">
      <h1>Crear Prestamo</h1>
      <article class="w-full">
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
          class="text-red-400 font-bold bg-gray-100 rounded-md"
          variant="secondary"
          @click="goToList()"
        >
          Cancelar
        </AtButton>
        <AtButton
          class="bg-blue-500 text-white rounded-md"
          @click="previewInstallments()"
          :disabled="!canCalculate"
        >
          Calcular
        </AtButton>
        <AtButton
          class="bg-blue-500 text-white rounded-md"
          @click="onSubmit"
          v-if="hasInstallments"
        >
          Crear
        </AtButton>
      </footer>
    </main>
  </AppLayout>
</template>
