<script setup lang="ts">
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { AtBackgroundIconCard } from "atmosphere-ui";

import Modal from "@/Components/Modal.vue";
import AppLayout from "@/Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";
import IconMarker from "@/Components/icons/IconMarker.vue";
import IconCoins from "@/Components/icons/IconCoins.vue";
import IconPersonSafe from "@/Components/icons/IconPersonSafe.vue";
import PropertySectionNav from "@/Pages/Properties/Partials/PropertySectionNav.vue";

import ContractCard from "./Partials/ContractCard.vue";
import EmptyAddTool from "./Partials/EmptyAddTool.vue";
import UnitForm from "./Partials/UnitFormModal.vue";

import { formatMoney } from "@/utils";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { IProperty } from "@/Modules/properties/propertyEntity";
import { ElTag } from "element-plus";
import { clientInteractions } from "@/Modules/clients/clientInteractions";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import UnitTag from "@/Components/realState/UnitTag.vue";

export interface Props {
  properties: IProperty;
  currentTab: string;
}

const props = withDefaults(defineProps<Props>(), {
  currentTab: "summary",
});

const tabs = {
  summary: "Detalles",
  reports: "Reportes",
  contracts: "Contratos",
  clients: "Inquilinos",
  notes: "Notas",
  documents: "Documentos",
  settings: "Configuracion",
};

const propertyTitle = computed(() => {
  const address = props.properties.address.split(",");
  return address[0];
});
type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
};

const isPaymentModalOpen = ref(false);
const selectedPayment = ref<IPaymentMetaData | null>(null);
const onPayment = (installment: ILoanInstallment) => {
  selectedPayment.value = {
    ...installment,
    // @ts-ignore solve backend sending decimals as strings
    amount: parseFloat(installment.amount_due) || installment.amount,
    id: undefined,
    installment_id: installment.id,
  };

  isPaymentModalOpen.value = true;
};
const paymentConcept = computed(() => {
  return (
    selectedPayment.value &&
    `Pago ${props.properties.id} pago #${selectedPayment.value.installment_id}`
  );
});

const refresh = () => {
  router.reload();
};

const isUnitFormOpen = ref(false);
const addUnit = () => {
  isUnitFormOpen.value = true;
};
</script>

<template>
  <AppLayout :title="`Propiedades / ${propertyTitle}`">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton
            @click="router.visit(route('properties.edit', properties))"
            variant="inverse"
          >
            Editar
          </AppButton>
          <AppButton
            @click="router.visit(route('rents.create', { propertyId: properties.id }))"
            variant="inverse"
          >
            Nuevo Contrato
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mt-8">
      <AppSectionHeader
        name=""
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="properties"
        :title="propertyTitle"
        @create="router.visit('/properties/create')"
        hide-action
      />
      <header
        class="w-full px-5 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"
      >
        <section class="flex items-center justify-between py-4">
          <article>
            <p class="flex items-center space-x-2">
              <IconMarker />
              <span>
                {{ properties.address }}
              </span>
            </p>
            <p
              class="flex items-center space-x-2 cursor-pointer text-primary group"
              @click="router.visit(route('clients.show', properties.owner))"
            >
              <IconPersonSafe />
              <span class="group-hover:underline underline-offset-4">
                {{ properties.owner.fullName }}
              </span>
            </p>
          </article>
          <article class="flex space-x-5">
            <section>
              <div class="flex items-center">
                <IconCoins class="mr-2 text-yellow-600" />
                <span class="font-bold text-success">
                  {{ formatMoney(properties.price) }}
                </span>
              </div>
              <p class="text-bold text-body-1">Renta Mensual</p>
            </section>
            <ElTag> {{ properties.status }}</ElTag>
          </article>
        </section>
      </header>

      <section
        class="flex flex-col md:flex-row w-full space-x-8 rounded-t-none border-t-none"
      >
        <article class="w-full md:w-9/12 space-y-4">
          <section class="flex space-x-4">
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Balance de Cuenta"
              :value="formatMoney(0)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Balance de Pendiente"
              :value="formatMoney(0)"
            />
            <AtBackgroundIconCard
              class="w-full bg-white border h-28 text-body-1"
              title="Dias de mora"
              :value="0"
            />
          </section>

          <section class="space-y-4 shadow-md bg-base-lvl-3">
            <div
              v-for="unit in properties.units"
              class="flex w-full rounded-md px-4 py-2 justify-between"
            >
              <h4>
                <UnitTitle
                  :title="unit.name"
                  :owner-name="properties.owner.display_name"
                  :tenant-name="unit.contract?.client.display_name"
                  :price="formatMoney(unit.price)"
                />
              </h4>
              <div class="flex items-center space-x-2">
                <UnitTag :status="unit.status" />
                <div class="flex">
                  <AppButton variant="neutral"><IMdiFile /></AppButton>
                  <!-- <AppButton variant="neutral"><IMdiFile /></AppButton>
                  <AppButton variant="neutral"><IMdiFile /></AppButton> -->
                </div>
              </div>
            </div>
          </section>

          <ContractCard
            v-if="properties.contract"
            class="p-4 border rounded-md shadow-md bg-base-lvl-3"
            :contract="properties.contract"
          />
        </article>

        <article class="w-3/12 space-y-2 rounded-md shadow-md">
          <div class="px-5 py-10 text-gray-600 bg-gray-200 rounded-md">
            <div class="header">
              <h2 class="text-lg font-bold">Manejo de Propiedad</h2>
              <small>
                Private place for you and your internal team to manage this project</small
              >
            </div>

            <div class="mt-4 space-y-2">
              <section class="flex space-x-4">
                <AppButton
                  class="w-full"
                  @click="
                    clientInteractions.generateOwnerDistribution(properties.owner_id)
                  "
                >
                  Generar Pago a Dueño
                </AppButton>
              </section>
              <EmptyAddTool> Notes </EmptyAddTool>
              <EmptyAddTool> Imagenes </EmptyAddTool>
              <EmptyAddTool> Documentos </EmptyAddTool>
              <EmptyAddTool @click="addUnit()"> Agregar unidad </EmptyAddTool>
            </div>
          </div>
        </article>
      </section>

      <PaymentFormModal
        v-if="selectedPayment"
        v-model="isPaymentModalOpen"
        :payment="selectedPayment"
        :endpoint="`/loans/${properties.id}/installments/${selectedPayment.installment_id}/pay`"
        :due="selectedPayment.amount"
        :default-concept="paymentConcept"
        @saved="refresh()"
      />

      <Modal v-if="isUnitFormOpen" :show="isUnitFormOpen" @close="isUnitFormOpen = false">
        <UnitForm @close="isUnitFormOpen = false" :property="properties" />
      </Modal>
    </main>
  </AppLayout>
</template>
