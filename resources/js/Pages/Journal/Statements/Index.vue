<template>
  <AppLayout title="Reportes">
    <template #header>
      <div class="flex items-center justify-between mx-5 border-4 border-white rounded-md bg-gray-50">
        <div class="px-5 font-bold text-gray-600">
            
        </div>

        <div class="flex space-x-2 overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
            <AppButton variant="inverse" @click="$inertia.visit('/statements/income')" class="text-center w-28">
                Ingresos
            </AppButton>
            <AppButton variant="inverse" @click="$inertia.visit('/statements/expense')" class="text-center w-28">
                Egresos
            </AppButton>
        </div>
    </div>
    </template>

    <div class="w-full py-10 mx-auto mt-8 sm:px-6 lg:px-8">
        <div class="w-full px-5 py-5 mb-10 bg-white rounded-md shadow-md">
            <div class="mt-10 divide-y items">
                <div v-for="(category, categoryName) in categories" :key="categoryName" class="flex justify-between py-2">
                    <div class="w-full">
                        <h4 class="text-xl font-bold"> {{ category.label }} </h4>
                        <p> {{ category.description }} </p>
                    </div>
                    <div class="w-full space-y-2 ">
                        <Link :href="report.url" v-for="(report, reportName) in category.reports" :key="reportName" class="block px-5 py-4 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-100">
                            <h5 class="text-lg font-bold text-blue-500">
                                {{ report.label }}
                            </h5>
                            <p> {{ report.description }}</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AppButton from '../../../Components/shared/AppButton.vue';
import AppLayout from '../../../Components/templates/AppLayout.vue';


defineProps({
    categories: {
        type: Array,
    },
})
</script>
