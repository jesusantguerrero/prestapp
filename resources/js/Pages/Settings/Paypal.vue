<template>
    <app-layout>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 h-auto py-12">
            <div class="flex justify-between items-center mb-10 border-4 border-white bg-gray-50 rounded-md py-2">
                <div class="px-5 text-gray-600 font-bold">
                    Settings / Paypal Integration
                </div>

                <div class="flex text-gray-500 font-bold rounded-t-lg overflow-hidden max-w-min">
                    <at-button @click="save()" class="w-32" type="primary"> Save </at-button>
                </div>
            </div>

            <div class="bg-white w-full divide-y divide-gray-200 space-y-5 px-5 py-10">
                <div class="pb-2 space-y-5">
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Paypal Client Id</h2>
                        <at-input placeholder="" v-model="formData.paypal_client_id"/>
                    </div>
                    <div class="md:max-w-sm">
                        <h2 class="font-bold"> Paypal Client Id</h2>
                        <at-input type="password" v-model="formData.paypal_key" />
                    </div>
                </div>
            </div>

        </div>
    </app-layout>
</template>

<script>
import { reactive, toRef, toRefs } from '@vue/reactivity'
import axios from 'axios'
import { ElNotification } from 'element-plus'

import AppLayout from '../../Layouts/AppLayout.vue'
import AtInput from '@/Atmosphere/Atoms/Input.vue'
import AtButton from '@/Atmosphere/Atoms/Button.vue'

export default {
    components: { AppLayout, AtInput, AtButton },
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
