<script setup lang="ts">
import { ClientProfile } from "./ClientProfile";

defineProps<{
  client: IClient;
}>();
</script>

<template>
  <div>
    <header>
      <ClientProfile :client="client" />
    </header>
  </div>
</template>
