<script setup>
import { formatDate, formatMoney } from '@/utils';
import { ElDropdown, ElIcon } from 'element-plus';

defineProps({
  invoice: {
    type: Object,
    required: true
  },
  actions: {
    type: Object
  }
});

</script>

<template>
  <article  class="flex justify-between text-sm text-body-1">
    <header>
      <h4 class="font-bold">{{ invoice.concept }}  <span class="font-bold text-primary">
        {{ formatDate(invoice.due_date) }}
      </span></h4>
      <p class="text-body-1/80">{{ invoice.description }}</p>
    </header>
    <section class="font-bold text-right ">
        <p class="text-green-500">
          {{ formatMoney(invoice.total) }}
        </p>
        <span>
          {{ invoice.status }}
        </span>
        <ElDropdown v-if="actions" @command="$emit('action', $event)">
          <span>
            ...
          </span>
          <template #dropdown>
            <ElDropdownMenu>
              <ElDropdownItem :command="actionName" v-for="(action, actionName) in actions">
                {{ action.label }}
              </ElDropdownItem>
            </ElDropdownMenu>
          </template>
        </ElDropdown>
    </section>
  </article>
</template>
