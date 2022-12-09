<template>
<div class="flex justify-between w-full pr-8">
    <SectionNavTab
        v-for="section in sections"
        @click="handleClick(section)"
        :is-selected="isSelected(section)"
        :key="section.url"
    >
        {{ section.label }}
    </SectionNavTab>
    <div class="flex items-center justify-end py-1 ml-auto space-x-2">
       <slot name="actions" />
    </div>
</div>
</template>


<script setup>
import { router  } from '@inertiajs/vue3';
import { computed } from "vue"
import SectionNavTab from './SectionNavTab.vue';

const props = defineProps({
    sections: {
        type: Array,
        default: () => ([])
    },
    modelValue: {
        type: String
    }
})
const emit = defineEmits(['update:modelValue']);

const currentPath = computed(() => {
    return document?.location?.pathname
})

const isSelected = (section) => {
    const sectionName = section.url || section.value
    const value = props.modelValue || currentPath.value
    return sectionName == value
}

const handleClick = (section) => {
    if (section.url) {
        router.visit(section.url)
    } else {
        emit('update:modelValue', section.value)
    }
}
</script>
