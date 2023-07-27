<template>
  <AppLayout title="Configuration / Theme">
    <template #header>
      <div class="flex items-center justify-end py-1 px-5">
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
          <AppButton variant="secondary" @click="save()"> Save </AppButton>
        </div>
      </div>
    </template>

    <main class="h-auto py-12 mx-auto">
      <div class="w-full px-5 py-4 space-y-5 bg-white divide-y divide-gray-200">
        <section class="pb-2 w-full">
          <article class="md:w-full">
            <h2 class="my-4 font-bold text-primary">{{ $t("Theme") }}</h2>
            <AppFormField
              class="w-4/12"
              :label="$t('Theme name')"
              v-model="formData.name"
            />
            <AppFormField
              class="w-4/12"
              :label="$t('description')"
              v-model="formData.description"
            />
          </article>
        </section>
        <section class="pb-2 w-full">
          <article class="md:w-full">
            <h2 class="my-4 font-bold text-primary">{{ $t("Colors") }}</h2>
            <section class="gap-1 grid grid-cols-3">
              <AppFormField
                class="w-4/12"
                :label="colorName"
                v-for="(_token, colorName) in themeColors"
              >
                <ColorPicker v-model="themeColors[colorName]" class="w-44" />
              </AppFormField>
            </section>
          </article>
        </section>
      </div>
    </main>
  </AppLayout>
</template>

<script lang="ts" setup>
import { ref } from "@vue/reactivity";
import axios from "axios";
import { ElNotification } from "element-plus";

import AppLayout from "../../Components/templates/AppLayout.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import ColorPicker from "@/Components/ColorPicker.vue";

const props = defineProps({
  settingData: {
    type: Object,
    default() {
      return {};
    },
  },
});
const formData = ref({
  name: "",
  description: "",
});

formData.value = {
  ...formData.value,
  ...props.settingData,
};

const themeColors = ref({
  primary: "",
  secondary: "#95b3f9",
  accent: "#7c5bbf",
  neutral: "#232130",
  "base-deep-1": "",
  "base-100": "",
  "base-lvl-1": "",
  "base-lvl-2": "",
  "base-lvl-3": "",
  info: "#3D68F5",
  success: "#79E7AE",
  warning: "#D39E17",
  error: "#F61909",
  body: "white",
  "body-1": "",
});

const save = () => {
  axios({
    url: "/api/settings",
    method: "POST",
    data: {
      ...formData.value,
      values: themeColors.value,
    },
  }).then(() => {
    ElNotification({
      title: "Business Data Updated",
      message: "Business Data Updated",
      type: "success",
    });
  });
};
</script>
