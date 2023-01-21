<template>
    <app-layout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-auto py-12">

            <div class="flex justify-between items-center mb-10 border-4 border-white bg-gray-50 rounded-md py-2">
                <div class="px-5 text-gray-600 font-bold">
                    Settings / Region
                </div>
            </div>

            <div class="bg-white w-full divide-y divide-gray-200 space-y-5 px-5 py-10 rounded-md">
                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Currency </h2>
                        <at-input placeholder="Business Name" />
                    </div>
                </div>

                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Date Format </h2>
                        <at-input placeholder="Business Name" />
                    </div>
                </div>

                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Number Format</h2>
                        <at-input placeholder="Business Name" />
                    </div>
                </div>

                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Language </h2>
                        <at-input placeholder="Business Name" />
                    </div>
                </div>

                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Timezone </h2>
                        <at-input placeholder="Business Name"  v-model="formData.business_timezone"/>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { ElSwitch } from "element-plus"
import { reactive, toRefs } from 'vue'
import { AtTextarea } from "atmosphere-ui";

import AppLayout from '../../Layouts/AppLayout.vue'
import AtInput from '@/Atmosphere/Atoms/Input.vue'
import AtButton from '@/Atmosphere/Atoms/Button.vue'
import SimpleSelect from '@/Atmosphere/Molecules/SimpleSelect.vue'

export default {
  components: {
      AppLayout,
      AtInput,
      AtButton,
      SimpleSelect,
      CustomText,
      ElSwitch
    },
    setup() {

        const state = reactive({
            formData: {
                invoice_email_text: "",
                invoice_payment_instructions: "",
                invoice_label: "",
                invoice_payment_due_days: 1,
                invoice_accept_online_payments: false
            }
        })

        return {
            ...toRefs(state)
        }
    }

}
</script>

<style>
.el-switch {
    background: transparent !important;
    border: none !important;
}

</style>
