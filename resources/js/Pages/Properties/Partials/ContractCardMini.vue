<script lang="ts" setup>
import { formatMoney, formatDate } from "@/utils";
import { Link, router } from "@inertiajs/vue3";
import AppButton from "../../../Components/shared/AppButton.vue";
import { IRent } from "@/Modules/properties/propertyEntity";

defineProps<{
  contract: IRent;
}>();
</script>

<template>
  <section class="p-4 border rounded-md shadow-md bg-base-lvl-3">
    <header class="flex justify-between">
      <h4 class="font-bold text-body">
        <Link :href="`/rents/${contract.id}`">
          Contrato {{ contract.status }}: {{ contract.client?.fullName }}
        </Link>
      </h4>
      <section class="space-x-2">
        <AppButton variant="inverse">Editar terminos</AppButton>
        <AppButton
          variant="inverse"
          v-if="contract.status !== 'CANCELLED'"
          @click="
            router.visit(
              `/contacts/${contract.client_id}/tenants/rents/${contract.id}/end`
            )
          "
        >
          Terminar Contrato
        </AppButton>
        <AppButton
          variant="neutral"
          class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
          @click="deleteRent(row)"
        >
          <IMdiTrash />
        </AppButton>
      </section>
    </header>
    <article>
      <p>Fecha de inicio {{ formatDate(contract.date) }}</p>
      <p>Proximo Pago: {{ formatDate(contract?.next_invoice_date) }}</p>
      <p>Renta recolectada: {{ formatMoney(contract?.paid ?? 0) }}</p>
      <p>Comision recolectada: {{ formatMoney(contract?.commission_paid ?? 0) }}</p>
      <p>Total: {{ contract?.total }}</p>
    </article>
  </section>
</template>
