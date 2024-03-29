<script setup lang="ts">
// @ts-ignore
import { AtButton, AtField, AtInput, AtSimpleSelect } from "atmosphere-ui";
import { ref, reactive, computed, watch, watchEffect, nextTick } from "vue";
import { addMonths } from "date-fns";
// @ts-ignore
import AppButton from "@/Components/shared/AppButton.vue";
import InstallmentTable from "./InstallmentTable.vue";

import { ILoan } from "@/Modules/loans/loanEntity";
import { generateInstallments } from "@/Modules/loans/features";
import { loanFrequencies } from "@/Modules/loans/constants";
import { formatDate } from "@/utils";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "@/Pages/Rents/Partials/FormSection.vue";
import LoanSummary from "./LoanSummary.vue";
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { getNextDate } from "@/Modules/loans/nextDate";

interface Props {
  loans: ILoan;
  isLoading?: boolean;
  canSave?: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits(["update:isLoading", "update:canSave", "submit"]);

const loanForm = reactive<Record<string, any>>({
  id: null,
  client_id: null,
  client: undefined,
  amount: 0,
  repayment_count: 0,
  interest_rate: 0,
  frequency: "MONTHLY",
  disbursement_date: new Date(),
  first_repayment_date: addMonths(new Date(), 1),
  // advanced
  grace_days: 0,
  late_fee: 0,
  installments_paid: 0,
  closing_fees: 0,
  category_id: null,
  source_type: null,
  source_account_id: null,
  sourceAccount: null,
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

watchEffect(() => {
  if (loanForm.disbursement_date && loanForm.frequency) {
    nextTick(() => {
      loanForm.first_repayment_date = getNextDate(
        loanForm.disbursement_date,
        loanForm.frequency,
        false
      );
    });
  }
});

const formTitle = computed(() => {
  return props.loans?.id ? `Prestamo ${props.loans.client?.fullName}` : "Crear Prestamo";
});

const saveButtonLabel = computed(() => {
  return props.loans?.id ? "Guardar prestamo" : "Registrar Prestamo";
});

const installments = ref(null);

watchEffect(() => {
  const date = formatDate(loanForm.first_repayment_date, "yyyy-MM-dd");

  if (loanForm.amount && loanForm.repayment_count) {
    installments.value = generateInstallments({
      interest_rate: loanForm.interest_rate,
      amount: loanForm.amount,
      repayment_count: loanForm.repayment_count,
      first_repayment_date: date,
      frequency: loanForm.frequency,
    });
  }
});

const hasInstallments = computed(() => {
  return installments.value && installments.value?.payments?.length;
});

const isLoading = ref(false);
const submit = () => {
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
  emit("submit", {
    ...formData,
    installments: installments.value.payments,
  });
};

const showAdvancedOptions = ref(false);

defineExpose({
  submit,
});
</script>

<template>
  <main>
    <section class="w-full md:w-8/12 p-4 bg-white rounded-md shadow-md text-body-1">
      <FormSection title="Datos del cliente" class="w-full" section-class="w-full">
        <AppFormField label="Cliente" class="w-full" required>
          <BaseSelect
            v-model="loanForm.client"
            track-by="id"
            endpoint="/api/clients"
            placeholder="Selecciona un cliente"
            label="display_name"
            class="w-full"
            required
          />
        </AppFormField>
      </FormSection>
      <FormSection title="Terminos de prestamo" section-class="w-full">
        <section class="flex flex-col md:flex-row md:space-x-4">
          <AppFormField label="Monto a prestar" class="w-full" required>
            <AtInput v-model="loanForm.amount" number-format rounded />
          </AppFormField>
          <AppFormField label="Interés mensual" class="w-full" required>
            <AtInput
              v-model="loanForm.interest_rate"
              number-format
              max="100"
              min="0"
              rounded
            >
              <template #suffix>
                <div>
                  <!-- <IMdiPercentage /> -->
                </div>
              </template>
            </AtInput>
          </AppFormField>
          <AppFormField label="Cuotas" class="w-full" required>
            <AtInput v-model="loanForm.repayment_count" rounded />
          </AppFormField>
        </section>
        <section class="flex flex-col md:flex-row md:space-x-4">
          <AppFormField label="Fecha de desembolso" required>
            <ElDatePicker
              v-model="loanForm.disbursement_date"
              size="large"
              class="w-full"
            />
          </AppFormField>
          <AppFormField label="Fecha de primer pago" required>
            <ElDatePicker
              v-model="loanForm.first_repayment_date"
              size="large"
              class="w-full"
            />
          </AppFormField>
          <AppFormField label="Frecuencia" class="w-full" required>
            <AtSimpleSelect :options="loanFrequencies" v-model="loanForm.frequency" />
          </AppFormField>
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
        <section class="flex flex-col md:flex-row w-full md:space-x-4">
          <AppFormField label="Dias de gracia" class="w-full">
            <AtInput v-model="loanForm.grace_days" rounded />
          </AppFormField>
          <AppFormField label="Interes de mora" class="w-full">
            <AtInput v-model="loanForm.late_fee" rounded />
          </AppFormField>
          <AppFormField label="Cuotas cobradas" class="w-full" v-if="false">
            <AtInput v-model="loanForm.paid_installments" rounded />
          </AppFormField>
        </section>
        <section class="flex space-x-4">
          <AppFormField label="Gastos de cierre" class="w-full">
            <AtInput v-model="loanForm.closing_fees" rounded />
          </AppFormField>
          <AtField class="w-full hidden" />
        </section>
      </FormSection>
      <FormSection
        title="Contabilidad"
        section-class="w-full"
        class="mt-4"
        v-if="showAdvancedOptions"
      >
        <section class="flex space-x-4">
          <!-- <AtField label="Cartera de prestamo" class="w-full">
              <BaseSelect
                endpoint="/api/wallets"
                v-model="loanForm.category"
                placeholder="Selecciona una cartera"
              />
            </AtField> -->
          <!-- <AtField label="Origen de prestamo" class="w-full">
              <BaseSelect
                :options="loanSourceTypes"
                v-model="loanForm.sourceType"
                track-by="id"
                placeholder="Selecciona una cartera"
              />
            </AtField> -->
          <AppFormField
            label="Cuenta origen"
            field="sourceAccount"
            class="w-full"
            required
          >
            <AccountSelect v-model="loanForm.sourceAccount" />
          </AppFormField>
        </section>
      </FormSection>
    </section>

    <article
      class="w-full md:w-4/12 mt-4 md:mt-0 rounded-md bg-white shadow-md relative overflow-hidden grid gap-4 grid-cols-1 grid-rows-[1fr_50px]"
    >
      <section class="w-full px-4 overflow-hidden text-body-1">
        <header class="py-4 font-bold">Resumen de prestamo</header>
        <LoanSummary
          :payment="installments?.payment"
          :total-interest="installments?.totalInterest"
          :total-debt="installments?.totalDebt"
        />

        <section v-if="hasInstallments" class="mt-4">
          <h4 class="text-xl font-bold">Tabla de amortización</h4>
          <InstallmentTable
            :installments="installments?.payments"
            :hidden-cols="['loan_id', 'actions']"
          />
        </section>
      </section>
      <footer class="flex justify-between w-full px-4 py-1">
        <AtButton
          class="font-bold text-red-400 rounded-md bg-base-lvl-2"
          variant="secondary"
          @click="goToList()"
        >
          Cancelar
        </AtButton>
        <AppButton
          :processing="isLoading"
          variant="secondary"
          @click="submit"
          :disabled="!hasInstallments || isLoading"
        >
          Registar Prestamo
        </AppButton>
      </footer>
    </article>
  </main>
</template>
