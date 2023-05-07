<script lang="ts" setup>
import { ref } from "@vue/reactivity";
//@ts-expect-error: no types
import { AtButton, AtInput, AtSimpleSelect } from "atmosphere-ui";
import axios from "axios";
import { ElNotification } from "element-plus";
import { getLocales } from "@/plugins/i18n";
import AppLayout from "../../Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

const props = defineProps({
  settingData: {
    type: Object,
    default() {
      return {};
    },
  },
});
const formData = ref({
  region_language: "",
  region_timezone: "",
});

formData.value = { ...formData.value, ...props.settingData };

const save = () => {
  axios({
    url: "/api/settings",
    method: "POST",
    data: {
      region_timezone:
        formData.value.region_timezone?.id ?? formData.value.region_timezone,
      region_language:
        formData.value.region_language?.id ?? formData.value.region_language,
    },
  }).then(() => {
    ElNotification({
      title: "Region Data Updated",
      message: "Region Data Updated",
      type: "success",
    });
  });
};

const getTimezones = () => {
  return Intl?.supportedValuesOf("timeZone")?.map((item: any) => ({
    name: item,
    id: item,
  }));
};
</script>

<template>
  <AppLayout title="Configuracion / Region">
    <template #header>
      <div class="flex items-center justify-end py-1 px-5">
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
          <AppButton variant="secondary" @click="save()"> Save </AppButton>
        </div>
      </div>
    </template>

    <main class="h-auto py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="w-full px-5 py-10 space-y-5 bg-white divide-y divide-gray-200">
        <div
          class="bg-white w-full divide-y divide-gray-200 space-y-5 px-5 py-10 rounded-md"
        >
          <div class="md:flex md:space-x-4 pb-2">
            <AppFormField class="md:max-w-sm" label="Currency">
              <AtInput placeholder="Currency name" v-model="formData.region_currency" />
            </AppFormField>
            <AppFormField class="md:max-w-sm" label="Date Format">
              <AtInput placeholder="Date format" v-model="formData.region_date_format" />
            </AppFormField>
            <AppFormField class="md:max-w-sm" label="Number Format">
              <AtInput
                placeholder="Number format"
                v-model="formData.region_number_format"
              />
            </AppFormField>
          </div>

          <div class="md:flex space-x-4">
            <AppFormField label="Language">
              <BaseSelect
                placeholder="Select a language"
                track-by="id"
                label="name"
                :options="getLocales()"
                v-model="formData.region_language"
                size="large"
              />
            </AppFormField>

            <AppFormField label="Timezone">
              <BaseSelect
                placeholder="Select a timezone"
                track-by="id"
                label="name"
                :options="getTimezones()"
                v-model="formData.region_timezone"
                size="large"
              />
            </AppFormField>
          </div>
        </div>
      </div>
    </main>
  </AppLayout>
</template>
