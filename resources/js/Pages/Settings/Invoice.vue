<template>
  <AppLayout title="Configuracion / Facturas">
    <template #header>
      <div class="flex items-center justify-between px-5 border-4 border-white rounded-md">
        <div class="flex items-center space-x-2">
            
        </div>
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
            <AppButton variant="inverse" @click="save()" class="w-32"> 
              Save 
            </AppButton>
        </div>
    </div>
    </template>

    <main class="w-full h-auto px-5 py-10 mx-auto mt-12 space-y-5 bg-white divide-y divide-gray-200 rounded-md sm:px-6 lg:px-8">
        <article class="w-full pb-2">
            <h2 class="font-bold"> Comisiones </h2>
            <template
                v-for="(tax, index) in taxesDefinition" :key="`taxt-${index}`">
                <TaxDefinitionBox
                    class="w-full"
                    v-model="taxesDefinition[index]"
                    @remove="removeTax(tax, index)"
                />
            </template>
            <AtButton @click="addTax" rounded class="flex items-center mt-2 text-gray-400 border cursor-pointer hover:bg-gray-100">
                <IconAdd class="w-4 h-4 mr-2" />
                Agregar Comision
            </AtButton>
        </article>

        <article class="w-full">
            <div class="md:max-w-sm">
                <h2 class="my-4 font-bold"> Preferencias De Pago</h2>
                <AtField class="" label="Limite De Pago">
                    <AtSimpleSelect
                        v-model="formData.invoice_payment_due_days"
                        :options="[{
                            name: 1,
                            label: 'Uppon receipt'
                        }, {
                            name: 7,
                            label: '7 Days'
                        },
                        {
                            name: 15,
                            label: '15 Days'
                        },
                        {
                            name: 30,
                            label: '30 Days'
                        },
                        {
                            name: 45,
                            label: '45 Days'
                        }
                        ]"
                    />
                </AtField>
                <AtField class="mt-5" label="Aceptar pagos online">
                    <el-switch  v-model="formData.invoice_accept_online_payments" />
                </AtField>
            </div>
        </article>

        <article>
            <h2 class="my-4 font-bold"> Titulo de factura </h2>
            <p class="mb-2 text-sm text-gray-400 md:w-8/12">By default, all Invoices are labelled 'Invoice' followed by a number (e.g., "Invoice 1").
If your local law, language or occupation requires a different name for your invoices, you can set that here.</p>
                <AtField class="md:max-w-sm" label="Factura" rounded>
                    <AtInput  v-model="formData.invoice_label" placeholder="Factura" />
                </AtField>
                <AtField class="md:max-w-sm" label="Factura Compra" rounded>
                    <AtInput  v-model="formData.invoice_label" placeholder="Factura Compra" />
                </AtField>
        </article>

        <article class="w-full">
            <h2 class="my-4 font-bold"> Notas e Instrucciones de pago</h2>
            <AtTextarea v-model="formData.invoice_payment_instructions" class="border rounded-md"/>
        </article>

        <article>
            <AtField class="w-full" label="Texto de email">
                <AtTextarea v-model="formData.invoice_email_text" class="border rounded-md" />
            </AtField>
        </article>
    </main>
  </AppLayout>
</template>

<script setup>
import { ElNotification, ElSwitch } from "element-plus"
import { reactive, watch } from 'vue'
import axios from 'axios'
import { Inertia } from '@inertiajs/inertia'

// import TaxDefinitionBox from './TaxDefinitionBox.vue'
import AppLayout from "../../Components/templates/AppLayout.vue"
import TaxDefinitionBox from "./TaxDefinitionBox.vue"
import IconAdd  from "@/Components/icons/IconAdd.vue";
import AppButton from "../../Components/shared/AppButton.vue"
import { AtButton, AtField, AtInput, AtSimpleSelect, AtTextarea } from "atmosphere-ui"

const props = defineProps({
    settingData: {
        type: Object,
        default: () => ({
            invoice_label: 'Invoice',
            invoice_payment_due_days: 7,
            invoice_accept_online_payments: false,
            invoice_payment_instructions: '',
            invoice_email_text: ''
        })
    },
    taxes: {
        type: Array,
        default: () => []
    }
})

const formData = reactive({
    invoice_email_text: "",
    invoice_payment_instructions: "",
    invoice_label: "",
    invoice_payment_due_days: 1,
    ...props.settingData,
    invoice_accept_online_payments: Boolean(props.settingData.invoice_accept_online_payments),
})
const taxesDefinition = reactive([...props.taxes]);
watch(() => props.taxes, () => {
    taxesDefinition.splice(0, taxesDefinition.length, ...props.taxes);
});

const addTax = () => {
    taxesDefinition.push({
        name: '',
        rate: 0,
        description: '',
        type: 1,
        is_default: false
    })
}

const removeTax = (tax, index) => {
    if (!tax.id) {
        taxesDefinition.splice(index, 1);
    } else {
       axios({
            method: 'DELETE',
            url: `/api/taxes/${tax.id}`
        }).then(() => {
            taxesDefinition.splice(index, 1);
            ElNotification.success({
                title: 'Tax removed',
                message: 'Tax has been removed'
            })
        }).catch(() => {
            ElNotification.error({
                title: 'Error',
                message: 'Could not delete tax'
            })
       })
    }
}

const updateTaxes = () => {
    if (!taxesDefinition.length) {
        return new Promise((resolve) => resolve(true));
    }
    return axios.post('/api/taxes', taxesDefinition.filter(tax => tax.name && tax.rate)).then(response => {
        console.log(response)
    }).catch(error => {
        console.log(error)
    })
}

const updateSettingsData = () => {
    return axios({
        url: "/api/settings",
        method: "POST",
        data: formData
    })
}

const save = () => {
    Promise.allSettled([updateTaxes(), updateSettingsData()])
    .then(() => {
        Inertia.reload({
            preserveScroll: true
        })
        ElNotification({
            title: "Invoicing Data Updated",
            message: "Invoicing settings has being updated",
            type: 'success'
        })
    })
}
</script>

<style>
.el-switch {
    background: transparent !important;
    border: none !important;
}

</style>
