<template>
    <AppLayout>
        <div class="h-auto py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-2 mb-10 border-4 border-white rounded-md bg-gray-50">
                <div class="px-5 font-bold text-gray-600">
                    Settings / Business
                </div>

                <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
                    <AtButton @click="save()" class="w-32" type="primary"> Save </AtButton>
                </div>
            </div>

            <div class="w-full px-5 py-10 space-y-5 bg-white divide-y divide-gray-200">
                <div class="pb-2">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Business Name</h2>
                        <AtInput placeholder="Business Name" v-model="formData.business_name"/>
                    </div>
                </div>

                <div class="w-full">
                    <div class="md:max-w-sm">
                        <h2 class="my-4 font-bold"> Business Address</h2>
                        <div class="flex space-x-4">
                            <div class="w-8/12">
                                <label for="" class="font-bold text-gray-500">Street</label>
                                <at-input type="text" v-model="formData.business_street"/>
                            </div>
                            <div class="w-4/12">
                                <label for="" class="font-bold text-gray-500">Apt/Unit</label>
                                <at-input type="text" v-model="formData.business_apt_unit"/>
                            </div>
                        </div>

                        <div class="flex mt-2 space-x-4">
                            <div class="w-8/12">
                                <label for="" class="font-bold text-gray-500">City</label>
                                <at-input type="text" v-model="formData.business_city"/>
                            </div>
                            <div class="w-4/12">
                                <label for="" class="font-bold text-gray-500">Zip Code</label>
                                <at-input type="text" v-model="formData.business_zip_code"/>
                            </div>
                        </div>

                        <div class="flex mt-2 space-x-4">
                            <div class="w-8/12">
                                <label for="" class="font-bold text-gray-500">Country</label>
                                <at-input type="text" v-model="formData.business_country"/>
                            </div>
                            <div class="w-4/12">
                                <label for="" class="font-bold text-gray-500">State</label>
                                <at-input type="text" v-model="formData.business_state"/>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="md:max-w-sm">
                            <h2 class="my-4 font-bold"> Business Phone number</h2>
                            <label for="" class="font-bold text-gray-500">Phone Number</label>
                            <at-input type="tel" v-model="formData.business_phone"/>
                    </div>
                </div>
                <div>
                <div class="md:max-w-sm">
                    <h2 class="my-4 font-bold"> Tax ID</h2>
                    <div class="flex space-x-4">
                        <div class="w-4/12">
                            <label for="" class="font-bold text-gray-500">Tax ID Label</label>
                            <at-input type="text" v-model="formData.business_tax_id_label"/>
                        </div>

                        <div class="w-8/12">
                            <label for="" class="font-bold text-gray-500">Tax ID Number</label>
                            <at-input type="text" v-model="formData.business_tax_id_number"/>
                        </div>
                    </div>
                </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script>
import { reactive, toRefs } from '@vue/reactivity'
import { AtButton, AtInput } from 'atmosphere-ui'
import axios from 'axios'
import { ElNotification } from 'element-plus'

import AppLayout from '../../Components/templates/AppLayout.vue'

export default {
    components: { AppLayout, AtInput, AtButton, AppLayout, AtButton, AtInput },
    props: {
        settingData: {
            type: Object,
            default() {
                return {}
            }
        }
    },
    setup(props) {
        const state = reactive({
            formData: {}
        })

        state.formData = {...state.formData,...props.settingData}

        const save = () => {
            axios({
                url: "/api/settings",
                method: "POST",
                data: state.formData
            }).then(() => {
                ElNotification({
                    title: "Business Data Updated",
                    message: "Business Data Updated",
                    type: 'success'
                })
            })
        }

        return {
            ...toRefs(state),
            save
        }
    }
}
</script>

<style>

</style>
