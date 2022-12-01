<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import { ILoan } from "../../Modules/loans/loanEntity";
import { AtButton } from "atmosphere-ui";
import { computed } from "vue";

const props = defineProps<{
  loans: [ILoan[] | Object];
}>();

const listData = computed(() => {
  return Array.isArray(props.loans) ? props.loans : props.loans.data;
});
</script>

<template>
  <AppLayout title="Prestamos">
    <main class="p-5">
      <AppSectionHeader
        name="Prestamos"
        class="rounded-md"
        @create="router.visit('/loans/create')"
      />
      <section class="mt-4">
        <ElTable :data="listData">
          <ElTableColumn prop="client.names" label="Nombres" />
          <ElTableColumn prop="client.lastnames" label="Apellidos" />
          <ElTableColumn prop="amount" label="Monto Prestado" />
          <ElTableColumn prop="interest_rate" label="Interes" />
          <ElTableColumn prop="start_date" label="Fecha Inicio" />
          <ElTableColumn prop="payment_status" label="Estado" />
          <ElTableColumn>
            <AtButton> Edit</AtButton>
            <AtButton> Delete</AtButton>
          </ElTableColumn>
        </ElTable>
      </section>
    </main>
  </AppLayout>
</template>
