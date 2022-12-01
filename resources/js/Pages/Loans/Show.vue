<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import InstallmentTable from "./Partials/InstallmentTable.vue";
import PaymentFormModal from "./Partials/PaymentFormModal.vue";
import { formatMoney } from "@/utils/formatMoney";
import { ILoanInstallment } from "../../Modules/loans/loanInstallmentEntity";

export interface Props {
  loans: ILoanWithInstallments;
  currentTab: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  summary: "Detalles",
  documents: "Tabla de Amortización",
  transactions: "Pagos",
  details: "Details",
};

type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);
const onPayment = (installment: ILoanInstallment) => {
  selectedPayment.value = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_debt) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  isPaymentModalOpen.value = true;
};

const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.loans.id} pago #${selectedPayment.value.installment_id}`
  );
});

const refresh = () => {
  router.reload();
};
</script>

<template>
  <AppLayout title="Prestamos">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="rounded-md px-5 rounded-b-none border-2 shadow-md border-white"
        :resource="loans"
        :title="loans.client.names"
        @create="router.visit('/loans/create')"
      />
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <div>
          Prestamo #{{ loans.id }} para
          {{ loans.client.names + " " + loans.client?.lastnames }}
        </div>
        <div class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-gray-300': tab == currentTab }"
            :href="`/loans/${loans.id}?current-tab=${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </div>
      </div>
      <section class="space-x-8 flex w-full border-t-none rounded-t-none">
        <article class="w-9/12 rounded-md border p-4 shadow-md space-y-2">
          <span> Cliente: {{ loans.client.names }} {{ loans.client.lastnames }} </span>
          <p>
            Monto Prestado:
            {{ loans.amount }}
          </p>
          <p>
            Fecha Primer Pago:
            {{ loans.first_installment_date }}
          </p>
          <p>
            Estatus:
            {{ loans.payment_status }}
          </p>

          <InstallmentTable
            :installments="loans.installments"
            accept-payment
            @pay="onPayment"
          />
        </article>
        <article class="w-3/12 rounded-md border p-4 shadow-md space-y-2">
          <AppButton class="w-full"> Agregar Pago </AppButton>
          <AppButton class="w-full"> Recibo Multiple </AppButton>

          <section class="mt-8 py-4 space-y-2">
            <div v-for="payment in loans.payment_documents" class="text-sm">
              Pagado
              <span class="text-green-500 font-bold">
                {{ formatMoney(payment.amount) }}
              </span>
              en
              <span class="text-primary font-bold">
                {{ payment.payment_date }}
              </span>
            </div>
          </section>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        :payment="selectedPayment"
        :endpoint="`/loans/${loans.id}/installments/${selectedPayment.installment_id}/pay`"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
      />
    </main>
  </AppLayout>
</template>
