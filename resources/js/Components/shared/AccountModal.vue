<script setup lang="ts">
import { useForm, usePage } from "@inertiajs/vue3";
import { reactive, toRefs, computed, watch } from "vue";
import { AtField, AtButton, AtInput } from "atmosphere-ui";
import Slug from "slug";
import BaseSelect from "@/Components/shared/BaseSelect.vue";

import Modal from "@/Components/Modal.vue";

const emit = defineEmits(["close"]);
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
  formData: {
    type: Object,
    default: () => ({}),
  },
});

const state = reactive({
  form: useForm({
    id: null,
    name: "",
    category_id: null,
    account_detail_type_id: null,
    display_id: "",
    description: "",
    opening_balance: 0,
  }),
  accountDisplayId: computed(() => {
    return state.form.name && Slug(state.form.name, "_");
  }),
});

const modalTitle = computed(() => {
  return props.formData.id ? `Edit ${props.formData.name} account` : "Add Account";
});

watch(
  () => props.formData,
  (newValue) => {
    Object.keys(state.form.data()).forEach((field) => {
      if (field == "date") {
        state.form[field] = new Date(newValue[field]);
      } else {
        state.form[field] = newValue[field];
      }
    });
  },
  { deep: true, immediate: true }
);

const close = () => {
  emit("close");
};

const submit = () => {
  const actions = {
    save: {
      method: "post",
      url: () => route("accounts.store"),
    },
    update: {
      method: "put",
      url: () => route("accounts.update", props.formData),
    },
  };

  const method = props.formData && props.formData.id ? "update" : "save";
  const action = actions[method];

  state.form
    .transform((form) => {
      form.display_id = Slug(form.name, "_");
      return form;
    })
    .submit(action.method, action.url(), {
      onSuccess: ({ data }) => {
        emit("close");
        state.form.reset();
      },
    });
};

const remove = () => {
  if (confirm("Are you sure you want to delete this account?")) {
    state.form.delete(route("accounts.destroy", state.form), {
      onSuccess() {
        emit("close");
        state.form.reset();
      },
    });
  }
};

const detailTypes = usePage().props.accountDetailTypes;

const { form } = toRefs(state);
</script>

<template>
  <modal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
    <div class="pb-4 bg-base-lvl-3 sm:p-6 sm:pb-4 text-body">
      <div class="text-center sm:mt-0 sm:ml-4 sm:text-left">
        <header class="pb-3 border-b">
          <h3 class="text-lg font-bold">
            <slot name="title">{{ modalTitle }}</slot>
          </h3>
        </header>

        <div class="px-4 pt-5 mt-2">
          <slot name="content">
            <div>
              <AtField
                label="Detail Type"
                class="flex justify-between w-full space-x-4 md:space-x-0"
              >
                <BaseSelect
                  endpoint="/api/categories"
                  v-model="form[categoryField]"
                  @update="createCategory"
                  :default-expand-all="true"
                  track-by="id"
                  label="name"
                />
              </AtField>
              <AtField
                label="Detail Type"
                class="flex justify-between w-full space-x-4 md:space-x-0"
              >
                <BaseSelect
                  class="w-48 md:w-full"
                  v-model:value="form.account_detail_type_id"
                  :default-expand-all="true"
                  :options="detailTypes"
                />
              </AtField>

              <AtField
                label="Account Label"
                class="flex justify-between w-full space-x-4 md:space-x-0"
              >
                <AtInput v-model="form.name" class="w-48 md:w-full" />
              </AtField>

              <AtField
                label="Opening Balance"
                class="flex justify-between w-full space-x-4 md:space-x-0"
              >
                <AtInput
                  v-model="form.opening_balance"
                  type="number"
                  class="w-48 md:w-full"
                />
              </AtField>
            </div>
          </slot>
        </div>
      </div>
    </div>

    <div class="flex w-full px-6 py-4 md:space-x-3 space-between bg-base-lvl-2">
      <AtButton
        type="secondary"
        class="hidden text-danger md:block"
        @click="remove"
        rounded
      >
        Delete
      </AtButton>
      <div class="flex items-center justify-end w-full md:space-x-2">
        <AtButton type="secondary" class="hidden md:block" @click="close" rounded>
          Cancel
        </AtButton>
        <AtButton class="text-white bg-primary" @click="submit" rounded> Save </AtButton>
      </div>
    </div>
  </modal>
</template>
