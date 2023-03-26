<script lang="ts" setup>
defineProps<{
  isDisabled?: boolean;
}>();
</script>

<template>
  <div class="rounded-md shadow-md p-20 text-center bg-base-lvl-3">
    <div class="modal-body">
      <div class="state-logo text-2xl">
        <IMdiMail class="mx-auto text-6xl" />
      </div>
      <h3 class="text-secondary font-bold">
        {{ isDisabled ? $t("approval.disabled.title") : $t("approval.pending.title") }}
      </h3>
      <p class="mt-4 text-body-1">
        {{
          isDisabled ? $t("approval.disabled.content") : $t("approval.pending.content")
        }}
      </p>
    </div>
  </div>
</template>
