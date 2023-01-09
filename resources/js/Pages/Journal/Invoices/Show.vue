<script setup>
import { Link, router } from "@inertiajs/vue3";
import { AtField } from "atmosphere-ui";

import InvoiceSimple from "./printTemplates/Simple.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import InvoicePaymentOptions from "@/Components/templates/InvoicePaymentOptions.vue";
import AccountingSectionNav from "../Partials/AccountingSectionNav.vue";
import AppButton from "@/Components/shared/AppButton.vue";

import { formatDate, formatMoney } from "@/utils";
import { getInvoiceTypeUrl } from "./utils";

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
          <AppButton @click="router.visit(getInvoiceTypeUrl(invoice))" variant="inverse">
            Editar Factura
          </AppButton>
          <AppButton @click="router.visit(route('invoices.create', invoice))" variant="inverse">
            Crear otra factura
          </AppButton>
          <InvoicePaymentOptions :invoice="invoice" />
        </template>
      </AccountingSectionNav>
    </template>

    <div class="py-10 mx-auto space-y-4 max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between px-5 py-1 border rounded-md space bg-base-lvl-3">
          <section class="flex space-x-4">
            <AtField label="Estatus">
                <p class="text-xl">
                  {{ invoice.status }}
                </p>
            </AtField>
            <AtField label="Cliente">
                <p class="text-xl font-bold text-primary">
                  {{ invoice.client.display_name }}
                </p>
            </AtField>
          </section>

          <section class="flex space-x-4">
            <AtField label="Monto Adeudado">
                <p class="text-xl">
                  {{ formatMoney(invoice.debt) }}
                </p>
            </AtField>
            <AtField label="Fecha limite">
                <p class="text-xl font-bold text-primary">
                  {{ formatDate(invoice.due_date) }}
                </p>
            </AtField>
          </section>
        </div>

        <div class="flex justify-between px-5 py-1 border rounded-md space bg-base-lvl-3">
          <section>
            Detalles de creacion
            <Link :href="`/transactions/${invoice.transaction.id}`" class="underline cursor-pointer text-primary underline-offset-1">Asiento Contable</Link>
          </section>
          <section>
            Detalles de envio
          </section>
          <section>
            Detalles de cobro
            <p>
              <span>Deuda: {{ formatMoney(invoice.debt) }} — Recibir pago manual </span>
              <div v-for="payment in invoice.payments">
                Recibido en {{ payment.date }}
              </div>
            </p>
          </section>
        </div>

        <component
            :is="InvoiceSimple"
            :type="type"
            :business-data="businessData"
            :invoice-data="invoice"
        />
    </div>
  </AppLayout>
</template>
