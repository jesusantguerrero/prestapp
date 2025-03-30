<script setup>
import { config } from '@/config';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { AtAuthBox, AtAuthForm, AtField, AtInput, AtInputPassword } from 'atmosphere-ui';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Reset Password" />

    <AtAuthBox>
        <AtAuthForm
            :app-name="config.appName"
            btn-label="Send email"
            btn-class="mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary"
            link-class="text-primary hover:text-primary"
            v-model:isLoading="form.processing"
            mode="register"
            :errors="form.errors"
            @submit="submit"
            @home-pressed="onHomePressed"
            @link-pressed="onLinkPressed"
        >
         <template #brand>
            <Link href="/" class="w-full font-light font-brand">
                PrestaApp
            </Link>
          </template>
          <template #content>
            <AtField label="Email">
                <AtInput v-model="form.email" required />
            </AtField>
            <AtField label="Password">
                <AtInputPassword class="bg-white" v-model="form.password" required />
            </AtField>
            <AtField label="Confirm Password">
                <AtInputPassword class="bg-white" v-model="form.password_confirmation" required />
            </AtField>
        </template>
        </AtAuthForm>
    </AtAuthBox>
</template>
