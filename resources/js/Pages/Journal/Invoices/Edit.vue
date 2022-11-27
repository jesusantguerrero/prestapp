<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <app-header
            :name="type"
            :resource="invoice"
            extract-title="concept"
            @saved="saveForm(true)"
        />

        <invoice-template
            ref="InvoiceTemplateForm"
            :is-editing="true"
            :type="type"
            :clients="clients"
            :products="products"
            :invoice-data="invoice"
            :available-taxes="availableTaxes"
        />
    </div>
</template>

<script>
    import { provide, ref } from 'vue'

    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import AppHeader from '@/Atmosphere/Organisms/AppHeader.vue'
    import InvoiceTemplate from "@/Atmosphere/Templates/InvoiceTemplate.vue";

    export default {
        props: [
            'invoice',
            'clients',
            'products',
            'categories',
            'availableTaxes',
            'type'
        ],

        components: {
            JetSectionBorder,
            JetButton,
            AppHeader,
            InvoiceTemplate
        },

        setup(props) {
            const InvoiceTemplateForm = ref(null);
            const saveForm = (isApplied) => {
                InvoiceTemplateForm.value.saveForm(isApplied);
            }

            provide('categories', props.categories);
            return {
                InvoiceTemplateForm,
                saveForm
            }
        }
    }
</script>
