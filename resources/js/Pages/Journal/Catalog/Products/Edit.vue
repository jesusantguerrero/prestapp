<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <app-header
            name="product"
            :resource="form"
            @saved="saveForm()"
            :is-editing="product && product.id"
            :title="product && product.name"
        />

        <form
            class="flex justify-center mx-auto space-x-5 mt-5"
            @submit.prevent="saveForm"
        >
            <progress
                v-if="formData.progress"
                :value="formData.progress.percentage"
                max="100"
            >
                {{ formData.progress.percentage }}%
            </progress>
            <div class="w-9/12 space-y-5">
                <div
                    class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                >
                    <div class="font-bold text-md">Upload images</div>
                    <el-upload
                        list-type="picture-card"
                        :on-change="handleImageUpload"
                        :file-list="formData.images"
                        :auto-upload="false"
                    >
                        <i class="el-icon-plus"></i>
                    </el-upload>
                </div>

                <div
                    class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                >
                    <div class="flex space-x-2">
                        <div class="w-6/12">
                            <label for="" class="block">Name</label>
                            <at-input
                                v-model="formData.name"
                                class="font-bold"
                                placeholder="Project name"
                            />
                        </div>
                        <div class="w-3/12">
                            <label for="" class="block">SKU</label>
                            <at-input
                                v-model="formData.sku"
                                class=""
                                placeholder="Project description"
                            />
                        </div>
                        <div class="w-3/12">
                            <label for="" class="block">Weight</label>
                            <at-input
                                v-model="formData.weight"
                                class=""
                                placeholder="CODE"
                            />
                        </div>
                    </div>
                    <div class="w-full">
                        <label for="" class="block">Description</label>
                        <AtTextarea
                            name=""
                            id=""
                            cols="30"
                            rows="5"
                            v-model="formData.description"
                            class="w-full rounde"
                        />
                    </div>
                </div>

                <div class="flex space-x-2">
                    <div
                        class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                    >
                        <div class="font-bold text-md">Add ribborn</div>
                        <div class="w-full">
                            <label for="" class="block">Ribborn</label>
                            <at-input
                                v-model="formData.ribborn"
                                class="font-bold"
                                placeholder=""
                            />
                        </div>
                    </div>

                    <div
                        class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                    >
                        <div class="font-bold text-md">Add subtitle</div>
                        <div class="w-full">
                            <label for="" class="block">Subtitle</label>
                            <at-input
                                v-model="formData.subtitle"
                                class="font-bold"
                                placeholder=""
                            />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="w-3/12 space-y-5">
                <pricing-card
                    v-model:price="formData.price.value"
                    :price-data="formData.price"
                    :price-list="formData.price_list"
                />

                <div class="space-y-2">
                    <AtField label="Taxes">
                        <NSelect v-model:value="formData.taxes" :options="state.taxes" multiple size="large" />
                    </AtField>
                    <AtField label="Income Account">
                        <NSelect v-model:value="formData.incomeAccount" size="large" />
                    </AtField>
                    <AtField label="Expense Account">
                        <NSelect v-model:value="formData.expenseAccount" size="large" />
                    </AtField>
                </div>

                <div
                    class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                >
                    <div class="font-bold text-md">Product Availability</div>
                    <div class="w-full">
                        <el-switch
                            v-model="formData.available"
                            class="font-bold"
                            placeholder=""
                        />
                    </div>
                </div>
                <div
                    class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
                >
                    <div class="font-bold text-md">Stock Control</div>
                    <div class="w-full">
                        <at-input
                            v-model="formData.stock"
                            class="font-bold"
                            placeholder=""
                        />
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout";
import AppHeader from "@/Atmosphere/Organisms/AppHeader";
import { useForm } from "@inertiajs/inertia-vue3";
import { ElUpload, ElSwitch } from "element-plus";
import { AtInput, AtTextarea, AtField } from "atmosphere-ui";
import { NSelect } from "naive-ui";
import PricingCard from "./PricingCard.vue";
import { watch, reactive, computed } from "vue";

const props = defineProps({
    product: {
        type: Object,
        default: () => ({}),
    },
    availableTaxes: {
        type: Array,
        default: () => [],
    },
});

const state = reactive({
    taxes: computed(() => {
        return props.availableTaxes.map((tax) => {
            return {
                value: tax.id,
                label: tax.name,
            };
        });
    })
})

const formData = useForm({
    id: null,
    name: "",
    slug: "",
    sku: "",
    description: "",
    images: [],
    price: {
        value: 0,
    },
    taxes:[],
    available: true,
    price_list: []
});

watch(
    () => props.product,
    (product) => {
        if (product) {
            Object.keys(formData.data()).forEach((key) => {
                formData[key] = product[key];
            });
        }
    },
    {
        immediate: true
    }
);

const handleImageUpload = (file, fileList) => {
    formData.images = fileList;
};

// api calls
const saveForm = () => {
    const param = formData.id ? `/${formData.id}` : "";
    const method = formData.id ? "put" : "post";

    formData.transform((data) => ({
        _method: method,
        ...data,
    })).post(`/products${param}`);
};
</script>
