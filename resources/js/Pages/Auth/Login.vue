<script setup lang="ts">
import { AtAuthBox, AtAuthForm, AtButton } from "atmosphere-ui";
import { router, useForm, Link, Head } from "@inertiajs/vue3";

import DemoInstructions from "./Partials/DemoInstructions.vue";

import { isDemo } from "@/utils/constants";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const onHomePressed = () => {
  router.visit("/");
};

const onLinkPressed = () => {
  router.visit("register");
};

const submit = (formData) => {
  form
    .transform((data) => ({
      ...data,
      ...formData,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
    });
};

const ssoLogin = () => {
  window.location.href = "/oauth/sso";
};
</script>

<template>
  <Head title="Log in" />

  <AtAuthBox theme="light" brand-container-class="text-primary mb-0">
    <AtAuthForm
      btn-class="mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary"
      link-class="text-primary hover:text-primary"
      v-model:isLoading="form.processing"
      :errors="form.errors"
      @submit="submit"
      @home-pressed="onHomePressed"
      @link-pressed="onLinkPressed"
    >
      <template #brand>
        <Link :href="{ name: 'landing' }" class="w-full font-light font-brand">
          <img src="/logo.svg" class="w-96" />
        </Link>
      </template>
      <template #prependInput v-if="isDemo">
        <DemoInstructions
          class="rounded-md mt-6 text-center py-2 px-5 bg-primary bg-opacity-25 text-sm font-sans"
        />
      </template>
      <template #more-actions>
        <AtButton @click="ssoLogin" class="bg-slate-900 w-full text-white" rounded type="button" attr-type="button">
          Connect with neatlancer
        </AtButton>
      </template>
    </AtAuthForm>
  </AtAuthBox>
</template>

<style lang="scss">
.auth-box {
  label {
    @apply text-secondary font-bold;
  }

  form {
    div.mb-20 {
      margin-bottom: -36px;
    }

    .form-group div,
    input {
      @apply rounded-md transition-all ease-in-out bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60;
    }
  }
}
</style>
