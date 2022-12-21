<template>
<div
    @click="$emit('click')"
    class="cursor-pointer rounded-md bg-transparent hover:bg-gray-300 hover:text-gray-700 transition-all border border-dashed border-gray-400 px-5 py-2 font-bold text-gray-400 flex justify-between items-center">
    <slot></slot>
    <i class="fa fa-plus"></i>
</div>
</template>

<script>
export default {

}
</script>

<style>

</style>
