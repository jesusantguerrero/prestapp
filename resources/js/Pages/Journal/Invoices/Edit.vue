<template>
  <AppLayout title="Factura">
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <AppSectionHeader
            :name="type"
            :resource="invoice"
            extract-title="concept"
            @saved="saveForm(true)"
        />

        <InvoiceTemplate
            ref="InvoiceTemplateForm"
            :is-editing="true"
            :type="type"
            :clients="clients"
            :products="products"
            :invoice-data="invoice"
            :available-taxes="availableTaxes"
        />
    </div>
  </AppLayout>
</template>

<script setup>
  import { provide, ref } from 'vue'

  import AppLayout from "@/Components/templates/AppLayout.vue";
  import AppSectionHeader from '@/Components/AppSectionHeader.vue';
  import InvoiceTemplate from "./Partials/InvoiceTemplate.vue";

  const props = defineProps([
      'invoice',
      'clients',
      'products',
      'categories',
      'availableTaxes',
      'type'
  ]);

  const InvoiceTemplateForm = ref(null);
  const saveForm = (isApplied) => {
      InvoiceTemplateForm.value.saveForm(isApplied);
  }

  provide('categories', props.categories);
</script>
