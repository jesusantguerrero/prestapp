<template>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-10 border-4 border-white bg-gray-50 rounded-md py-2">
        <div class="px-5 text-gray-600 font-bold">
            Statements
        </div>

        <div class="flex text-gray-500 font-bold rounded-t-lg overflow-hidden max-w-min space-x-2">
            <jet-button @click="$inertia.visit('/statements/income')" class="w-28 text-center">
                Incomes
            </jet-button>
            <jet-button @click="$inertia.visit('/statements/expense')" class="w-28 text-center">
                Expenses
            </jet-button>
        </div>
    </div>
        <div class="w-full bg-white px-5 py-5 rounded-md shadow-md mb-10">
            <div class="items divide-y mt-10">
                <div v-for="(category, categoryName) in categories" :key="categoryName" class="flex justify-between py-2">
                    <div class="w-full">
                        <h4 class="text-xl font-bold"> {{ category.label }} </h4>
                        <p> {{ category.description }} </p>
                    </div>
                    <div class="w-full space-y-2 ">
                        <inertia-link :href="report.url" v-for="(report, reportName) in category.reports" :key="reportName" class="cursor-pointer bg-gray-50 transition hover:bg-gray-100 rounded-md px-5 py-4 block">
                            <h5 class="font-bold text-lg text-blue-500">
                                {{ report.label }}
                            </h5>
                            <p> {{ report.description }}</p>
                        </inertia-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import JetButton from "@/Jetstream/Button.vue";

defineProps({
    categories: {
        type: Array,
    },
})
</script>
