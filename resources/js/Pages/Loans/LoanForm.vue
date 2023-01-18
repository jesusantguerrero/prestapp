<script setup lang="ts">
import { router } from "@inertiajs/core";
// @ts-ignore
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed, watch, watchEffect } from "vue";
import { addMonths } from "date-fns";
// @ts-ignore
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";
import IconCoins from "@/Components/icons/IconCoins.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./Partials/LoanSectionNav.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { IClient } from "@/Modules/clients/clientEntity";
import { saveLoan, generateInstallments } from "../../Modules/loans/features";
import { loanFrequencies, loanSourceTypes } from "@/Modules/loans/constants";
import { formatDate, formatMoney } from "@/utils";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "../Rents/Partials/FormSection.vue";

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
  return props.loans?.id ? `Prestamo ${props.loans.client?.fullName}` : "Crear Prestamo";
});

const saveButtonLabel = computed(() => {
  return props.loans?.id ? "Guardar prestamo" : "Registrar Prestamo";
});

const installments = ref(null);

watchEffect(() => {
  const date = formatDate(loanForm.first_installment_date, "yyyy-MM-dd");

  if (loanForm.amount && loanForm.interest_rate && loanForm.repayment_count) {
    installments.value = generateInstallments({
      interest_rate: loanForm.interest_rate,
      amount: loanForm.amount,
      repayment_count: loanForm.repayment_count,
      first_installment_date: date,
      frequency: loanForm.frequency,
    });
  }
});

const hasInstallments = computed(() => {
  return installments.value && installments.value?.payments?.length;
});

const onSubmit = () => {
  const formData = {
    ...loanForm,
    disbursement_date: formatDate(loanForm.disbursement_date, "yyyy-MM-dd"),
    first_installment_date: formatDate(loanForm.first_installment_date, "y-M-d"),
    client_id: loanForm.client.id,
  };

  saveLoan(formData, installments.value.payments)
    .then(() => {
      close();
      goToList();
    })
    .catch((err) => {
      console.log(err);
    });
};

const showAdvancedOptions = ref(false);

const goToList = () => {
  router.visit("/loans");
};
</script>

<template>
  <AppLayout :title="formTitle">
    <template #header>
      <LoanSectionNav>
        <template #actions>
          <AppButton variant="secondary" @click="onSubmit()" :disabled="!hasInstallments">
            {{ saveButtonLabel }}
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <main class="flex w-full mt-16 space-x-4 pb-10">
      <section class="w-8/12 p-4 text-body-1 bg-white rounded-md shadow-md">
        <FormSection title="Datos del cliente">
          <AtField label="Cliente" class="w-full">
            <BaseSelect
              v-model="loanForm.client"
              track-by="id"
              endpoint="/api/clients"
              placeholder="Selecciona un cliente"
              label="display_name"
            />
          </AtField>
        </FormSection>
        <FormSection title="Datos de termino" section-class="w-full">
          <section class="flex space-x-4">
            <AtField label="Monto a prestar" class="w-full">
              <AtInput v-model="loanForm.amount" number-format rounded />
            </AtField>
            <AtField label="Interés mensual" class="w-full">
              <AtInput v-model="loanForm.interest_rate" number-format max="100" rounded>
                <template #suffix>
                  <div>
                    <!-- <IMdiPercentage /> -->
                  </div>
                </template>
              </AtInput>
            </AtField>
            <AtField label="Cuotas" class="w-full">
              <AtInput v-model="loanForm.repayment_count" rounded />
            </AtField>
          </section>
          <section class="flex space-x-4">
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
        </FormSection>
        <AppButton class="w-full" @click="showAdvancedOptions = !showAdvancedOptions">
          Opciones Avanzadas</AppButton
        >
        <FormSection
          title="Avanzadas"
          section-class="w-full"
          class="mt-4"
          v-if="showAdvancedOptions"
        >
          <section class="w-full flex space-x-4">
            <AtField label="Dias de gracia" class="w-full">
              <AtInput v-model="loanForm.grace_days" rounded />
            </AtField>
            <AtField label="Interes de mora" class="w-full">
              <AtInput v-model="loanForm.late_fee" rounded />
            </AtField>
            <AtField label="Cuotas cobradas" class="w-full">
              <AtInput v-model="loanForm.grace_days" rounded />
            </AtField>
          </section>
          <section class="flex space-x-4">
            <AtField label="Gastos de cierre" class="w-full">
              <AtInput v-model="loanForm.closing_fees" rounded />
            </AtField>
            <AtField label="Cuotas cobradas" class="w-full">
              <AtInput v-model="loanForm.paid_installments" rounded />
            </AtField>
            <AtField class="w-full" />
          </section>
        </FormSection>
        <FormSection
          title="Contabilidad"
          section-class="w-full"
          class="mt-4"
          v-if="showAdvancedOptions"
        >
          <section class="flex space-x-4">
            <AtField label="Cartera de prestamo" class="w-full">
              <BaseSelect
                endpoint="/api/wallets"
                v-model="loanForm.wallet"
                placeholder="Selecciona una cartera"
              />
            </AtField>
            <AtField label="Origen de prestamo" class="w-full">
              <BaseSelect
                :options="loanSourceTypes"
                v-model="loanForm.transaction_source_type"
                track-by="id"
                placeholder="Selecciona una cartera"
              />
            </AtField>
            <AtField label="Cuenta origen" class="w-full">
              <BaseSelect
                :options="loanSourceTypes"
                v-model="loanForm.transactionSource"
                placeholder="Selecciona una cuenta"
              />
            </AtField>
          </section>
        </FormSection>
      </section>

      <article
        class="w-4/12 rounded-md bg-white shadow-md relative grid gap-4 grid-rows-[1fr_50px]"
      >
        <section class="text-body-1 w-full overflow-hidden px-4">
          <header class="py-4 font-bold">Resumen de prestamo</header>
          <div
            class="rounded-md flex justify-between bg-primary/5 border-primary/20 font-bold border px-4 relative"
          >
            <div
              class="h-8 w-8 rounded-full items-center flex justify-center cursor-pointer hover:bg-primary hover:text-white transition-colors text-primary bg-[#F6FBFE] border border-primary/20 absolute top-6 -left-4"
            >
              <IconCoins />
            </div>
            <AtField label="Monto Cuotas">
              <span class="text-primary">
                {{ formatMoney(installments?.payment) }}
              </span>
            </AtField>
            <AtField label="Interes a pagar">
              <span class="text-error">
                {{ formatMoney(installments?.totalInterest) }}
              </span>
            </AtField>
            <AtField label="Total a pagar">
              <span class="text-secondary">
                {{ formatMoney(installments?.totalDebt) }}
              </span>
            </AtField>
          </div>
          <section v-if="hasInstallments" class="mt-4">
            <h4 class="text-xl font-bold">Tabla de amortización</h4>
            <InstallmentTable :installments="installments?.payments" />
          </section>
        </section>
        <footer class="flex w-full justify-between py-1 px-4">
          <AtButton
            class="font-bold text-red-400 bg-base-lvl-2 rounded-md"
            variant="secondary"
            @click="goToList()"
          >
            Cancelar
          </AtButton>
          <AppButton variant="secondary" @click="onSubmit" :disabled="!hasInstallments">
            Registar Prestamo
          </AppButton>
        </footer>
      </article>
    </main>
  </AppLayout>
</template>
