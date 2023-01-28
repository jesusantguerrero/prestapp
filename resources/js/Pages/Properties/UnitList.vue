<script setup lang="ts">
// @ts-ignore: its my template
import AppLayout from "@/Components/templates/AppLayout.vue";
import { router } from "@inertiajs/core";
import { computed } from "vue";
import cols from "./unitCols";
import AtTable from "@/Components/AtTable.vue";
import AppButton from "@/Components/shared/AppButton.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import { IPaginatedData } from "@/utils/constants";
import { IProperty } from "@/Modules/properties/propertyEntity";

const props = defineProps<{
  units: IProperty[] | IPaginatedData<IProperty>;
}>();

const listData = computed(() => {
  return Array.isArray(props.units) ? props.units : props.units.data;
});
</script>

<template>
  <AppLayout title="Propiedades">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <AppButton variant="secondary" @click="router.visit(route('properties.create'))"
            >Agregar Propiedad
          </AppButton>
        </template>
      </PropertySectionNav>
    </template>

    <main class="p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <AtTable
        :table-data="listData"
        :cols="cols"
        class="bg-white rounded-md text-body-1"
      />
    </main>
  </AppLayout>
</template>
