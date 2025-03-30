<script setup>
import { nextTick, ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { AtAuthBox, AtAuthForm, AtField, AtInput } from 'atmosphere-ui';
import { config } from '@/config';

const recovery = ref(false);

const form = useForm({
    code: '',
    recovery_code: '',
});

const recoveryCodeInput = ref(null);
const codeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};

const descriptionLabel = computed(() => {
    return recovery.value
    ? "Please confirm access to your account by entering one of your emergency recovery codes."
    : " Please confirm access to your account by entering the authentication code provided by your authenticator application."
});

const linkLabel = computed(() => {
    return recovery.value
    ? "Use an authentication code"
    : "Use a recovery code"
});
</script>

<template>
    <Head title="Two-factor Confirmation" />

    <AtAuthBox>
        <AtAuthForm
            :app-name="config.appName"
            btn-label="Login"
            custom-link-label="Recovery"
            btn-class="mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary"
            link-class="text-primary hover:text-primary"
            v-model:isLoading="form.processing"
            mode="register"
            :errors="form.errors"
            @submit="submit"
            @home-pressed="onHomePressed"
            @link-pressed="toggleRecovery()"
        >
            <template #brand>
                <AuthenticationCardLogo />
            </template>

            <template #content>
                <section>
                    <div class="mb-4 text-sm text-body">
                        {{ descriptionLabel }}
                    </div>
                    <AtField label="Code" v-if="!recovery">
                        <AtInput
                            id="code"
                            ref="codeInput"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            autofocus
                            autocomplete="one-time-code"
                        />
                    </AtField>
                    <AtField label="Recovery Code" field="recovery_code" :errors="form.errors" v-else>
                        <AtInput
                            id="recovery_code"
                            ref="recoveryCodeInput"
                            v-model="form.recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                        />
                    </AtField>
                </section>
            </template>
        </AtAuthForm>
    </AtAuthBox>
</template>
