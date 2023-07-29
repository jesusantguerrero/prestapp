<script setup lang="ts">
import {  useForm } from "@inertiajs/vue3";
import { AtInput } from "atmosphere-ui";

import WelcomeWidget from "@/Components/WelcomeWidget.vue";

import { formatMoney, formatDate } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import OrderTemplate from "./Partials/OrderTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";
import { ref } from "vue";

defineProps<{
  orders: IRent;
  currentTab: string;
}>();

const updateRentForm = useForm({
  date: "",
});

const isEditing = ref(false);

const toggleQuickEdit = () => {};
</script>

<template>
  <OrderTemplate :rents="orders" :current-tab="currentTab">
    <WelcomeWidget :message="$t('items')" class="w-full text-body-1">
      <template #content>
        <section class="py-4 space-y-2">
          <p class="flex items-center space-x-2">
            <span> Mensualidad: </span>
            <div class=" w-48">
              <AtInput
                class="form-control"
                number-format
                @update:model-value="orders.amount = $event"
                :model-value="orders.amount"
                rounded
                required
                borderless
              >
              <template #prefix>
                <div class="flex items-center">
                  DOP
                </div>
              </template>
            </AtInput>
            </div>
          </p>
          <p>
            Fecha de Inicio:
            {{ formatDate(orders.date) }}
          </p>
          <p>
            Contrato hasta:
            {{ formatDate(orders.end_date) }}
          </p>
          <p class="flex items-center">
            Proximo pago:
            <span v-if="!isEditing">
              {{ formatDate(orders.next_invoice_date) }}
            </span>
            <ElDatePicker v-else v-model="updateRentForm.next_invoice_date" size="large" class="ml-2" />
            <button
              @click="toggleQuickEdit"
              :disabled="updateRentForm.processing"
              class="mr-4  h-10 w-14 flex justify-center items-center"
            :class="[isEditing && 'bg-success text-white border-l-0 border hover:bg-success/80 transition']">
              <IMdiEdit class="ml-2" v-if="!isEditing" />
              <IMdiContentSaveCheck v-else />
            </button>
          </p>
          <p>
            Estatus:
            {{ $t(`commons.${orders.status}`) }}
          </p>
          <p class="py-2 cursor-pointer hover:bg-base-lvl-1">
            Deposito {{ formatMoney(orders.deposit) }}
          </p>
        </section>
      </template>
    </WelcomeWidget>

    <WelcomeWidget message="Detalles de propiedad" class="w-full text-body-1">
      <template #content>
        <UnitTitle
          class="px-4 py-2 mt-4 bg-white rounded-md cursor-pointer hover:bg-white"
          :title="orders.address + ' ' + orders.unit?.name"
          :owner-name="orders.owner_name"
          :owner-link="`/contacts/${orders.property?.owner_id}/owners`"
          :tenant-name="formatMoney(orders.amount)"
        />
      </template>
    </WelcomeWidget>
  </OrderTemplate>
</template>
