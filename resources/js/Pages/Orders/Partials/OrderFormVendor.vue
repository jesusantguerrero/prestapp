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
    client: null,
    client_id: null,
  },
  modelValue,
  emit
);
</script>

<template>
  <section>
    <FormSection section-class="w-full -px-10">
      <div class="w-full mt-8">
        <header class="flex justify-between">
          <label for="">{{ $t("Vendor") }}</label>
        </header>
        <BaseSelect
          v-model="formData.client"
          tag
          :allow-create="true"
          endpoint="/api/clients"
          :placeholder="$t('Select a vendor')"
          label="display_name"
          track-by="id"
        />
      </div>

      <AtField label="Apellidos" class="w-full" v-if="formData.is_new_client">
        <AtInput v-model="formData.client_last_name" rounded />
      </AtField>
    </FormSection>
  </section>
</template>
