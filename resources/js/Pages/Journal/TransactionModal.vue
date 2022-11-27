<template>
<account-modal
    v-model:is-open="isVisible"
    :buttons="buttons"
    title="Add an account"
    @update:is-open="$emit('update:is-open', $event)"
    @command="handleCommand"
>
    <template #body>
        <div class="row">
            <AtField label="category">
                <jet-select
                    v-model="account.category_id"
                    v-model:selected="category"
                    :options="categories"
                    placeholder="Select a category"
                    class="w-full"
                    group-label="name"
                    group-items="subcategories"
                    label="name"
                    key-track="id"
                />
            </AtField>

            <div class="flex mt-5">
                    <AtField
                        class="w-6/12"
                        label="Account"
                    >
                        <AtInput
                            type="text"
                            class="form-control"
                            v-model="account.name"
                            required
                            min="1"
                        />
                    </AtField>

                    <AtField
                        class="w-6/12"
                        label="Account Id"
                    >
                        <AtInput
                            type="text"
                            class="form-control"
                            v-model="account.display_id"
                            required
                            min="1"
                        />
                    </AtField>
            </div>

            <div class="mt-5">
                    <label for="">Description</label>
                    <textarea
                        class="w-full"
                        v-model="account.description"
                        cols="3"
                        rows="3"
                    >
                    </textarea>
            </div>
        </div>
    </template>
</account-modal>
</template>

<script setup>
import axios from "axios";
import { ElNotification } from "element-plus";
import { AtField, AtInput } from 'atmosphere-ui';
import { Inertia } from '@inertiajs/inertia';
import { reactive, watch } from 'vue';


import AccountModal from '@/Atmosphere/Organisms/AccountModal.vue';
import JetSelect from '@/Atmosphere/Molecules/JetSelect.vue';

const props =  defineProps({
    isVisible: Object,
    accountData: [Object, null],
    endpoint: {
      type: String,
      required: true
    },
    categories: {
        type: Array,
        required: true
    }
});

const emit = defineEmits({
    'update:is-open': Boolean,
    'command': String
});

const state = reactive({
    account: {},
    category: null,
    accounts: [],
    isLoading: false
});

watch(
      () => props.accountData,
      (account) => {
        if (account) {
          this.account = account;
        }
      },
      {
      deep: true,
      immediate: true
});

const buttons = computed(() => {
    const buttons = [{
        name: 'cancel',
        text: 'Cancel',
        classes: 'bg-gray-400 text-white hover:bg-gray-500'
    },
    {
        name: 'delete',
        text: 'Delete Account',
        classes: 'bg-red-400 text-white hover:bg-red-500',
        requireEdit: true
    },
    {
        name: 'save',
        text: 'Save',
        classes: 'bg-green-400 text-white hover:bg-green-500'
    }]
    return buttons.filter(button => !button.requireEdit || (button.requireEdit && this.accountData && this.accountData.id))
});

const resetForm = (shouldClose) => {
      state.account = {};
      if (shouldClose) {
        emit('update:is-open', false)
      }
}

const addAccount = () => {
    if (!this.account.name || !this.account.category_id) {
        ElNotification({
            type: "error",
            message: "should specify a category and name"
        });
        return;
    }

    const formData = this.account;

    axios.post("/accounts", formData)
    .then(() => {
        ElNotification({
            title: "Saved",
            message: "Account Saved",
            type: "success"
        })
        resetForm(true);
        Inertia.reload();
    })
}

const handleCommand = (commandName) => {
    switch (commandName) {
        case 'save':
            addAccount()
            break;
        case 'delete':
            emit('update:is-open', false)
            break;
        default:
            emit('update:is-open', false)
            break;
    }
}
</script>
