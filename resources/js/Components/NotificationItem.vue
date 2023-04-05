<template>
  <div
    class="px-5 py-3 cursor-pointer hover:bg-gray-50"
    :class="{'bg-gray-200': !notification.read_at}"
    @click="$emit('read', notification)"
>
    <span class="font-bold" v-if="notification.data.title">{{ notification.data.title }}:</span> {{ notification.data.message }}
</div>
</template>

<script lang="ts" setup>
defineProps({
  notification: {
      type: Object,
      required: true
  }
});
</script>

<style>

</style>
