<script setup lang="ts">
import { router } from "@inertiajs/vue3";

import AppButton from "@/Components/shared/AppButton.vue";

import { formatMoney } from "@/utils";
import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { IProperty, IUnit } from "@/Modules/properties/propertyEntity";
import { ElMessageBox, ElNotification } from "element-plus";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
import UnitTag from "@/Components/realState/UnitTag.vue";

const props = defineProps<{
  unit: IUnit;
  property: IProperty;
}>();

type IPaymentMetaData = ILoanInstallment & {
  installment_id?: number;
};

const removeUnit = async (unit: IUnit) => {
  const isConfirmed = await ElMessageBox.confirm(
    `Estas seguro de eliminar la unidad ${unit.name}?`,
    "Eliminar unidad"
  );

  if (!isConfirmed) return;
  router.delete(`/properties/${props.properties.id}/units/${unit.id}`, {
    onSuccess() {
      ElNotification({
        message: `Unidad ${unit.name} borrada con exito`,
        title: "Unidad eliminada",
        type: "success",
      });
    },
  });
};

const handleContractClick = (unit: IUnit) => {
  const url = unit.contract
    ? `/rents/${unit.contract?.id}`
    : `/rents/create?unit=${unit.id}`;
  router.visit(url);
};
</script>

<template>
  <div class="flex w-full rounded-md px-4 py-2 justify-between">
    <header>
      <UnitTitle
        :title="unit.name"
        :owner-name="property.owner.display_name"
        :tenant-name="unit.contract?.client.display_name"
        :price="formatMoney(unit.price) as string"
      />
      <section class="flex mt-2 space-x-2 text-gray-500">
        <span class="flex items-center space-x-1">
          <i-ic-sharp-photo-size-select-small />
          <span>
            {{ unit.area ?? 0 }}
          </span>
        </span>
        <span class="flex items-center space-x-1"
          ><IIcTwotoneBed />
          <span>
            {{ unit.bedrooms ?? 0 }}
          </span>
        </span>
        <span class="flex items-center space-x-1"
          ><IIcTwotoneBathtub />
          <span>
            {{ unit.bathrooms ?? 0 }}
          </span>
        </span>
      </section>
    </header>
    <div class="flex items-center space-x-2">
      <UnitTag :status="unit.status" />
      <div class="flex">
        <ElTooltip :content="$t('Go to contract')">
          <AppButton variant="neutral" @click="$emit('contract-clicked', unit)">
            <IMdiFile />
          </AppButton>
        </ElTooltip>
        <ElTooltip :content="$t('Edit unit')">
          <AppButton variant="neutral" @click="$emit('edit', unit)">
            <IMdiEdit />
          </AppButton>
        </ElTooltip>
        <ElTooltip :content="$t('Remove unit')">
          <AppButton
            variant="neutral"
            class="hover:bg-error/80 hover:text-white transition"
            @click="$emit('delete', unit)"
          >
            <IMdiTrash />
          </AppButton>
        </ElTooltip>
      </div>
    </div>
  </div>
</template>
