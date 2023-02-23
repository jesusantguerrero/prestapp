<template>
  <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
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

<script lang="ts" setup>
import { provide, ref } from "vue";
import InvoiceTemplate from "./Partials/InvoiceTemplate.vue";

defineProps(["invoice", "clients", "products", "categories", "availableTaxes"]);

const InvoiceTemplateForm = ref(null);
const saveForm = (isApplied) => {
  InvoiceTemplateForm.value.saveForm(isApplied);
};

provide("categories", props.categories);
</script>
