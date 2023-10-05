<template>
    <div
      class="w-full text-gray-600 rounded-md form-field"
      :class="[disabled ? 'pb-4' : ' bg-white space-y-1' ]"
    >
        <h1 class="mb-0 text-xl font-bold" v-if="title"> {{ title }}</h1>
        <p class="my-0" v-if="subtitle"> {{ subtitle }} </p>

        <section>
            <ServiceBlockItem
              v-for="(item, index) in items"
              :key="item.index"
              :item="item"
              :disabled="disabled"
              :hide-fields="hideFields"
              :labels="labels"
              @set-item="$emit('set-item', index, $event)"
            />
        </section>
    </div>
</template>

<script setup>
import ServiceBlockItem from './ServiceBlockItem.vue';

defineProps({
    disabled: {
        type: Boolean,
        required: false
    },
    subtitle: {
        type: String,
        default: "Describe and price the services you'll be delivering."
    },
    items: {
        type: Boolean,
        required: false
    },
    hideFields: {
      type: Array,
    },
    labels: {
      type: Object
    }
});
</script>
