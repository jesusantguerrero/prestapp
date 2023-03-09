<script setup lang="ts">
import { router } from "@inertiajs/core";

import { useTransactionModal } from "@/Modules/transactions/useTransactionModal";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { computed } from "vue";

const props = defineProps<{
  extended: boolean;
}>();

const { openModal } = useToggleModal("contact");
const { openModal: openInvoiceModal } = useToggleModal("invoice");

const welcomeCards = computed(() => {
  const options = [
    {
      label: "Contactos",
      extended: true,
    },
    {
      label: "Crear un contacto",
      icon: "contact",
      action() {
        openModal({
          data: { type: "lender " },
          isOpen: true,
        });
      },
    },
    {
      label: "Prestamos",
      extended: true,
    },
    {
      label: "Crear un prestamo",
      icon: "money",
      action() {
        router.visit("/loans/create");
      },
    },
    {
      label: "Precalcular prestamo",
      icon: "calculator",
      action() {
        router.visit("/loans/create");
      },
      extended: true,
    },
    {
      label: "Pago prestamo",
      icon: "money",
      action() {
        router.visit("/payment-center");
      },
      extended: true,
    },
    {
      label: "Propiedades",
      extended: true,
    },
    {
      label: "Agregar propiedad",
      icon: "home",
      action() {
        router.visit("/properties/create");
      },
    },
    {
      label: "Crear un contrato",
      icon: "document",
      action() {
        router.visit("/rents/create");
      },
    },
    {
      label: "Gasto de propiedad",
      icon: "document",
      action() {
        openInvoiceModal({
          data: { type: "lender " },
          isOpen: true,
        });
      },
      extended: true,
    },
    {
      label: "Otros",
      extended: true,
    },
    {
      label: "Distribucion a propietario",
      icon: "document",
      action() {
        router.visit("/rents/create");
      },
      extended: true,
    },
  ];

  return options.filter((option) => props.extended || !option.extended);
});

const { openTransactionModal } = useTransactionModal();
</script>

<template>
  <div class="grid py-2" :class="[extended ? 'grid-cols-3' : 'grid-cols-2']">
    <template v-for="card in welcomeCards">
      <button
        v-if="card.action"
        class="flex flex-col items-center hover:text-primary justify-center w-full py-3 text-center transition-all ease-in bg-white border-2 border-transparent rounded-lg hover:border-primary group"
        :class="[extended ? 'text-body-1/50' : 'text-primary']"
        @click="card.action()"
      >
        <IMdiUserOutline class="text-4xl" v-if="card.icon == 'contact'" />
        <IMdiMoney class="text-4xl" v-if="card.icon == 'money'" />
        <IMdiHomeCityOutline class="text-4xl" v-if="card.icon == 'home'" />
        <IMdiFileDocument class="text-4xl" v-if="card.icon == 'document'" />
        <IMdiCalculator class="text-4xl" v-if="card.icon == 'calculator'" />

        <p class="text-sm font-bold text-body-1/50 group-hover:text-primary">
          {{ card.label }}
        </p>
      </button>
      <h4 v-else class="col-span-3 mt-2 text-secondary">
        {{ card.label }}
      </h4>
    </template>
  </div>
</template>
