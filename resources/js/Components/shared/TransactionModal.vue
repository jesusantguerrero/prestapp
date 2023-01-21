<template>
  <modal
    :show="show"
    :max-width="maxWidth"
    :full-height="fullHeight"
    :closeable="closeable"
    v-slot:default="{ close }"
    @close="$emit('update:show', false)"
  >
    <div class="pb-4 bg-base-lvl-3 sm:p-6 sm:pb-4 text-body flex-1">
      <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
        <TransactionTypesPicker v-model="form.direction" />

        <div class="mt-2">
          <slot name="content">
            <div>
              <header v-if="fullHeight" class="flex justify-between py-3 px-4">
                <CategoryPicker
                  class="w-full"
                  v-model="form[categoryField]"
                  v-model:isPickerOpen="isPickerOpen"
                  :placeholder="`Choose ${categoryLabel}`"
                  :options="categoryOptions"
                />

                <AtField v-if="!isPickerOpen">
                  <AtInput :number-format="true" v-model="form.total">
                    <template #prefix>
                      <span class="flex items-center pl-2"> RD$ </span>
                    </template>
                  </AtInput>
                </AtField>
              </header>

              <div class="md:flex md:space-x-2 md:px-0 px-4">
                <AtField
                  label="Date"
                  class="md:w-3/12 md:block flex w-full justify-between"
                >
                  <ElDatePicker
                    v-model="form.date"
                    type="date"
                    size="large"
                    class="w-48 md:w-full"
                  />
                </AtField>

                <AtField
                  label="Description"
                  class="md:w-5/12 flex w-full md:block md:space-x-0 justify-between"
                >
                  <AtInput v-model="form.description" class="w-48 md:w-full" />
                </AtField>

                <AtField
                  :label="accountLabel"
                  class="md:w-4/12 md:block md:space-x-0 flex w-full justify-between space-x-4"
                >
                  <BaseSelect
                    class="w-48 md:w-full"
                    endpoint="/api/accounts"
                    v-model="form.account"
                    track-by="id"
                    label="name"
                  />
                </AtField>
              </div>
              <div class="md:flex md:space-x-3 md:px-0 px-4">
                <AtField :label="categoryLabel" class="hidden md:block md:w-full">
                  <BaseSelect
                    endpoint="/api/accounts"
                    v-model="form.counterAccount"
                    track-by="id"
                    label="name"
                  />
                </AtField>
                <AtField label="Amount" class="hidden md:block md:w-5/12">
                  <AtInput :number-format="true" v-model="form.total">
                    <template #prefix>
                      <span class="flex items-center pl-2"> RD$ </span>
                    </template>
                  </AtInput>
                </AtField>
              </div>
            </div>

            <div class="flex space-x-2">
              <AtFieldCheck v-model="isRecurrence" label="Set recurrence" />
            </div>

            <div v-if="isRecurrence">
              <div class="flex">
                <AtField label="Repeat this transaction" class="w-full">
                  <n-select
                    v-model:value="schedule_settings.frequency"
                    :options="[
                      {
                        value: 'WEEKLY',
                        label: 'Weekly',
                      },
                      {
                        value: 'MONTHLY',
                        label: 'Monthly',
                      },
                    ]"
                  />
                </AtField>
                <AtField :label="frequencyLabel">
                  <AtInput
                    type="number"
                    v-model="schedule_settings.repeat_on_day_of_month"
                  />
                </AtField>
              </div>
              <div class="flex">
                <AtField label="Ends" class="w-full">
                  <n-select
                    v-model:value="schedule_settings.end_type"
                    :options="[
                      {
                        value: 'NEVER',
                        label: 'Never',
                      },
                      {
                        value: 'DATE',
                        label: 'At',
                      },
                      {
                        value: 'COUNT',
                        label: 'After',
                      },
                    ]"
                  />
                </AtField>
                <AtField label="Date" v-if="schedule_settings.end_type == 'DATE'">
                  <n-date-picker v-model:value="schedule_settings.end_date" size="lg" />
                </AtField>
                <AtField label="Instances" v-if="schedule_settings.end_type == 'COUNT'">
                  <AtInput type="number" v-model="schedule_settings.count" />
                </AtField>
              </div>
            </div>
          </slot>
        </div>
      </div>
    </div>

    <footer
      class="px-6 py-4 space-x-3 items-center justify-end flex w-full bg-base-lvl-2"
    >
      <div>
        <AtButton @click="close" rounded class="h-10 text-body"> Cancel </AtButton>
        <AtButton
          class="h-10 text-white bg-primary"
          :disabled="!form.total || form.processing"
          @click="submit"
          rounded
        >
          Save
        </AtButton>
      </div>
    </footer>
  </modal>
</template>

<script setup>
import { format } from "date-fns";
import { reactive, toRefs, watch, computed, inject, ref } from "vue";
import { useForm } from "@inertiajs/inertia-vue3";
import { AtField, AtButton, AtFieldCheck, AtInput } from "atmosphere-ui";

import Modal from "@/Components/Modal.vue";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import TransactionTypesPicker from "./TransactionTypesPicker.vue";

import { TRANSACTION_DIRECTIONS } from "@/Modules/transactions";

const props = defineProps({
  show: {
    default: false,
  },
  maxWidth: {
    default: "2xl",
  },
  closeable: {
    default: true,
  },
  categories: {
    type: Array,
    default: [],
  },
  accounts: {
    type: Array,
    default: [],
  },
  recurrence: {
    type: Boolean,
    default: false,
  },
  transactionData: {
    type: Object,
    default: () => ({}),
  },
  mode: {
    type: String,
    default: "income",
  },
  fullHeight: {
    type: Boolean,
  },
});

const emit = defineEmits(["close", "saved"]);

const transactionTypes = [
  {
    value: "DEPOSIT",
    label: "Income",
  },
  {
    value: "WITHDRAW",
    label: "Expense",
  },
  {
    value: "TRANSFER",
    label: "Transfer",
  },
];

const state = reactive({
  frequencyLabel: "every",
  schedule_settings: {
    end_date: null,
    count: null,
    end_type: "NEVER",
    final_item_date: null,
    interval: 1,
    frequency: "monthly",
    repeat_at_end_of_month: false,
    repeat_on_day_of_month: null,
    start_date: null,
    timezone_id: "America/Santo_Domingo",
  },
  form: useForm({
    name: "",
    payee_id: "",
    account: null,
    payee_label: "",
    date: null,
    description: "",
    direction: "WITHDRAW",
    category_id: null,
    counterAccount: null,
    counter_account_id: null,
    account_id: null,
    display_id: "",
    total: 0,
    is_transfer: false,
  }),
});

watch(
  () => state.form.direction,
  (direction) => {
    if (direction?.toLowerCase() == "transfer") {
      state.form.is_transfer = true;
    } else {
      state.form.is_transfer = false;
    }
  }
);
const isTransfer = computed(() => {
  return state.form.is_transfer;
});

const accountLabel = computed(() => {
  return !isTransfer.value ? "Account" : "Source";
});
const categoryLabel = computed(() => {
  return !isTransfer.value ? "Category" : "Destination";
});

const categoryField = computed(() => {
  return isTransfer.value ? "counter_account_id" : "category_id";
});

const categoryOptions = inject("categoryOptions", []);
const accountsOptions = inject("accountsOptions", []);

const categoryAccounts = computed(() => {
  return isTransfer.value ? accountsOptions : categoryOptions;
});

const close = () => {
  emit("close");
};

const submit = () => {
  const actions = {
    transaction: {
      save: {
        method: "post",
        url: () => route("transactions.store"),
      },
      update: {
        method: "put",
        url: () => route("transactions.update", props.transactionData),
      },
    },
    recurrence: {
      save: {
        method: "post",
        url: () => route("transactions.store-planned"),
      },
      update: {
        method: "update",
        url: () => route("transactions.store-planned"),
      },
    },
  };
  const method = props.transactionData && props.transactionData.id ? "update" : "save";
  const actionType = isRecurrence.value ? "recurrence" : "transaction";
  const action = actions[actionType][method];

  state.form
    .transform((form) => ({
      ...form,
      resource_type_id: "MANUAL",
      total: form.total,
      account_id: form.account?.id,
      counter_account_id: form.counterAccount?.id,
      date: format(new Date(form.date), "yyyy-MM-dd"),
      status: "verified",
      direction: form.is_transfer ? TRANSACTION_DIRECTIONS.WITHDRAW : form.direction,
      ...state.schedule_settings,
    }))
    .submit(action.method, action.url(), {
      onBefore(evt) {
        if (!evt.data.total) {
          alert("The balance should be more than 0");
        }
      },
      onSuccess: () => {
        emit("close");
        state.form.reset();
      },
    });
};
watch(
  () => props.transactionData,
  () => {
    const newValue = { ...props.transactionData };
    Object.keys(state.form.data()).forEach((field) => {
      if (field == "date" && newValue[field]) {
        state.form[field] = new Date(newValue[field]);
      } else {
        state.form[field] = newValue[field] || state.form[field];
      }
    });
  },
  { deep: true }
);

watch(
  () => props.show,
  (show) => {
    state.form.direction = props.mode?.toUpperCase() ?? "EXPENSE";
  }
);

const isRecurrence = ref(props.recurrence);
const toggleRecurrence = () => {
  isRecurrence.value = !isRecurrence.value;
  emit("update:recurrence", isRecurrence.value);
};

const { form, schedule_settings } = toRefs(state);

//
const isPickerOpen = ref(false);
</script>