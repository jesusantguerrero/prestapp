<template>
  <app-layout>
    <template #header>
      <app-header
        name="invoice"
        :resource="invoice"
        extract-title="concept"
        @saved="saveForm(true)"
      >
      </app-header>
    </template>

    <div class="flex space-x-5 max-w-7xl mx-auto py-10 sm:px-6 lg:px-8 justify-center">
      <invoice-template
        ref="InvoiceTemplateForm"
        :clients="clients"
        :products="products"
        :invoice-data="invoice"
      >
      </invoice-template>
    </div>
  </app-layout>
</template>

<script>
import { provide, ref } from "vue";

import AppLayout from "@/Components/templates/AppLayout.vue";
import JetSectionBorder from "@/Jetstream/SectionBorder.vue";
import JetButton from "@/Components/Button.vue";
import AppHeader from "@/Components/templates/AppHeader.vue";
import InvoiceTemplate from "../Invoices/Partials/InvoiceTemplate.vue";

export default {
  props: ["invoice", "clients", "products", "categories"],

  components: {
    AppLayout,
    JetSectionBorder,
    JetButton,
    AppHeader,
    InvoiceTemplate,
  },

  setup(props) {
    const InvoiceTemplateForm = ref(null);
    const saveForm = (isApplied) => {
      InvoiceTemplateForm.value.saveForm(isApplied);
    };

    provide("categories", props.categories);
    return {
      InvoiceTemplateForm,
      saveForm,
    };
  },
};
</script>
