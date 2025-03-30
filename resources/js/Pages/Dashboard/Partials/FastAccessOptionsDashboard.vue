<script setup lang="ts">
import { router } from "@inertiajs/core";
import { useToggleModal } from "@/Modules/_app/useToggleModal";
import { computed } from "vue";
import { useResponsive } from "@/utils/useResponsive";
import { useI18n } from "vue-i18n";

interface FastAccessCard {
  label: string;
  icon?: string;
  description?: string;
  extended?: boolean;
  isSection?: boolean;
  action?: () => void;
}

const props = defineProps<{
  extended?: boolean;
  display?: string;
}>();

const { openModal } = useToggleModal("contact");
const { openModal: openInvoiceModal } = useToggleModal("propertyCharge");
const { isMobile } = useResponsive();
const { t } = useI18n();

const welcomeCards = computed((): FastAccessCard[] => {
  const options: FastAccessCard[] = [
    {
      label: t("Contacts"),
      extended: true,
      isSection: true
    },
    {
      label: t("Create contact"),
      icon: "contact",
      description: t("Add new tenant or owner"),
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
      isSection: true
    },
    {
      label: t("Add property"),
      icon: "home",
      description: t("Register new property"),
      action() {
        router.visit("/properties/create");
      },
    },
    {
      label: t("Create rent"),
      icon: "document",
      description: t("New rental agreement"),
      action() {
        router.visit("/rents/create");
      },
    },
    {
      label: t("Property expense"),
      icon: "document",
      description: t("Record property costs"),
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
      description: t("Manage owner withdrawals"),
      action() {
        router.visit("/owners/draws");
      },
      extended: true,
    },
    {
      label: t("Reports"),
      extended: true,
      isSection: true
    },
    {
      label: t("Monthly rent"),
      icon: "document",
      description: t("View monthly summary"),
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
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 py-4 bg-base-lvl-3 rounded-lg px-4">
    <template v-for="(card, index) in welcomeCards" :key="index">
      <button v-if="!card.isSection && card.action"
        class="group flex items-center p-2.5 bg-base-lvl-2rounded-md hover:bg-primary/5 transition-all text-left"
        @click="card.action(); $emit('action');">
        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-md bg-base-lvl-3 text-primary">
          <IMdiUserOutline v-if="card.icon == 'contact'" class="w-5 h-5" />
          <IMdiMoney v-if="card.icon == 'money'" class="w-5 h-5" />
          <IMdiHomeCityOutline v-if="card.icon == 'home'" class="w-5 h-5" />
          <IMdiFileDocument v-if="card.icon == 'document'" class="w-5 h-5" />
          <IMdiCalculator v-if="card.icon == 'calculator'" class="w-5 h-5" />
          <IMdiUsers v-if="card.icon == 'users'" class="w-5 h-5" />
        </div>
        <div class="min-w-0 ml-3">
          <div class="text-sm font-medium text-body leading-snug truncate">{{ card.label }}</div>
          <div class="text-xs text-body-1 leading-snug truncate mt-0.5">{{ card.description }}</div>
        </div>
      </button>
    </template>
  </div>
</template>

<style scoped>
.group:hover .group-hover\:shadow-sm {
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}
</style>
