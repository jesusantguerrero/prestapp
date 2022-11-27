<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import AppSectionHeader from "../../Components/AppSectionHeader.vue";
import { ILoan } from "../../Modules/loans/loanEntity";

defineProps<{
  loans: ILoan[];
}>();
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
        <ElTable :data="loans">
          <ElTableColumn prop="contact.names" label="Nombres" />
          <ElTableColumn prop="contact.lastnames" label="Apellidos" />
          <ElTableColumn prop="amount" label="Monto Prestado" />
          <ElTableColumn prop="interest_rate" label="Interes" />
          <ElTableColumn prop="start_date" label="Fecha Inicio" />
        </ElTable>
      </section>
    </main>
  </AppLayout>
</template>
