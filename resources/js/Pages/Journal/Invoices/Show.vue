<script setup>
import { router } from "@inertiajs/vue3";

import InvoiceSimple from "./printTemplates/Simple.vue";
import AppLayout from "../../../Components/templates/AppLayout.vue";
import AccountingSectionNav from "../Partials/AccountingSectionNav.vue";
import AppButton from "../../../Components/shared/AppButton.vue";

defineProps({
    invoice: {
        type: Object,
        required: true
    },
    businessData: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        required: true
    },
    invoiceTemplate: {
        type: String,
        default: 'invoice-simple'
    }
})
</script>


<template>
  <AppLayout :title="invoice.concept">
    <template #header>
      <AccountingSectionNav>
        <template #actions>
          <AppButton @click="router.visit(route('invoices.edit', invoice))" variant="inverse">Editar Factura</AppButton>
        </template>
      </AccountingSectionNav>
    </template>
    
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <component
            :is="InvoiceSimple"
            :type="type"
            :business-data="businessData"
            :invoice-data="invoice"
        />
    </div>
  </AppLayout>
</template>