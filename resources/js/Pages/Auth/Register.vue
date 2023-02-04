<script setup>
import { AtAuthBox, AtAuthForm, AtField, AtInput } from "atmosphere-ui";
import { router, useForm, Link, Head } from "@inertiajs/vue3";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: true,
})

const onHomePressed = () => {
    router.visit('/');
}

const onLinkPressed = () => {
    router.visit('login');
}

const submit = (formData) => {
    form
        .transform(data => ({
            ...data,
            ...formData,
            password_confirmation: formData.confirmPassword,
            remember: form.remember ? 'on' : ''
        }))
        .post(route('register'), {
            onFinish: () => form.reset('password'),
        })
}
</script>

<template>
    <Head title="Log in" />

    <AtAuthBox>
        <AtAuthForm
            app-name="ICLoan"
            btn-class="mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary"
            link-class="text-primary hover:text-primary"
            mode="register"
            v-model:isLoading="form.processing"
            :errors="form.errors"
            @submit="submit"
            @home-pressed="onHomePressed"
            @link-pressed="onLinkPressed"
        >
            <template #brand>
                <Link :to="{name: 'landing'}" class="w-full font-light font-brand">
                    ICLoan
                </Link>
            </template>
            <template #prependInput>
                <AtField label="Name">
                    <AtInput v-model="form.name" required />
                </AtField>
            </template>
        </AtAuthForm>
    </AtAuthBox>
</template>
