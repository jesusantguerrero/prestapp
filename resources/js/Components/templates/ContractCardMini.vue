<script lang="ts" setup>
import { formatDate, formatMoney } from "@/utils";

import { getStatus, getStatusIcon } from "@/Modules/invoicing/constants";
import { IRent } from "@/Modules/properties/propertyEntity";
import IconMarker from "../icons/IconMarker.vue";
import { Link } from "@inertiajs/vue3";

defineProps<{
  contract: IRent;
  actions?: Record<string, any>;
  allowEdit: boolean;
}>();
</script>

<template>
  <article class="md:flex justify-between text-sm text-body-1">
    <header>
      <h4 class="flex flex-col font-bold">
        <Link class="text-body-1/80 group" :href="`/rents/${contract.id}`">
          <article class="flex">
            <IClarityContractLine class="mr-2 group-hover:text-primary" />
            <span
              class="group-hover:underline underline-offset-2 group-hover:text-primary decoration-dashed"
            >
              {{ $t("Rent to") }} {{ contract.client_name }}
            </span>
          </article>
        </Link>
        <article class="md:flex md:space-x-2 mt-2 md:mt-0">
          <Link
            :href="`/properties/${contract.property_id}`"
            class="group text-xs md:text-sm"
          >
            <section class="flex">
              <IconMarker class="mr-2 group-hover:text-primary" />
              <span
                class="group-hover:underline underline-offset-2 group-hover:text-primary decoration-dashed"
              >
                {{ contract.address }}
              </span>
            </section>
          </Link>
          <IMdiChevronRight class="hidden md:inline-block" />
          <Link
            :href="`/contacts/${contract.owner_id}/owner`"
            class="group text-xs md:text-sm"
          >
            <section class="flex">
              <IMdiUsers class="mr-2 group-hover:text-primary" />
              <span
                class="group-hover:underline underline-offset-2 group-hover:text-primary decoration-dashed"
                >{{ contract.owner_name }}</span
              >
            </section>
          </Link>
        </article>
        <div class="font-bold mt-4 md:mt-1 flex justify-between md:block">
          <span class="capitalize"> {{ $t("duration") }}: </span>
          <span class="text-primary">
            {{ formatDate(contract.date) }} -
            {{ formatDate(contract.end_date) }}
          </span>
        </div>
      </h4>
    </header>
    <section class="flex justify-between md:block font-bold text-right">
      <p class="flex space-x-2 justify-end items-center">
        <slot name="header-actions" />
        <span class="text-success">
          {{ formatMoney(contract.amount) }}
        </span>
      </p>
      <div class="flex items-center">
        <span class="w-32">
          <i :class="getStatusIcon(contract.status)" />
          {{ $t(getStatus(contract.status)) }}
        </span>
        <!-- <InvoicePaymentOptions
          :contract="contract"
          :allow-edit="allowEdit"
          @edit="$emit('edit')"
        /> -->
      </div>
    </section>
  </article>
</template>
