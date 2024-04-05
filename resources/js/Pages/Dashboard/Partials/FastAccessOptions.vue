<script setup lang="ts">
import { router } from "@inertiajs/core";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { computed } from "vue";
import { useResponsive } from "@/utils/useResponsive";
import { useI18n } from "vue-i18n";

const props = defineProps<{
  extended: boolean;
}>();

const { openModal } = useToggleModal("contact");
const { openModal: openInvoiceModal } = useToggleModal("propertyCharge");
const { isMobile } = useResponsive();

const { t } = useI18n();

const welcomeCards = computed(() => {
  const options = [
    {
      label: t("Contacts"),
      extended: true,
    },
    {
      label: t("Create contact"),
      icon: "contact",
      action() {
        openModal({
          data: { type: "tenant" },
          isOpen: true,
        });
      },
    },
    {
      label: t("Properties"),
      extended: true,
    },
    {
      label: t("Add property"),
      icon: "home",
      action() {
        router.visit("/properties/create");
      },
    },
    {
      label: t("Create rent"),
      icon: "document",
      action() {
        router.visit("/rents/create");
      },
      extended: true,
    },
    {
      label: t("Property expense"),
      icon: "document",
      action() {
        openInvoiceModal({
          data: { type: "lender " },
          isOpen: true,
        });
      },
      extended: true,
    },
    {
      label: t("Owner draws"),
      icon: "users",
      action() {
        router.visit("/owners/draws");
      },
      extended: true,
    },
    {
      label: t("Reports"),
      extended: true,
    },
    {
      label: t("Monthly rent"),
      icon: "document",
      action() {
        router.visit("/rent-reports/monthly-summary");
      },
    },
  ];

  if (isMobile.value) {
    return options.filter((item) => item.action);
  }

  return options.filter((option) => props.extended || !option.extended);
});
</script>

<template>
  <div
    class="grid py-2"
    :class="[extended ? 'grid-cols-3' : 'grid-cols-3 md:grid-cols-2']"
  >
    <template v-for="card in welcomeCards">
      <button
        v-if="card.action"
        class="flex flex-col items-center hover:text-primary justify-center w-full py-3 text-center transition-all ease-in bg-white border-2 border-transparent rounded-lg hover:border-primary group"
        :class="[extended ? 'text-body-1/50' : 'text-primary']"
        @click="
          card.action();
          $emit('action');
        "
      >
        <div class="icon-container" :class="extended ? 'text-2xl' : 'text-4xl'">
          <IMdiUserOutline v-if="card.icon == 'contact'" />
          <IMdiMoney v-if="card.icon == 'money'" />
          <IMdiHomeCityOutline v-if="card.icon == 'home'" />
          <IMdiFileDocument v-if="card.icon == 'document'" />
          <IMdiCalculator v-if="card.icon == 'calculator'" />
          <IMdiUsers v-if="card.icon == 'users'" />
        </div>

        <p class="text-sm font-bold text-body-1/50 group-hover:text-primary">
          {{ card.label }}
        </p>
      </button>
      <h4 v-else class="col-span-3 mt-2 text-secondary">
        {{ card.label }}
      </h4>
    </template>
  </div>
</template>
