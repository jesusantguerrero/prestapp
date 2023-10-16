<template>
  <AppLayout title="Configuracion / Facturas">
    <template #header>
      <div class="flex items-center justify-between px-5 py-2 rounded-md">
        <div class="flex items-center space-x-2"></div>
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
          <AppButton variant="inverse" @click="save()"> Save </AppButton>
        </div>
      </div>
    </template>

    <main
      class="w-full h-auto px-5 py-10 mx-auto mt-12 space-y-5 bg-white divide-y divide-gray-200 rounded-md sm:px-6 lg:px-8"
    >
      <article class="w-full pb-2">
        <h2 class="font-bold capitalize">{{ $t("commissions") }}</h2>
        <template v-for="(tax, index) in taxesDefinition" :key="`taxt-${index}`">
          <TaxDefinitionBox
            class="w-full"
            v-model="taxesDefinition[index]"
            @remove="removeTax(tax, index)"
          />
        </template>
        <AtButton
          @click="addTax"
          rounded
          class="flex items-center capitalize mt-2 text-gray-400 border cursor-pointer hover:bg-gray-100"
        >
          <IconAdd class="w-4 h-4 mr-2" />
          {{ $t("add commission") }}
        </AtButton>
      </article>

      <article class="w-full">
        <div class="md:max-w-sm">
          <h2 class="my-4 font-bold">Preferencias De Pago</h2>
          <AtField class="" label="Limite De Pago">
            <AtSimpleSelect
              v-model="formData.invoice_payment_due_days"
              :options="[
                {
                  name: 1,
                  label: $t('Upon receipt'),
                },
                {
                  name: 7,
                  label: $t('7 Days'),
                },
                {
                  name: 15,
                  label: $t('15 Days'),
                },
                {
                  name: 30,
                  label: $t('30 Days'),
                },
                {
                  name: 45,
                  label: $t('45 Days'),
                },
              ]"
            />
          </AtField>
        </div>
      </article>

      <article>
        <AppFormField :label="$t('company logo')">
          <ProfilePhoto
            :photo-url="$page.props.user.current_team?.profile_photo_url"
            v-model:file="companyForm.file"
            :alt="formData.invoice_label"
            @deleted="deletePhoto"
          />
        </AppFormField>
        <h2 class="my-4 font-bold">{{ $t("Invoice title") }}</h2>
        <p class="mb-2 text-sm text-gray-400 md:w-8/12">
          By default, all Invoices are labelled 'Invoice' followed by a number (e.g.,
          "Invoice 1"). If your local law, language or occupation requires a different
          name for your invoices, you can set that here.
        </p>
        <AtField class="md:max-w-sm" label="Factura" rounded>
          <AtInput v-model="formData.invoice_label" placeholder="Factura" rounded />
        </AtField>
        <AtField class="md:max-w-sm" label="Factura Compra" rounded>
          <AtInput
            v-model="formData.purchase_label"
            placeholder="Factura Compra"
            rounded
          />
        </AtField>
      </article>

      <article class="w-full">
        <h2 class="my-4 font-bold">Notas e Instrucciones de pago</h2>
        <AtTextarea
          v-model="formData.invoice_payment_instructions"
          class="border rounded-md"
        />
      </article>
      <!--
        <article>
            <AtField class="w-full" label="Texto de email">
                <AtTextarea v-model="formData.invoice_email_text" class="border rounded-md" />
            </AtField>
        </article> -->
    </main>
  </AppLayout>
</template>

<script setup lang="ts">
import { ElNotification } from "element-plus";
import { reactive, watch, ref } from "vue";
import axios from "axios";
import { router, useForm } from "@inertiajs/vue3";

// import TaxDefinitionBox from './TaxDefinitionBox.vue'
import AppLayout from "../../Components/templates/AppLayout.vue";
import TaxDefinitionBox from "./TaxDefinitionBox.vue";
import IconAdd from "@/Components/icons/IconAdd.vue";
import AppButton from "../../Components/shared/AppButton.vue";
import { AtButton, AtField, AtInput, AtSimpleSelect, AtTextarea } from "atmosphere-ui";
import { useI18n } from "vue-i18n";
import AppFormField from "@/Components/shared/AppFormField.vue";
import ProfilePhoto from "@/Components/ProfilePhoto.vue";

const { t } = useI18n();
const props = defineProps({
  team: {
    type: Object,
  },
  settingData: {
    type: Object,
    default: () => ({
      invoice_label: "Invoice",
      purchase_label: "Purchase",
      invoice_payment_due_days: 7,
      invoice_accept_online_payments: false,
      invoice_payment_instructions: "",
      invoice_email_text: "",
    }),
  },
  taxes: {
    type: Array,
    default: () => [],
  },
});

const formData = reactive({
  invoice_email_text: "",
  invoice_payment_instructions: "",
  invoice_label: "",
  invoice_payment_due_days: 1,
  ...props.settingData,
  invoice_accept_online_payments: Boolean(
    props.settingData.invoice_accept_online_payments
  ),
});
const taxesDefinition = reactive([...props.taxes]);
watch(
  () => props.taxes,
  () => {
    taxesDefinition.splice(0, taxesDefinition.length, ...props.taxes);
  }
);

const addTax = () => {
  taxesDefinition.push({
    name: "",
    rate: 0,
    description: "",
    type: 1,
    is_default: false,
  });
};

const removeTax = (tax, index) => {
  if (!tax.id) {
    taxesDefinition.splice(index, 1);
  } else {
    axios({
      method: "DELETE",
      url: `/api/taxes/${tax.id}`,
    })
      .then(() => {
        taxesDefinition.splice(index, 1);
        ElNotification.success({
          title: t("Tax removed"),
          message: t("Tax has been removed"),
        });
      })
      .catch(() => {
        ElNotification.error({
          title: t("Error"),
          message: t("Could not delete tax"),
        });
      });
  }
};

const updateTaxes = () => {
  if (!taxesDefinition.length) {
    return new Promise((resolve) => resolve(true));
  }
  return axios
    .post(
      "/api/taxes",
      taxesDefinition.filter((tax) => tax.name && tax.rate)
    )
    .then((response) => {
      console.log(response);
    })
    .catch((error) => {
      console.log(error);
    });
};

const companyForm = useForm({
  file: null,
});

const deletePhoto = () => {
  companyForm.delete(route("team-profile-photo.destroy"), {
    preserveScroll: true,
    onSuccess: () => {
      companyForm.file = null;
    },
  });
};

const clearPhotoFileInput = () => {
  companyForm.file = null;
};

const updateCompanyInformation = () => {
  if (!companyForm.file) return;

  companyForm.post(route("team-profile-photo.update"), {
    errorBag: "updateProfileInformation",
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
};

const updateSettingsData = () => {
  return axios({
    url: "/api/settings",
    method: "POST",
    data: formData,
  });
};

const save = () => {
  Promise.allSettled([updateTaxes(), updateSettingsData()]).then(() => {
    router.reload({
      preserveScroll: true,
    });
    ElNotification({
      title: t("Invoicing Data Updated"),
      message: t("Invoicing settings has being updated"),
      type: "success",
    });

    updateCompanyInformation();
  });
};
</script>

<style>
.el-switch {
  background: transparent !important;
  border: none !important;
}
</style>
