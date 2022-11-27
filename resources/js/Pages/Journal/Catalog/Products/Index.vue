<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-2 mb-10 border-4 border-white rounded-md bg-gray-50">
            <div class="px-5 font-bold text-gray-600">
                Products & Services
            </div>

            <div class="flex overflow-hidden font-bold text-gray-500 rounded-t-lg">
                <at-button  type="primary" class="w-full" @click="$inertia.visit('products/create')"> Add Product</at-button>
            </div>
        </div>

        <div class="w-full">
                <at-table
                    :cols="cols"
                    :tableData="products.data"
                    :show-prepend="true"
                >

                <!-- Table Data -->
                    <template v-slot:name="{ scope }">
                        <div class="flex space-x-5 items-center">
                            <div class="flex items-center justify-center h-20 font-bold text-gray-400 border border-gray-300 rounded-md w-28 bg-gray-50">
                                <img :src="`/storage/${scope.row.images[0].url}`" alt="" v-if="scope.row.images.length" style="min-width: 100%; min-height: 100%; object-fit: cover;">
                                <i class="text-xl fa fa-images" v-else></i>
                            </div>
                            <div>
                                <div class="font-bold capitalize"> {{ scope.row.name }} </div>
                                <div class="italic font-normal" v-if="scope.row.last_transaction_date"> Last transaction on: {{ scope.row.last_transaction_date.date }} </div>
                            </div>
                        </div>
                    </template>

                    <template v-slot:price="{ scope: { row } }">
                        <div class="font-bold" :class="{'text-red-500':  row.direction == 'WITHDRAW', 'text-green-500': row.direction == 'DEPOSIT'}">
                            {{ row.price ? row.price.value : "0.00" }}
                        </div>
                    </template>

                    <template v-slot:actions="{ scope: { row } }">
                        <div class="space-x-2">
                            <at-button @click="$inertia.visit(`/products/${row.id}`)" type="primary" class="inline-block">
                                <i class="fa fa-edit"></i>
                            </at-button>
                            <at-button @click="confirmDelete(row)" type="danger" class="inline-block">
                                <i class="fa fa-trash"></i>
                            </at-button>
                        </div>
                    </template>
                <!-- / Table data-->
            </at-table>
        </div>
    </div>
</template>

<script setup>
import AtTable from "@/Atmosphere/Atoms/Table/CustomTable"
import AppLayout from "@/Layouts/AppLayout.vue";
import cols from "./cols";
import { AtButton } from "atmosphere-ui";

defineProps({
    products: {
        type: Array,
    },
    categories: {
        type: Array,
    },
})
</script>
<style lang="scss" scoped>
.body-section {
    background: white;
    padding: 15px;
}

.el-table th {
    font-weight: bolder;
    color: #222 !important;
}

.section-actions {
    display: flex;

    .app-search__container {
        width: 80%;
        margin-right: 15px;
    }

    .action-buttons {
        width: 20%;
        display: flex;

        button {
            margin-left: auto;
        }
    }
}
</style>
