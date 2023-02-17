<script lang="ts" setup>
import { ElTag } from "element-plus";
import {
  getPropertyStatus,
  getPropertyStatusColor,
} from "@/Modules/properties/constants";

defineProps<{
  status: string;
}>();
</script>
<template>
  <ElTag :type="getPropertyStatusColor(status)">
    {{ getPropertyStatus(status) }}
  </ElTag>
</template>
