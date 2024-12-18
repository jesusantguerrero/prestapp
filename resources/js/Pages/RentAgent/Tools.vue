<script setup lang="ts">
import { Link, useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { AtBackgroundIconCard, AtInput } from "atmosphere-ui";

import AppButton from "@/Components/shared/AppButton.vue";
import WelcomeWidget from "@/Components/WelcomeWidget.vue";

import { formatMoney } from "@/utils/formatMoney";
import { useResponsive } from "@/utils/useResponsive";
import AppLayout from "@/Components/templates/AppLayout.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { IClient } from "@/Modules/clients/clientEntity";
import { watch } from "vue";

const { t } = useI18n();

const props = defineProps<{
  features: Record<string, boolean>
}>();

const formData = useForm({
  owner: null,
  owner_id: "",
  email: ''
});


watch(() => formData.owner, (owner: IClient) => {
  formData.email = owner.email;
  formData.owner_id = owner.id;
})

const onSubmit = () => {
  if (formData.processing) return;

  formData
    .transform((data) => {
      return {
        email: formData.email,
        owner_id: formData.owner?.id,
      };
    }).post(`/agents/tools/portal/${formData.owner_id}/send-link`, {
      onSuccess() {
        alert('Link sent');
      },
    });
}
</script>


<template>
  <AppLayout :title="t('Tools')">
    <main class="py-5 pt-0 mx-auto text-gray-500">
      <div class="mt-4 mb-4 md:mt-0">
        <h4 class="hidden md:inline-block font-bold text-xl">{{ $t("Client portal") }}</h4>
        <p>Envia a tus propietarios un enlace a su historial de facturas, desde alli podran descargar sus comprobantes y realizar pagos pendientes. Todo desde un solo lugar!</p>
      </div>
      <section class="md:flex md:space-x-2">
         <AppFormField class="w-full" :label="$t('owner')">
           <BaseSelect
             class="min-w-max"
             size="large"
             v-model="formData.owner"
             endpoint="/api/clients?filter[is_owner]=1"
             :placeholder="$t('select an owner')"
             label="display_name"
             track-by="id"
           />
         </AppFormField>
        <AppFormField class="w-full" :label="$t('email')"  v-model="formData.email"   required />

        <AppButton
          variant="inverse"
          class="capitalize"
          :processing="formData.processing"
          @click="onSubmit"
        >
          {{ $t("Send link") }}
        </AppButton>
      </section>
    </main>
  </AppLayout>
</template>

<style lang="scss">
@media (max-width: 1024px) {
  .dashboard-bank-accounts .text-3xl {
    font-size: 1em;
  }
}
</style>
