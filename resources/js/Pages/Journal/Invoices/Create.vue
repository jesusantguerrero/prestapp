<template>
  <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <app-header
      name="invoice"
      :resource="invoice"
      extract-title="concept"
      @saved="saveForm(true)"
      class="rounded-b-none"
    />

    <invoice-template
      ref="InvoiceTemplateForm"
      :is-editing="true"
      :clients="clients"
      :products="products"
      :invoice-data="invoice"
      :available-taxes="availableTaxes"
    />
  </div>
</template>

<script setup>
import AppHeader from "@/Components/templates/AppHeader.vue";
import { provide, ref } from "vue";
import InvoiceTemplate from "./Partials/InvoiceTemplate.vue";

defineProps(["invoice", "clients", "products", "categories", "availableTaxes"]);

const InvoiceTemplateForm = ref(null);
const saveForm = (isApplied) => {
  InvoiceTemplateForm.value.saveForm(isApplied);
};

provide("categories", props.categories);
</script>
