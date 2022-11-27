<template>
    <div
        class="w-full px-5 py-4 space-y-5 text-gray-600 bg-white border-gray-200 rounded-md shadow-md "
    >
        <div class="font-bold text-md">Pricing</div>
        <div class="w-full">
            <at-input
                :modelValue="price"
                @update:modelValue="$emit('update:price', $event)"
                class="font-bold"
                placeholder="Project name"
            />
        </div>
        <div class="space-y-1">
            <div v-for="(priceItem, index) in priceList" class="flex space-x-1">
                <AtInput v-model="priceItem.name" placeholder="name" />
                <AtInput v-model="priceItem.value" />
                <AtButton type="danger" @click.stop.prevent="removePrice(index)"> <i class="fa fa-trash"></i> </AtButton>
            </div>
        </div>
        <AtButton type="secondary" @click.stop.prevent="addPrice" class="w-full">
            Add multiple pricing
        </AtButton>
    </div>
</template>

<script setup>
import { AtButton, AtInput } from "atmosphere-ui";


const props = defineProps({
    price: {
        type: Number,
        default: 0,
    },
    priceData: {
        type: Object,
        default: () => {},
    },
    priceList: {
        type: Array,
        default: () => [],
    },
})

const PRICE_LIST_ITEM = {
    value: 0,
    name: "",
    retail_price: 0,
    sale_price: 0,
    list_price: 0,
    extended_sale_price: 0,
    extended_list_price: 0,
}

const addPrice = () => {
    props.priceList.push({ ...PRICE_LIST_ITEM })
}

const removePrice = (index) => {
    props.priceList.splice(index, 1)
}
</script>
