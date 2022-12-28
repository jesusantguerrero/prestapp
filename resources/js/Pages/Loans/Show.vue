<script setup lang="ts">
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";

// @ts-ignore
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
// @ts-ignore
import InstallmentTable from "./Partials/InstallmentTable.vue";
// @ts-ignore
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

const clientName = computed(() => props.loans.client?.fullName);

const tabs = {
  summary: "Detalles",
  documents: "Tabla de Amortización",
  transactions: "Pagos",
  details: "Details",
};

// payment related things
type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
  documents: any[];
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);
const onPayment = (installment: ILoanInstallment) => {
  selectedPayment.value = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_due) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  isPaymentModalOpen.value = true;
};
const onMultiplePayment = () => {
  selectedPayment.value = {
    // @ts-ignore solve backend sending decimals as strings
    amount: 0,
    id: undefined,
    documents: props.loans.installments
      .map((installment) => ({
        name: `Pago ${installment.id}`,
        ...installment,
        // @ts-ignore solve backend sending decimals as strings
        amount: parseFloat(installment.amount_due),
        payment: 0,
      }))
      .filter((doc) => {
        console.log(doc);
        // @ts-ignore solve backend sending decimals as strings
        return parseFloat(doc.amount || 0);
      }),
  };

  isPaymentModalOpen.value = true;
};
const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.loans.id} pago #${selectedPayment.value.installment_id}`
  );
});

const paymentUrl = computed(() => {
  const baseUrl = `/loans/${props.loans?.id}`;
  const url = selectedPayment.value?.documents?.length
    ? `${baseUrl}/pay`
    : `${baseUrl}/installments/${selectedPayment.value?.installment_id}/pay`;
  return url;
});

const refresh = () => {
  router.reload();
};
</script>

<template>
  <AppLayout :title="`Prestamo ${clientName}`">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="loans"
        :title="clientName"
        hide-action
        @create="router.visit('/loans/create')"
      />
      <div
        class="w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <div>Prestamo #{{ loans.id }} para {{ clientName }}</div>
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
      <section class="flex w-full space-x-8 rounded-t-none border-t-none">
        <article
          class="w-9/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3 overflow-auto"
        >
          <span> Cliente: {{ clientName }} </span>
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

        <article class="w-3/12 p-4 space-y-2 border rounded-md shadow-md bg-base-lvl-3">
          <AppButton class="w-full" @click="onMultiplePayment()">
            Agregar Pago
          </AppButton>
          <AppButton class="w-full" @click="onMultiplePayment()">
            Recibo Multiple
          </AppButton>

          <section class="py-4 mt-8 space-y-2">
            <div v-for="payment in loans.payment_documents" class="text-sm">
              Pagado
              <span class="font-bold text-green-500">
                {{ formatMoney(payment.amount) }}
              </span>
              en
              <span class="font-bold text-primary">
                {{ payment.payment_date }}
              </span>
              <a
                :href="`/loans/${loans.id}/payments/${payment.id}/print`"
                target="_blank"
                rel="noopener noreferrer"
              >
                Recibo
              </a>
            </div>
          </section>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        :payment="selectedPayment"
        :endpoint="paymentUrl"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
        @closed="selectedPayment = null"
      />
    </main>
  </AppLayout>
</template>
