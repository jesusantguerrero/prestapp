<template>
    <div class="h-auto py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
            class="flex items-center justify-between py-2 mb-10 border-4 border-white rounded-md bg-gray-50"
        >
            <div class="px-5 font-bold text-gray-600">
                Settings / Branding
            </div>

                <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
                <at-button @click="save()" class="w-32" type="primary"> Save </at-button>
            </div>
        </div>

        <div class="px-5 py-10 bg-white rounded-md">
            <h2 class="mb-5">
                Customize the appearance of your invoices, proposals, PayMe
                page and email communication.
            </h2>
            <div class="flex space-x-5">
                <div class="w-8/12">
                    <h4 class="font-bold">Preview</h4>

                    <div
                        class="w-full px-24 py-10 mb-10 bg-white border rounded-md shadow-md "
                    >
                        <div
                            class="flex flex-col items-center justify-center text-center pb-14"
                        >
                            <div class="w-24 h-24 bg-gray-300 rounded-md">
                                <img
                                    src=""
                                    alt=""
                                    width="100px"
                                    height="100px"
                                />
                            </div>
                            <p class="mt-4 font-bold capitalize">
                                Sample Email
                            </p>
                            <p class="mt-2 font-bold">
                                {{ businessData.business_name }}
                            </p>
                        </div>

                        <div
                            class="py-10 whitespace-pre border-t border-b px-14"
                        >
                            {{ body }}
                        </div>

                        <div class="text-center p-14">
                            <at-button class="text-white" :style="{background: formData.branding_color}">
                                Sample Button
                            </at-button>
                        </div>
                    </div>

                    <div class="bg-white border rounded-md shadow-md p-14">
                        <div>
                            {{ formData.business_name }} is powered by
                            <span class="font-bold">Neatlancer</span> for
                            FREE invoicing, time tracking, proposals,
                            contracts, and more.
                            <a
                                href="http://neatlancer.com"
                                class="mt-4 text-blue-400 underline"
                                :style="{color: formData.branding_color}"
                            >
                                Learn More
                            </a>
                        </div>
                    </div>
                </div>

                <div class="w-4/12">
                    <div class="pb-2">
                        <div class="md:max-w-sm">
                            <h2 class="font-bold">Color</h2>
                            <color-select
                                v-model="formData.branding_color"
                                class="w-full"
                            ></color-select>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="md:max-w-sm">
                            <h2 class="my-4 font-bold">Logo</h2>
                            <at-Button class="text-white" :style="{background: formData.branding_color}">
                                Remove Logo
                            </at-Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { reactive, toRefs } from '@vue/reactivity';
import { computed } from '@vue/runtime-core';

import AppLayout from "../../Layouts/AppLayout.vue";
import AtInput from "@/Atmosphere/Atoms/Input.vue";
import ColorSelect from "@/Atmosphere/Molecules/ColorSelect.vue";
import AtButton from "@/Atmosphere/Atoms/Button.vue";

export default {
    components: { AppLayout, AtInput, AtButton, ColorSelect },
    props: {
        user: {
            type: Object,
            required: true
        },
        settingData: {
            type: Object,
            default() {
                return {};
            },
        },
        businessData: {
            type: Object,
            default() {
                return {};
            },
        },
    },
    setup(props) {
        const state = reactive({
            body: computed(() => {
                return `Hi there 👋\n\nThanks for your business! \n\n- ${props.user.name}`
            }),
            formData: {
                branding_color: ['#9CA3AF'],
                branding_logo: ''
            }
        })

        state.formData = {...state.formData,...props.settingData}
        if (!Array.isArray(state.formData.branding_color)) {
            state.formData.branding_color = JSON.parse(state.formData.branding_color);
        }
        const save = () => {
            const formData = {...state.formData};


            formData.branding_color = JSON.stringify(state.formData.branding_color);

            axios({
                url: "/api/settings",
                method: "POST",
                data: formData
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
        };
    },
};
</script>

<style></style>
