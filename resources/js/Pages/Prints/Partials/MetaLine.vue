<script setup lang="ts">
import { computed } from "vue";
import { formatDate, formatMoney } from "@/utils";

interface Props {
  label: string;
  value: string | number;
  type?: string;
}

const props = defineProps<Props>();

const formattedValue = computed(() => {
  if (props.type == "money") {
    return formatMoney(props.value);
  } else if (props.type == "date" && typeof props.value == "string") {
    return formatDate(props.value);
  }
  return props.value;
});
</script>

<template>
  <p>
    <span>
      {{ label }}
    </span>
    <span>
      {{ formattedValue }}
    </span>
  </p>
</template>
