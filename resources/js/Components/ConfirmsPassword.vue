<script lang="ts" setup>
import { ref, reactive, nextTick } from "vue";
import DialogModal from "./DialogModal.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import { AtField, AtInputPassword } from "atmosphere-ui";

const emit = defineEmits(["confirmed"]);

defineProps({
  title: {
    type: String,
    default: "Confirm Password",
  },
  content: {
    type: String,
    default: "For your security, please confirm your password to continue.",
  },
  button: {
    type: String,
    default: "Confirm",
  },
});

const confirmingPassword = ref(false);

const form = reactive({
  password: "",
  error: "",
  processing: false,
});

const passwordInput = ref(null);

const startConfirmingPassword = () => {
  axios.get(route("password.confirmation")).then((response) => {
    if (response.data.confirmed) {
      emit("confirmed");
    } else {
      confirmingPassword.value = true;

      setTimeout(() => passwordInput.value.focus(), 250);
    }
  });
};

const confirmPassword = () => {
  form.processing = true;

  axios
    .post(route("password.confirm"), {
      password: form.password,
    })
    .then(() => {
      form.processing = false;

      closeModal();
      nextTick().then(() => emit("confirmed"));
    })
    .catch((error) => {
      form.processing = false;
      form.error = error.response.data.errors.password[0];
      passwordInput.value.focus();
    });
};

const closeModal = () => {
  confirmingPassword.value = false;
  form.password = "";
  form.error = "";
};
</script>

<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

    <DialogModal :show="confirmingPassword" @close="closeModal">
      <template #title>
        {{ title }}
      </template>

      <template #content>
        {{ content }}

        <AtField class="mt-4" :error="form.error">
          <AtInputPassword
            ref="passwordInput"
            v-model="form.password"
            type="password"
            placeholder="Password"
            class="border-t-0 border-b border-l-0 border-r-0"
            @keyup.enter="confirmPassword"
          />
        </AtField>
      </template>

      <template #footer>
        <AppButton variant="secondary" @click="closeModal"> Cancel </AppButton>

        <AppButton
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="confirmPassword"
        >
          {{ button }}
        </AppButton>
      </template>
    </DialogModal>
  </span>
</template>
