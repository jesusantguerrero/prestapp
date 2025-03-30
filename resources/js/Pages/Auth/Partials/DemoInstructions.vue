<script setup lang="ts">
import { config } from '@/config';

</script>

<template>
  <div class="border border-primary">
    <p class="text-secondary font-bold">{{ $t("Welcome to the {appName}!", { appName: config.appName }) }}</p>
    <p class="mt-2">
      <span>
        {{ $t("To log in, please use: email:") }}
      </span>
      <span class="text-secondary font-bold">demo@neatrents.com</span> password:
      <span class="text-secondary font-bold">password</span>.
    </p>
  </div>
</template>
