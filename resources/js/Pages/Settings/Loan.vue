<script setup lang="ts">
import { ElNotification, ElSwitch } from "element-plus"
import { reactive, onMounted } from 'vue'
import axios from 'axios'
import { router} from '@inertiajs/vue3'

import AppLayout from "../../Components/templates/AppLayout.vue"

import AppButton from "../../Components/shared/AppButton.vue"
// @ts-expect-error: its my lib and doent come with ts
import {  AtField, AtInput, AtTextarea } from "atmosphere-ui"
import AccountSelect from "@/Components/shared/Selects/AccountSelect.vue"

const props = defineProps({
    settingData: {
        type: Object,
        default: () => ({
            loan_default_source_account: null,
            loan_grace_days: 7,
            loan_apply_late_fees: false,
            loan_payment_notes: '',
        })
    },
    taxes: {
        type: Array,
        default: () => []
    }
})

const formData = reactive({
    ...props.settingData,
})

onMounted(async () => {
  console.log(props.settingData);
  const accountId = props.settingData.loan_default_source_account
  if (accountId) {
    formData.loan_default_source_account = await axios.get(`/loan-accounts/${accountId}`).then(({data }) => {
      return data;
    });
  }
})

const submit = () => {
  axios({
        url: "/api/settings",
        method: "POST",
        data: {
          ...formData,
          loan_default_source_account: formData.loan_default_source_account?.id
        }
    })
    .then(() => {
        router.reload({
            preserveScroll: true
        })
        ElNotification({
            title: "Configuracion de prestamo guardada",
            message: "La configuracion de prestamo ha sido guardada correctamente",
            type: 'success'
        })
    })
}
</script>


<template>
  <AppLayout title="Configuracion / Prestamos">
    <template #header>
      <div class="flex items-center justify-between px-5 border-4 border-white rounded-md">
        <div class="flex items-center space-x-2">

        </div>
        <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
            <AppButton variant="inverse" @click="submit()" class="w-32">
              Guardar
            </AppButton>
        </div>
    </div>
    </template>

    <main class="w-full h-auto px-5 py-10 mx-auto mt-12 space-y-5 bg-white divide-y divide-gray-200 rounded-md sm:px-6 lg:px-8">
        <article class="w-full">
            <div class="md:max-w-sm">
                <h2 class="my-4 font-bold"> Preferencias de prestamo</h2>
                <AtField class="" label="Cuenta de origen">
                    <AccountSelect
                      v-model="formData.loan_default_source_account"
                    />
                </AtField>
                <AtField class="mt-5" label="Aplicar moras por defecto">
                    <ElSwitch  v-model="formData.loan_apply_late_fees" />
                </AtField>
            </div>
        </article>

        <article>
            <h2 class="my-4 font-bold"> Detalle de moras</h2>
            <AtField class="md:max-w-sm" label="Dias de gracia" rounded>
              <AtInput  v-model="formData.loan_grate_days" placeholder="0" rounded />
            </AtField>
            <AtField class="md:max-w-sm" label="Interes de mora" rounded>
              <AtInput  v-model="formData.loan_late_fee" placeholder="Interes de mora" rounded />
            </AtField>
        </article>

        <article class="w-full">
            <h2 class="my-4 font-bold"> Notas de ticket de pago</h2>
            <AtTextarea v-model="formData.loan_payment_notes" class="border rounded-md"/>
        </article>
    </main>
  </AppLayout>
</template>


<style>
.el-switch {
    background: transparent !important;
    border: none !important;
}

</style>
