<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import AppButton from "@/Components/shared/AppButton.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";

import { formatMoney, formatDate } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import RentTemplate from "../Partials/RentTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";

interface Props {
  rents: IRent;
  currentTab: string;
}

defineProps<Props>();
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab">
    <WelcomeWidget message="Detalles de contrato" class="w-full text-body-1">
      <template #content>
        <section class="py-4 space-y-2">
          <p>
            Mensualidad:
            {{ rents.amount }}
          </p>
          <p>
            Fecha de Inicio:
            {{ formatDate(rents.date) }}
          </p>
          <p>
            Proximo pago:
            {{ formatDate(rents.next_invoice_date) }}
          </p>
          <p>
            Estatus:
            {{ $t(`commons.${rents.status}`) }}
          </p>
          <p class="hover:bg-base-lvl-1 cursor-pointer py-2">
            Deposito {{ formatMoney(rents.deposit) }}
          </p>
        </section>
      </template>
      <template #actions>
        <AppButton
          variant="neutral"
          class="hover:bg-error hover:text-white"
          v-if="rents.status !== 'CANCELLED'"
          @click="router.visit(`/clients/${rents.client_id}/rents/${rents.id}/end`)"
        >
          <IMdiFileDocumentRemove class="mr-2" />
          Terminar Contrato
        </AppButton>
      </template>
    </WelcomeWidget>

    <WelcomeWidget message="Detalles de propiedad" class="w-full text-body-1">
      <template #content>
        <UnitTitle
          class="mt-4 hover:bg-white cursor-pointer px-4 py-2 bg-white rounded-md"
          :title="rents.address + ' ' + rents.unit?.name"
          :owner-name="rents.owner_name"
          :owner-link="`/contacts/${rents.property.owner_id}/owners`"
          :tenant-name="formatMoney(rents.amount)"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
