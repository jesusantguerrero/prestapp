<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ILoan } from "../../Modules/loans/loanEntity";

defineProps<{
  data: ILoan[];
}>();
</script>

<template>
  <AppLayout>
    <main class="p-5">
      <h1>Prestamos</h1>
      <div v-for="loan in data">
        {{ loan }}
      </div>
      <Link class="bg-blue-500 text-white rounded-md" href="/loans/create">
        Nuevo Prestamo
      </Link>
    </main>
  </AppLayout>
</template>
