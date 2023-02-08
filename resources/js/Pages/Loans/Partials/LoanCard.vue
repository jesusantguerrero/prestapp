<script setup lang="ts">
import { ILoan } from "@/Modules/loans/loanEntity";
import { formatMoney } from "@/utils";

defineProps<{
  loan: ILoan;
}>();
</script>

<template>
  <div>
    <header>
      {{ loan.client?.fullName }}
    </header>
    <section class="flex text-sm justify-between mt-4">
      <div>
        Monto: <span class="font-bold">{{ formatMoney(loan.total) }}</span>
      </div>
      <div>
        Plazo: <span class="font-bold">{{ loan.repayment_count }}</span>
      </div>
      <div>
        <span class="font-bold">{{ loan.frequency }}</span>
      </div>
    </section>
  </div>
</template>
