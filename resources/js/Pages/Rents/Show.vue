<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import AppButton from "@/Components/shared/AppButton.vue";
import WelcomeWidget from "@/Pages/Dashboard/Partials/WelcomeWidget.vue";

import { formatMoney, formatDate } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import RentTemplate from "./Partials/RentTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";
import { ElMessageBox } from "element-plus";

interface Props {
  rents: IRent;
  currentTab: string;
}

defineProps<Props>();

const deleteRent = async (rent: IRent) => {
  const isValid = await ElMessageBox.confirm(
    `Estas seguro de eliminar el contrato de ${rent.address} ${rent.client_name}?`,
    "Eliminar contrato"
  );
  if (isValid) {
    router.delete(route("rents.destroy", rent), {
      onSuccess() {
        router.reload();
      },
    });
  }
};
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab">
    <WelcomeWidget message="Detalles de contrato" class="w-full text-body-1">
      <template #content>
        <section class="py-4 space-y-2">
          <p>
            Mensualidad:
            {{ formatMoney(rents.amount) }}
          </p>
          <p>
            Fecha de Inicio:
            {{ formatDate(rents.date) }}
          </p>
          <p>
            Contrato hasta:
            {{ formatDate(rents.end_date) }}
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
        <section class="flex">
          <AppButton
            variant="neutral"
            class="hover:bg-error hover:text-white"
            v-if="rents.status !== 'CANCELLED'"
            @click="router.visit(`/clients/${rents.client_id}/rents/${rents.id}/end`)"
          >
            <IMdiFileDocumentRemove class="mr-2" />
            Terminar Contrato
          </AppButton>
          <AppButton
            variant="error"
            class="hover:text-error transition items-center flex flex-col justify-center hover:border-red-400"
            @click="deleteRent(rents)"
          >
            <IMdiTrash />
          </AppButton>
        </section>
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
