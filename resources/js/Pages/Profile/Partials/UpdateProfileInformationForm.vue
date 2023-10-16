<script setup lang="ts">
import { ref } from "vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import AppFormField from "@/Components/shared/AppFormField.vue";
import ProfilePhoto from "@/Components/ProfilePhoto.vue";

const props = defineProps({
  user: Object,
});

const form = useForm({
  _method: "PUT",
  name: props.user.name,
  email: props.user.email,
  photo: null,
});

const verificationLinkSent = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
  if (newProfilePhoto.value) {
    form.photo = newProfilePhoto.value;
  }

  form.post(route("user-profile-information.update"), {
    errorBag: "updateProfileInformation",
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
};

const sendEmailVerification = () => {
  verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
  photoInput.value.click();
};

const newProfilePhoto = ref<File | null>(null);
const deletePhoto = () => {
  router.delete(route("current-user-photo.destroy"), {
    preserveScroll: true,
    onSuccess: () => {
      newProfilePhoto.value = null;
    },
  });
};

const clearPhotoFileInput = () => {
  newProfilePhoto.value = null;
};
</script>

<template>
  <FormSection
    title="Profile Information "
    description="Update your account's profile information and email address."
  >
    <template #form>
      <!-- Profile Photo -->
      <AppFormField
        label="Profile picture"
        v-if="$page.props.jetstream.managesProfilePhotos"
        class="col-span-6 sm:col-span-4"
      >
        <!-- Profile Photo File Input -->
        <ProfilePhoto
          :photo-url="user.profile_photo_url"
          v-model:file="newProfilePhoto"
          :alt="user.name"
          @deleted="deletePhoto"
          :errors="form.errors.photo"
        />
      </AppFormField>

      <!-- Name -->
      <div class="col-span-6 sm:col-span-4">
        <AppFormField
          label="Name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          autocomplete="name"
          :errors="form.errors"
        />
      </div>

      <!-- Email -->
      <div class="col-span-6 sm:col-span-4">
        <AppFormField
          label="Email"
          name="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          :errors="form.errors"
        />
        <div
          v-if="
            $page.props.jetstream.hasEmailVerification && user.email_verified_at === null
          "
        >
          <p class="text-sm mt-2">
            Your email address is unverified.

            <Link
              :href="route('verification.send')"
              method="post"
              as="button"
              class="underline text-gray-600 hover:text-gray-900"
              @click.prevent="sendEmailVerification"
            >
              Click here to re-send the verification email.
            </Link>
          </p>

          <div
            v-show="verificationLinkSent"
            class="mt-2 font-medium text-sm text-green-600"
          >
            A new verification link has been sent to your email address.
          </div>
        </div>
      </div>
    </template>

    <template #actions>
      <ActionMessage :on="form.recentlySuccessful" class="mr-3"> Saved. </ActionMessage>

      <AppButton
        @click="updateProfileInformation"
        :processing="form.processing"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
      >
        Save
      </AppButton>
    </template>
  </FormSection>
</template>
