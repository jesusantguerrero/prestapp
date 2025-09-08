<script setup lang="ts">
import { AtInput, AtTextarea } from "atmosphere-ui";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";

import AppFormField from "@/Components/shared/AppFormField.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { IUnit, IProperty, propertyLabel } from "@/Modules/properties/propertyEntity";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

const props = defineProps<{
  unit: IUnit;
  property: IProperty;
}>();

const selectedProperty = ref<IProperty | null>(null);
const showTransferSection = ref(false);

const handleTransferUnit = () => {
  if (selectedProperty.value) {
    router.post(`/properties/${props.property.id}/units/${props.unit.id}/transfer`, {
     new_property_id: selectedProperty.value?.id
    }, {
      onSuccess: () => {
        showTransferSection.value = false;
        selectedProperty.value = null;
      }
    });
  }
};
</script>

<template>
  <section class="space-y-2">
    <section class="space-y-2" v-if="!showTransferSection">
      <section class="grid grid-cols-2 gap-4">
        <AppFormField class="w-full" label="Nombre de unidad">
          <AtInput v-model="unit.name" class="w-full" rounded placeholder="Appto #512" />
        </AppFormField>
        <AppFormField class="w-full" label="Precio de Renta">
          <AtInput v-model="unit.price" class="w-full" rounded number-format />
        </AppFormField>
      </section>
      <section class="grid grid-cols-3 gap-4">
        <AppFormField class="w-full" label="Area" v-model="unit.area" rounded>
          <template #prefix>
            <span class="inline-blocks h-full flex items-center px-2">
              <i-ic-sharp-photo-size-select-small />
            </span>
          </template>
        </AppFormField>
        <AppFormField class="w-full" :label="$t('rooms')" v-model="unit.bedrooms" rounded>
          <template #prefix>
            <span class="inline-blocks h-full flex items-center px-2">
              <IIcTwotoneBed />
            </span>
          </template>
        </AppFormField>
        <AppFormField
          class="w-full"
          label="Baños"
          v-model="unit.bathrooms"
          rounded
          placeholder="0"
        >
          <template #prefix>
            <span class="inline-blocks h-full flex items-center px-2">
              <IIcTwotoneBathtub />
            </span>
          </template>
        </AppFormField>
      </section>
      <AppFormField :label="$t('description')">
        <AtTextarea
          v-model="unit.description"
          class="w-full p-2 border focus:outline-none"
          :placeholder="$t('Notes, description, details...')"
        />
      </AppFormField>
    </section>

    <!-- Property Transfer Section -->
    <div class="pt-6 border-t border-neutral/10">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-medium">Transferencia de Unidad</h3>
        <AppButton
          variant="primary"
          class="btn-sm"
          @click="showTransferSection = !showTransferSection"
        >
          <IMdiSwapHorizontal class="w-5 h-5 mr-2" />
          {{ showTransferSection ? 'Cancelar' : 'Transferir Unidad' }}
        </AppButton>
      </div>

      <!-- Transfer Form -->
      <div v-if="showTransferSection" class="bg-neutral/5 rounded-lg p-4 space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <AppFormField label="Seleccionar Propiedad Destino" class="col-span-2">
            <BaseSelect
              v-model="selectedProperty"
              :endpoint="`/api/properties?filter[owner_id]=${property.owner_id}`"
              :placeholder="$t('select a property')"
              label="name"
              track-by="id"
              :custom-label="propertyLabel"
            />
          </AppFormField>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-neutral/10">
          <div class="text-sm text-body-2">
            <p class="flex items-center text-warning">
              <IMdiAlert class="w-5 h-5 mr-2" />
              Esta acción no puede ser revertida
            </p>
          </div>
          <AppButton
            variant="primary"
            attr-type="button"
            :disabled="!selectedProperty"
            @click="handleTransferUnit"
          >
            Confirmar Transferencia
          </AppButton>
        </div>
      </div>
    </div>
  </section>
</template>
