<script setup lang="ts">
import { router, useForm } from "@inertiajs/vue3";
import { AtInput } from "atmosphere-ui";

import AppButton from "@/Components/shared/AppButton.vue";
import WelcomeWidget from "@/Components/WelcomeWidget.vue";

import { formatMoney, formatDate } from "@/utils";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import RentTemplate from "./Partials/RentTemplate.vue";
import { IRent } from "@/Modules/properties/propertyEntity";
import { ElMessageBox } from "element-plus";
import { parseISO } from "date-fns";
import { ref } from "vue";
import { useResponsive } from "@/utils/useResponsive";

import TaxTypeSelector from "@/Pages/Settings/TaxTypeSelector.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { computed } from "vue";

interface Props {
  rents: IRent;
  currentTab: string;
}

const props = defineProps<Props>();

const updateRentForm = useForm({
  next_invoice_date: parseISO(props.rents.next_invoice_date),
  amount: props.rents.amount,
})


const isEditing = ref<string|boolean>(false);

const toggleQuickEdit = (mode: string|boolean) => {
  if (!mode) {
    isEditing.value = false;
  } else if (isEditingDate.value && mode == 'next_invoice_date' && props.rents.next_invoice_date !== updateRentForm.next_invoice_date) {
      updateRentForm.put(route('rents.update', props.rents), {
        onSuccess() {
          router.reload();
          isEditing.value = false;
        }
      })
  } else if (isEditingAmount.value && mode == 'amount' && props.rents.amount !== updateRentForm.amount) {
      updateRentForm.put(route('rents.update', props.rents), {
        onSuccess() {
          router.reload();
          isEditing.value = false;
        }
      })
  } else {
    isEditing.value = mode;
  }
}

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

const { isMobile } = useResponsive();

const isEditingDate = computed(() => {
  return isEditing.value =='next_invoice_date'
})

const isEditingAmount = computed(() => {
  return isEditing.value =='amount'
})
</script>

<template>
  <RentTemplate :rents="rents" :current-tab="currentTab">
    <WelcomeWidget
      message="Detalles de contrato"
      class="w-full text-body-1"
      :vertical-header="isMobile"
    >
      <template #content>
        <article class="flex py-4">
          <section class="space-y-2 md:w-6/12">
            <p class="flex items-center space-x-2">
              <span> Mensualidad: </span>
              <span v-if="!isEditingAmount">
                {{ formatMoney(rents.amount) }}
              </span>
              <div class=" w-48 flex" v-else>
                <AtInput
                  class="form-control"
                  number-format
                  v-model="updateRentForm.amount"
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
            <section class="flex">
              <AppButton
                  @click="toggleQuickEdit('amount')"
                  :processing="isEditingAmount && updateRentForm.processing"
                  :disabled="updateRentForm.processing"
                  class="h-10 w-14 flex justify-center items-center"
                  :variant="isEditingAmount ? 'success' : 'neutral'">
                  <IMdiContentSaveCheck v-if="isEditingAmount" />
                  <IMdiEdit  v-else />
              </AppButton>
              <AppButton
                  v-if="isEditingAmount"
                  @click="toggleQuickEdit(false)"
                  :disabled="updateRentForm.processing"
                  class="h-10 w-14 flex justify-center items-center"
                  variant="neutral"
              >
                  X
              </AppButton>
            </section>
            </p>
            <p>
              Fecha de Inicio:
              {{ formatDate(rents.date) }}
            </p>
            <p>
              Contrato hasta:
              {{ formatDate(rents.end_date) }}
            </p>
            <p class="flex items-center">
              Proximo pago:
              <span v-if="!isEditingDate">
                {{ formatDate(rents.next_invoice_date) }}
              </span>
              <ElDatePicker v-else v-model="updateRentForm.next_invoice_date" size="large" class="ml-2" />
              <section class="flex">
                <AppButton
                  @click="toggleQuickEdit('next_invoice_date')"
                  class="h-10 w-14 flex justify-center items-center"
                  :processing="isEditingDate && updateRentForm.processing"
                  :disabled="updateRentForm.processing"
                  :variant="isEditingDate ? 'success' : 'neutral'"
               >
                <IMdiContentSaveCheck v-if="isEditingDate" />
                  <IMdiEdit class="ml-2" v-else />
                </AppButton>
                <AppButton
                v-if="isEditingDate"
                @click="toggleQuickEdit(false)"
                :disabled="updateRentForm.processing"
                class=" h-10 w-14 flex justify-center items-center"
                variant="neutral"
            >
                X
            </AppButton>
              </section>
            </p>
            <p>
              Estatus:
              {{ $t(`commons.${rents.status}`) }}
            </p>
            <p class="py-2 cursor-pointer hover:bg-base-lvl-1">
              Deposito {{ formatMoney(rents.deposit) }}
            </p>
          </section>
          <section class="space-y-2 md:w-6/12">
            <p class="flex items-center space-x-2">
                <AppFormField
                  label="Penalidad"
                  class="w-full"
                  disabled
                  row
                  v-model="rents.late_fee"
                >
                  <template #suffix>
                    <TaxTypeSelector v-model="rents.late_fee_type" disabled />
                  </template>
                </AppFormField>
            </p>
          </section>
        </article>
      </template>
      <template #actions>
        <section class="flex md:flex-row flex-col md:space-x-2 w-full">
          <AppButton
            variant="neutral"
            class="hover:bg-error hover:text-white w-full md:w-fit"
            v-if="rents.status !== 'CANCELLED'"
            @click="router.visit(`/contacts/${rents.client_id}/tenants/rents/${rents.id}/end`)"
          >
            <IMdiFileDocumentRemove class="mr-2" />
            Terminar Contrato
          </AppButton>
          <AppButton
            variant="neutral"
            class="hover:bg-success hover:text-white md:w-fit"
            v-if="rents.status !== 'CANCELLED'"
            @click="
              router.visit(`/contacts/${rents.client_id}/tenants/rents/${rents.id}/renew`)
            "
          >
            <IClarityContractLine class="mr-2" />
            <span class="capitalize">
              {{ $t("extend rent") }}
            </span>
          </AppButton>
          <AppButton
            v-if="rents.status !== 'CANCELLED'"
            variant="error"
            class="flex items-center w-full md:w-fit md:justify-center justify-start transition hover:text-error hover:border-red-400"
            @click="deleteRent(rents)"
          >
            <IMdiTrash class="mr-2"/>
            <span v-if="isMobile" class="capitalize">
              {{ $t("delete rent") }}
            </span>
          </AppButton>
        </section>
      </template>
    </WelcomeWidget>

    <WelcomeWidget message="Detalles de propiedad" class="w-full text-body-1">
      <template #content>
        <UnitTitle
          class="px-4 py-2 mt-4 bg-white rounded-md cursor-pointer hover:bg-white"
          :title="rents.address + ' ' + rents.unit?.name"
          :owner-name="rents.owner_name"
          :owner-link="`/contacts/${rents.property?.owner_id}/owners`"
          :tenant-name="formatMoney(rents.amount)"
        />
      </template>
    </WelcomeWidget>
  </RentTemplate>
</template>
