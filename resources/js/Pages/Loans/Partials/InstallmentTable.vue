<script setup>
import AppButton from "../../../Components/shared/AppButton.vue";
import { formatMoney } from "@/utils/formatMoney";

defineProps({
  installments: {
    type: Array,
  },
  loanId: {
    type: Number,
  },
});

const markAsPaid = (id) => {
  router.post();
};

const tdHeaderClass = "bg-blue-400 p-2 text-white";
</script>

<template>
  <table class="table w-full mt-4">
    <thead class="bg-blue-400 py-4 text-xl text-white">
      <td :class="tdHeaderClass">Fecha</td>
      <td :class="tdHeaderClass">Monto a Pagar</td>
      <td :class="tdHeaderClass">Abono a Capital</td>
      <td :class="tdHeaderClass">Interés</td>
      <td :class="tdHeaderClass">Balance deudor</td>
      <td :class="tdHeaderClass">Estado</td>
      <td :class="tdHeaderClass">Acciones</td>
    </thead>
    <tr v-for="installment in installments">
      <td class="p-2">{{ installment.due_date }}</td>
      <td class="p-2 text-right">{{ formatMoney(installment.amount) }}</td>
      <td class="p-2 text-right">{{ formatMoney(installment.principal) }}</td>
      <td class="p-2 text-right">{{ formatMoney(installment.interest) }}</td>
      <td class="p-2 text-right">{{ formatMoney(installment.final_balance) }}</td>
      <td class="p-2 text-right">{{ installment.payment_status }}</td>
      <td class="p-2">
        <AppButton> Pagar </AppButton>
      </td>
    </tr>
  </table>
</template>
