<template>
    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex items-center justify-between py-2 mb-10 border-4 border-white rounded-md bg-gray-50">
            <div class="px-5 font-bold text-gray-600">
                Statements
            </div>

            <div class="flex space-x-2 overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min">
                <AtButton type="primary" @click="$inertia.visit('/statements/income')" class="text-center w-28">
                    Incomes
                </AtButton>
                <AtButton type="primary" @click="$inertia.visit('/statements/expense')" class="text-center w-28">
                    Expenses
                </AtButton>
            </div>
        </div>
        <div class="w-full px-5 py-5 mb-10 bg-white rounded-md shadow-md">
            <header class="text-center text-gray-500">
                <h4 class="text-3xl font-bold capitalize"> {{ categoryType }} Statement </h4>
                <h5 class="font-bold"> Neatforms </h5>
                <p> From date to date</p>
            </header>
            <div class="flex items-center justify-end space-x-2">
                <AtButton type="primary" @click="isSummary=true"> Summary </AtButton>
                <AtButton type="primary" @click="isSummary=false"> Details </AtButton>
            </div>
            <div class="mt-10 items" :class="{'divide-y': isSummary}">
                <div v-for="category in mainCategories" :key="category.id" class="py-2">
                    <div v-if="hasHeader(category)" class="w-full px-5 py-2 mt-5 font-bold bg-gray-200">
                        {{ category.category.name }}
                    </div>
                    <div class="divide-y" v-if="!isSummary">
                        <div class="px-5 py-2 font-semibold bg-gray-100">
                            {{ category.name }}
                        </div>
                        <div class="w-full px-5" v-for="account in category.accounts">
                            <div class="flex justify-between py-2">
                                <span class="font-semibold text-blue-500"> {{ account.name }} </span>
                                <div>
                                    <span> {{ formatMoney(account.balance) }}</span>
                                    <AtButton @click="setPayment(account)"> Pay </AtButton>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between px-5 py-2" :class="{'border-t': !isSummary}">
                        <span class="font-bold"> Total {{ category.name }} </span>
                        <span class="font-bold"> {{ formatMoney(category.total) }}</span>
                    </div>
                </div>

                <div class="flex justify-between py-5 text-xl capitalize">
                    <span class="font-bold"> Total {{ categoryType }}s </span>
                    <span class="font-bold"> {{ formatMoney(categoriesTotal) }}</span>
                </div>
            </div>
        </div>
        <transaction-modal
            @close="state.isTransferModalOpen=false"
            v-model:show="state.isTransferModalOpen"
        />
    </div>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import TransactionModal from "@/Components/TransactionModal.vue"
import formatMoney from "@/Atmosphere/utils/formatMoney"
import { AtButton } from "atmosphere-ui";
import { useSelect } from "@/Atmosphere/utils/useSelects"
import { reactive, computed, toRefs, ref } from "vue";

const props = defineProps({
    categoryType: {
        type: String,
        default: 'income'
    },
    categories: {
        type: Array,
    },
    accounts: {
        type: Array,
    }
})

const state = reactive({
    isSummary: true,
    isTransferModalOpen: false,
    mainCategories: computed(() => {
        return props.categories
    }),
    categoriesTotal: computed(() => {
        return props.categories.reduce((total, category) => {
            return total + parseFloat(category.total || 0)
        }, 0)
    })
});

const lastParent = ref(null);
const hasHeader = (category) => {
    const parent = category.category;
    if (parent && (!lastParent.value || lastParent.value.id !== parent.id)) {
        lastParent.value = parent;
        return true;
    } else {
        return false;
    }
}

const setPayment = (account) => {
    state.isTransferModalOpen = true;
    state.transferAccount = account;
}

const { categoryOptions: transformCategoryOptions } = useSelect()
transformCategoryOptions(props.categories, true, 'categoryOptions');
transformCategoryOptions(props.accounts, true, 'accountsOptions');

const { isSummary, mainCategories, categoriesTotal } = toRefs(state);
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
