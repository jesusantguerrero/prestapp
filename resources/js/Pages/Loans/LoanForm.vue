<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed } from "vue";

import { createLoan, generateInstallments } from "../../Modules/loans/features";
import { ILoanInstallment, ILoan } from "../../Modules/loans/loanEntity";
import { loanFrequencies } from "../../Modules/loans/constants";

defineProps<{
  loanData: ILoan[];
}>();

const loanForm = reactive({
  amount: 0,
  count: 0,
  interest_rate: 0,
  frequency: "MONTHLY",
  start_date: "",
  grace_days: 0,
  contact_id: 1,
});

const installments = ref<ILoanInstallment[]>([]);

const previewInstallments = () => {
  installments.value = generateInstallments(loanForm);
};

const hasInstallments = computed(() => {
  return installments.value.length;
});

const onSubmit = () => {
  createLoan(loanForm, installments.value)
    .then(() => {
      close();
      router.reload();
    })
    .catch((err) => {
      console.log(err);
    });
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
            <AtInput v-model="loanForm.count" />
          </AtField>
          <AtField label="Frecuencia" class="w-full">
            <AtSimpleSelect :options="loanFrequencies" v-model="loanForm.frequency" />
          </AtField>
        </section>
        <section v-if="hasInstallments">
          <table class="table w-full">
            <thead class="bg-blue-400 py-4 text-xl text-white">
              <td class="bg-blue-400 p-2 text-white">Payment</td>
              <td class="bg-blue-400 p-2 text-white">Principal</td>
              <td class="bg-blue-400 p-2 text-white">Interest</td>
            </thead>
            <tr v-for="installment in installments">
              <td class="p-2">{{ installment.amount.toFixed(2) }}</td>
              <td class="p-2">{{ installment.principal.toFixed(2) }}</td>
              <td class="p-2">{{ installment.interest.toFixed(2) }}</td>
            </tr>
          </table>
        </section>
      </article>
      <footer class="flex justify-end space-x-2">
        <AtButton
          class="bg-blue-500 text-white rounded-md"
          @click="previewInstallments()"
        >
          Calcular
        </AtButton>
        <template v-if="hasInstallments">
          <AtButton
            class="bg-blue-500 text-white rounded-md"
            @click="router.visit('/loans')"
          >
            Cancelar
          </AtButton>
          <AtButton class="bg-blue-500 text-white rounded-md" @click="onSubmit">
            Crear
          </AtButton>
        </template>
      </footer>
    </main>
  </AppLayout>
</template>
