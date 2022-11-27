<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <app-header
            name="invoice"
            :resource="invoice"
            extract-title="concept"
            @saved="saveForm(true)"
        />

        <component
            :is="InvoiceSimple"
            :type="type"
            :business-data="businessData"
            :invoice-data="invoice"
        />
    </div>
</template>

<script setup>

import AppHeader from '@/Atmosphere/Organisms/AppHeader'
import InvoiceSimple from "@/Atmosphere/Templates/invoices/Simple.vue";

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
