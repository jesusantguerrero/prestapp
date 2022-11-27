<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <app-header
            :name="state.sectionName"
            @create="$inertia.visit(`/${state.sectionName}/create`)"
            class="px-5"
        />

        <div class="w-full mt-8">
                <at-table
                    :cols="cols"
                    :tableData="invoices.data"
                    :show-prepend="true"
                >

                <!-- Table Data -->
                    <template v-slot:date="{ scope: { row } }">
                        <div>
                            <div class="font-bold text-blue-400"> {{ formatDate(row.date) }} </div>
                        </div>
                    </template>

                    <template v-slot:concept="{ scope: { row } }">
                        <div class="text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-md">
                            {{ row.concept }}
                            <span class="font-bold text-gray-300"> {{ row.series }} #{{ row.number }} </span>
                        </div>
                    </template>

                    <template v-slot:status="{ scope: { row } }">
                        <div class="font-bold capitalize">
                            {{ row.status }}
                        </div>
                    </template>

                    <template v-slot:total="{ scope: { row } }">
                        <div class="font-bold">
                            {{ formatMoney(row.total) }}
                        </div>
                    </template>

                    <template v-slot:debt="{ scope: { row } }" >
                        <div class="font-bold" :class="[row.debt > 0 ?  'text-red-500' : 'text-green-500']">
                            {{ formatMoney(row.debt) }}
                        </div>
                    </template>

                    <template v-slot:actions="{ scope: { row } }">
                        <div class="flex space-x-2">
                            <at-button @click="$inertia.visit(`${state.sectionName}/${row.id}/edit`)" class="w-8 h-8 text-gray-400 rounded-full hover:text-green-400"> <i class="fa fa-edit"></i> </at-button>
                            <at-button class="w-8 h-8 text-gray-400 rounded-full hover:text-red-400"> <i class="fa fa-trash"></i> </at-button>
                        </div>
                    </template>
                <!-- / Table data-->
            </at-table>
        </div>
    </div>
</template>

<script setup>
import AtTable from "@/Atmosphere/Atoms/Table/CustomTable"
import AppHeader from "@/Atmosphere/Organisms/AppHeader"
import cols from "./cols";
import formatMoney from "@/Atmosphere/utils/formatMoney";
import { formatDate } from "@/Atmosphere/utils/formatDate";
import { AtButton } from "atmosphere-ui"
import { reactive, computed } from "vue";

const props = defineProps({
    invoices: {
        type: Array,
    },
    type: {
        type: String,
    },
})

const state = reactive({
    sectionName: computed(() => {
        return `${props.type.toLowerCase()}s`
    }),
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
