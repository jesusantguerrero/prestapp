<script setup lang="ts">
// @ts-ignore
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
// @ts-ignore
import { AtBackgroundIconCard } from "atmosphere-ui";
// @ts-ignore
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
// @ts-ignore
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
// @ts-ignore: its my template
import LoanSectionNav from "./LoanSectionNav.vue";
// @ts-ignore

import { formatMoney } from "@/utils/formatMoney";
import { ILoan } from "@/Modules/loans/loanEntity";
import { useToggle } from "@vueuse/shared";
import AgreementFormModal from "./AgreementFormModal.vue";
import { formatDate } from "@/utils";

const [isAgreementModalOpen, toggleAgreementModal] = useToggle();

interface Props {
  loans: ILoan;
  currentTab: string;
  stats: Object;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "",
});

const clientName = computed(() => props.loans.client?.fullName);

const tabs = {
  "": "Detalles",
  payments: "Pagos",
  agreements: "Acuerdos de pago",
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

const onUpdateStatus = () => {
  router.post(`/loans/${props.loans.id}/update-status`);
};
</script>

<template>
  <AppLayout :title="`Prestamo ${clientName}`">
    <template #header>
      <LoanSectionNav>
        <template #actions>
          <AppButton variant="inverse" @click="router.visit(`/loans/${loans.id}/edit`)">
            Editar prestamo
          </AppButton>
        </template>
      </LoanSectionNav>
    </template>

    <main class="mt-16">
      <AppSectionHeader
        name="Prestamo a"
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="loans"
        :title="clientName"
        hide-action
        @create="router.visit('/loans/create')"
      />
      <section
        class="w-full px-6 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <header class="flex justify-between space-x-4">
          <section class="w-full">
            <p class="flex justify-between w-full">
              <span> No.: </span>
              <span class="font-bold"> {{ loans.id }} </span>
            </p>
            <p class="flex justify-between w-full">
              <span> Fecha: </span>
              <span class="font-bold"> {{ formatDate(loans.date) }} </span>
            </p>
            <p class="flex justify-between w-full">
              <span> Monto Prestado/Total a pagar: </span>
              <span class="font-bold">
                {{ formatMoney(loans.amount) }} / {{ formatMoney(loans.total) }}
              </span>
            </p>
          </section>
          <section class="w-full">
            <p class="flex justify-between w-full">
              <span> Interes: </span>
              <span class="font-bold"> {{ loans.interest_rate }} %</span>
            </p>
            <p class="flex justify-between w-full">
              <span> Pago/Modo: </span>
              <span class="font-bold"> {{ loans.payment }}/ {{ loans.frequency }}</span>
            </p>
            <p class="flex justify-between w-full">
              <span> Dias de gracia: </span>
              <span class="font-bold"> {{ loans.grace_days }} Dias</span>
            </p>
            <p class="flex justify-between w-full">
              <span> Monto pagado: </span>
              <span class="font-bold">
                {{ formatMoney(loans.amount_paid) }}
              </span>
            </p>
          </section>
        </header>
        <footer class="flex space-x-2">
          <Link
            class="px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200"
            v-for="(tabLabel, tab) in tabs"
            :key="tab"
            :class="{ 'bg-primary/10 text-primary font-bold': tab == currentTab }"
            :href="`/loans/${loans.id}/${tab}`"
            replace
          >
            {{ tabLabel }}
          </Link>
        </footer>
      </section>
      <section class="flex w-full space-x-8 rounded-t-none border-t-none">
        <article class="w-9/12 space-y-2 overflow-auto">
          <section class="flex space-x-4">
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Capital pendiente"
              :value="formatMoney(stats.outstandingPrincipal)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Interes pendiente"
              :value="formatMoney(stats.outstandingInterest)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Mora pendiente"
              :value="formatMoney(stats.outstandingFee)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Monto pendiente"
              :value="formatMoney(stats.outstandingTotal)"
            />
          </section>

          <slot />
        </article>

        <article
          class="w-3/12 p-4 space-y-2 overflow-hidden border rounded-md shadow-md bg-base-lvl-3"
        >
          <section class="grid grid-cols-1 gap-2">
            <AppButton
              @click="onMultiplePayment()"
              class="flex items-center justify-center text-sm"
            >
              <IIcSharpPayment class="mr-2" />
              Recibo Multiple
            </AppButton>
            <AppButton variant="primary" class="flex items-center justify-center text-sm">
              <IIcSharpPayment class="mr-2" />
              Saldar prestamo
            </AppButton>
            <AppButton
              @click="toggleAgreementModal()"
              variant="secondary"
              class="flex items-center justify-center text-sm"
            >
              <IMdiHandshakeOutline class="mr-1" />
              Acuerdo de pago
            </AppButton>

            <AppButton
              variant="secondary"
              class="flex items-center justify-center w-full text-sm"
            >
              <IMdiHandshakeOutline class="mr-1" />
              Finalizar
            </AppButton>
          </section>

          <section class="py-4 mt-8 space-y-2">
            <div
              class="w-full px-2 py-4 rounded-md bg-base-lvl-2 text-body-1"
              v-if="loans.cancelled_at"
            >
              <h4>Cancelado en {{ formatDate(loans.cancelled_at, "dd MMMM yyyy") }}</h4>
              <header class="font-bold">Razon de cancelacion</header>
              {{ loans.cancel_reason }}
            </div>
          </section>
        </article>
      </section>
    </main>

    <AgreementFormModal
      v-model="isAgreementModalOpen"
      :loan="loans"
      @update:model-value="toggleAgreementModal(false)"
    />
  </AppLayout>
</template>
