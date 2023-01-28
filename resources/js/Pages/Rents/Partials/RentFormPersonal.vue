<script setup lang="ts">
import { computed, toRefs } from "vue";
// @ts-expect-error
import { AtField, AtInput } from "atmosphere-ui";

import BaseSelect from "@/Components/shared/BaseSelect.vue";
import FormSection from "./FormSection.vue";
import { useReactiveForm } from "@/utils/useReactiveForm";

const props = defineProps<{
  modelValue: Record<string, any>;
}>();

const emit = defineEmits(["update:modelValue"]);
const { modelValue } = toRefs(props);

const { formData } = useReactiveForm(
  {
    property_id: null,
    property: null,
    unit_id: null,
    unit: null,
  },
  modelValue,
  emit
);

const clientToggleText = computed(() => {
  return formData.is_new_client ? "Lista de clientes" : "Nuevo cliente";
});
</script>

<template>
  <section>
    <FormSection>
      <div class="w-full mt-8">
        <header class="flex justify-between">
          <label for="">Inquilino</label>
          <button
            @click.prevent.stop="formData.is_new_client = !formData.is_new_client"
            class="text-primary underline underline-offset-2"
          >
            {{ clientToggleText }}
          </button>
        </header>
        <BaseSelect
          v-model="formData.client"
          endpoint="/api/clients?filter[is_tenant]=1"
          placeholder="Selecciona un inquilino"
          label="display_name"
          track-by="id"
          v-if="!formData.is_new_client"
        />

        <AtInput v-else v-model="formData.client_name" rounded />
      </div>

      <AtField label="Apellidos" class="w-full" v-if="formData.is_new_client">
        <AtInput v-model="formData.client_last_name" rounded />
      </AtField>
    </FormSection>

    <div v-if="formData.is_new_client" class="grid grid-cols-2 gap-2">
      <AtField label="Telefono" class="w-full">
        <AtInput v-model="formData.client_phone_number" rounded />
      </AtField>
      <AtField label="Email" class="w-full">
        <AtInput v-model="formData.client_email" rounded />
      </AtField>
      <AtField label="Cedula" class="w-full">
        <AtInput v-model="formData.client_dni" rounded />
      </AtField>
    </div>
  </section>
</template>
