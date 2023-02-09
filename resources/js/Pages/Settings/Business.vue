<template>
  <AppLayout title="Configuracion / Empresa">
    <template #header>
      <div class="flex items-center justify-end py-1 px-5">
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
          <AppButton variant="secondary" @click="save()"> Save </AppButton>
        </div>
      </div>
    </template>

    <main class="h-auto py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="w-full px-5 py-10 space-y-5 bg-white divide-y divide-gray-200">
        <section class="pb-2 w-6/12">
          <article class="md:w-full">
            <h2 class="my-4 font-bold text-primary">Registro Legal</h2>
            <AppFormField label="Nombre de Empresa" v-model="formData.business_name" />
            <section class="flex space-x-4">
              <AppFormField
                class="w-4/12"
                label="Nombre registro legal"
                v-model="formData.business_tax_id_label"
                placeholder="RNC"
              />

              <AppFormField
                class="w-8/12"
                label="# Registro Legal"
                v-model="formData.business_tax_id_number"
              />
            </section>
          </article>
        </section>

        <section class="w-full flex space-x-4">
          <article class="md:w-full">
            <h2 class="my-4 font-bold text-primary">Detalles de direccion</h2>
            <section class="flex space-x-4">
              <AppFormField
                class="w-8/12"
                label="Calle"
                v-model="formData.business_street"
              />
              <AppFormField
                class="w-4/12"
                label="#"
                v-model="formData.business_apt_unit"
              />
            </section>

            <section class="flex space-x-4">
              <AppFormField
                class="w-8/12"
                label="Ciudad"
                v-model="formData.business_city"
              />
              <AppFormField
                class="w-4/12"
                label="Codigo Zip"
                v-model="formData.business_zip_code"
              />
            </section>

            <section class="flex mt-2 space-x-4">
              <AppFormField
                class="w-8/12"
                label="Pais"
                v-model="formData.business_country"
              />
              <AppFormField
                class="w-4/12"
                label="Provincia"
                v-model="formData.business_state"
              />
            </section>
          </article>
          <article class="md:w-full">
            <h2 class="my-4 font-bold text-primary">Detalles de contacto</h2>
            <section>
              <AppFormField
                label="Phone Number"
                type="tel"
                v-model="formData.business_phone"
              />
            </section>
          </article>
        </section>
      </div>
    </main>
  </AppLayout>
</template>

<script lang="ts" setup>
import { ref } from "@vue/reactivity";
import { AtButton, AtInput } from "atmosphere-ui";
import axios from "axios";
import { ElNotification } from "element-plus";

import AppLayout from "../../Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";

const props = defineProps({
  settingData: {
    type: Object,
    default() {
      return {};
    },
  },
});
const formData = ref({});

formData.value = { ...formData.value, ...props.settingData };

const save = () => {
  axios({
    url: "/api/settings",
    method: "POST",
    data: formData.value,
  }).then(() => {
    ElNotification({
      title: "Business Data Updated",
      message: "Business Data Updated",
      type: "success",
    });
  });
};
</script>
