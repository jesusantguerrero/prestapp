<script setup>
import { formatMoney, formatDate } from "@/utils";
import { Link } from "@inertiajs/vue3";
import AppButton from "../../../Components/shared/AppButton.vue";

defineProps({
  contract: {
    type: Object
  }
});
</script>

<template>
  <section class="p-4 border rounded-md shadow-md bg-base-lvl-3">
    <header class="flex justify-between">
      <h4 class="font-bold text-body">
        <Link :href="`/rents/${contract.id}`">
          Contrato Activo: {{ contract.client?.fullName }}
        </Link>
      </h4>
      <section class="space-x-2">
        <AppButton variant="inverse">Editar terminos</AppButton>
        <AppButton variant="inverse">Terminar Contrato</AppButton>
      </section>
    </header>
    <article>
      <p>
        Fecha de inicio {{ formatDate(contract.date) }}
      </p>
      <p>
        Proximo Pago: {{ formatDate(contract?.next_invoice_date) }}
      </p>
      <p>
        Renta recolectada: {{ formatMoney(contract?.paid) }}
      </p>
      <p>
        Comision recolectada: {{ formatMoney(contract?.commission_paid) }}
      </p>
      <p>
        Total: {{ contract?.total }}
      </p>
    </article>
  </section>
</template>