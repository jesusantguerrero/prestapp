<script lang="ts" setup>
import { formatMoney } from "@/utils";
import BaseSelect from "../BaseSelect.vue";
import axios from "axios";
import { ref, watchEffect } from "vue";

const { clientId, categoryName, modelValue } = defineProps<{
  modelValue: string;
  clientId: number;
  categoryName: string;
}>();

const emit = defineEmits(["update:modelValue"]);

function getDepositBalance(clientId: number, categoryName: string) {
  return axios
    .get(`/categories/${categoryName}/clients/${clientId}/balance?exclude_credits=true`)
    .then(({ data }) => {
      return data;
    });
}

const options = ref();
watchEffect(async () => {
  const results = await getDepositBalance(clientId, categoryName);
  options.value = results;
  emit("update:modelValue", results[0]);
});
</script>

<template>
  <BaseSelect
    :options="options"
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :placeholder="$t('Select a deposit')"
    label="alias"
    track-by="id"
  >
    <template v-slot:singleLabel="{ option }">
      <span class="option__title">{{ option.cat_alias ?? option.cat_name }}</span>
      <span class="option__small ml-2"
        >({{ formatMoney(Math.abs(option.balance)) }})
      </span>
    </template>
    <template v-slot:option="{ option }">
      <div class="option__desc">
        <span class="option__title">{{ option.cat_alias ?? option.cat_name }}</span>
        <span class="option__small ml-2"
          >({{ formatMoney(Math.abs(option.balance)) }})
        </span>
      </div>
    </template>
  </BaseSelect>
</template>
