<script lang="ts" setup>
import { format } from "date-fns";
import { reactive, toRefs, watch, computed, inject, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
// @ts-ignore
import { AtField, AtButton, AtInput } from "atmosphere-ui";

import Modal from "@/Components/Modal.vue";
import TransactionTypesPicker from "./TransactionTypesPicker.vue";

import { TRANSACTION_DIRECTIONS } from "@/Modules/transactions";
import AccountSelect from "./Selects/AccountSelect.vue";

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

const state = reactive({
  frequencyLabel: "every",
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
  return !isTransfer.value ? "Cuenta" : "Cuenta origen";
});
const categoryLabel = computed(() => {
  return !isTransfer.value ? "Categoria" : "Cuenta destino";
});

const categoryField = computed(() => {
  return isTransfer.value ? "counter_account_id" : "category_id";
});

const categoryOptions = inject("categoryOptions", []);

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
    state.form.direction = props.mode?.toUpperCase() ?? "WITHDRAW";
  }
);

const isRecurrence = ref(props.recurrence);
const toggleRecurrence = () => {
  isRecurrence.value = !isRecurrence.value;
  emit("update:recurrence", isRecurrence.value);
};

const { form } = toRefs(state);

//
const isPickerOpen = ref(false);
</script>

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
                  label="Fecha"
                  class="md:w-4/12 md:block flex w-full justify-between"
                >
                  <ElDatePicker
                    v-model="form.date"
                    type="date"
                    size="large"
                    class="w-48 md:w-full"
                  />
                </AtField>

                <AtField
                  label="Concepto"
                  class="md:w-8/12 flex w-full md:block md:space-x-0 justify-between"
                >
                  <AtInput v-model="form.description" class="w-48 md:w-full" />
                </AtField>
              </div>
              <AtField
                :label="accountLabel"
                class="md:w-full md:block md:space-x-0 flex w-full justify-between space-x-4"
              >
                <AccountSelect endpoint="/api/accounts" v-model="form.account" />
              </AtField>
              <div class="md:flex md:space-x-3 md:px-0 px-4">
                <AtField :label="categoryLabel" class="hidden md:block md:w-full">
                  <AccountSelect endpoint="/api/accounts" v-model="form.counterAccount" />
                </AtField>
                <AtField label="Monto" class="hidden md:block md:w-5/12">
                  <AtInput :number-format="true" v-model="form.total">
                    <template #prefix>
                      <span class="flex items-center pl-2"> RD$ </span>
                    </template>
                  </AtInput>
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
