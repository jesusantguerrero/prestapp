<script setup lang="ts">
import { watch } from "vue";
import { AtButton, AtInput, AtTextarea } from "atmosphere-ui";
import { router, useForm } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import AppButton from "../../Components/shared/AppButton.vue";

import { propertyTypes } from "@/Modules/properties/constants";
import BaseSelect from "@/Components/shared/BaseSelect.vue";
import { IClient } from "@/Modules/clients/clientEntity";
import AppFormField from "@/Components/shared/AppFormField.vue";
import { IUnit } from "@/Modules/properties/propertyEntity";
import AppSectionHeader from "@/Components/AppSectionHeader.vue";

const props = defineProps(["properties", "clients"]);

const formData = useForm({
  name: "",
  description: "",
  owner: null,
  owner_id: "",
  address: "",
  property_type: "",
  price: 0,
  units: [
    {
      price: 0,
      description: "",
    },
  ],
});

watch(
  () => props.properties,
  (propertyEntry) => {
    if (propertyEntry) {
      setInitialForm(propertyEntry, formData);
    }
  },
  { immediate: true, deep: true }
);

function setInitialForm(entry: Record<string, any>, formData: Record<string, any>) {
  Object.keys(formData).forEach((field) => {
    formData[field] = entry[field] || formData[field];
    if (field == "owner_id") {
      formData.owner = props.clients.find(
        (client: IClient) => client?.id == formData["owner_id"]
      );
    }
  });
}

const client = null;

// api calls
const onSubmit = () => {
  const isEditing = props.properties?.id;
  const method = isEditing ? "put" : "post";
  const param = isEditing ? `/${props.properties.id}` : "";

  formData
    .transform((data) => {
      return {
        ...data,
        owner_id: data.owner?.id,
      };
    })
    [method](`/properties${param}`, {
      onsuccess() {
        route.visit(route("properties"));
      },
    });
};

const addUnit = () => {
  const index = formData.units.length + 1;
  formData.units.push({
    index: index,
    description: "",
    price: 0,
  });
};

const removeUnit = (indexToDelete: number) => {
  const index = formData.units.length + 1;
  formData.units = formData.units.filter((unit, index) => index !== indexToDelete);
};

const onCancel = () => {
  if (props.properties.id) {
    return router.visit(`/properties/${props.properties.id}`);
  }
  return router.visit(`/properties/`);
};
</script>

<template>
  <AppLayout :title="$t('Add property')">
    <template #header>
      <PropertySectionNav>
        <template #actions v-if="!properties?.id">
          <section class="flex justify-end space-x-2">
            <AtButton
              class="font-bold text-red-400 bg-gray-100 rounded-md"
              variant="secondary"
              @click="onCancel()"
            >
              Cancelar
            </AtButton>
            <AppButton variant="inverse" @click="onSubmit"> Guardar propiedad </AppButton>
          </section>
        </template>
      </PropertySectionNav>
    </template>

    <main class="mx-auto pt-5 mt-8 text-gray-500 sm:px-6 lg:px-8">
      <AppSectionHeader
        v-if="properties?.id"
        name=""
        class="px-5 border-2 border-white rounded-md rounded-b-none shadow-md"
        :resource="properties"
        :title="properties?.name"
        @create="router.visit('/properties/create')"
        hide-action
      >
        <template #actions>
          <section class="flex space-x-2">
            <AtButton
              class="font-bold text-red-400 bg-gray-100 rounded-md"
              variant="secondary"
              @click="onCancel()"
            >
              Cancelar
            </AtButton>
            <AppButton variant="secondary" @click="onSubmit">
              <IMdiContentSaveCheck class="ml-2" />
              Guardar propiedad
            </AppButton>
          </section>
        </template>
      </AppSectionHeader>
      <div class="w-full px-5 py-4 space-y-4 text-gray-600 bg-white rounded-md shadow-md">
        <div class="flex space-x-5">
          <AppFormField
            label="Dirección"
            v-model="formData.address"
            class="w-full border-b focus:outline-none"
            placeholder="Dirección"
            rounded
            required
          />
          <AppFormField class="w-4/12" label="Dueño de propiedad" required>
            <BaseSelect
              v-model="formData.owner"
              endpoint="/api/clients?filter[is_owner]=1"
              placeholder="Selecciona un dueño"
              label="display_name"
              track-by="id"
            />
          </AppFormField>
        </div>
        <div class="md:flex md:space-x-2 space-y-4">
          <AppFormField
            label="Nombre de propiedad"
            v-model="formData.name"
            class="w-full"
            placeholder="Nombre de propiedad"
            rounded
          />
          <AppFormField class="w-full" label="Tipo de propiedad" required>
            <BaseSelect
              v-model="formData.type"
              @update:model-value="formData.property_type = $event.name"
              :options="propertyTypes"
              placeholder="Selecciona un tipo"
              label="label"
              track-by="name"
            />
          </AppFormField>
        </div>

        <!-- Units -->
        <template v-if="!properties?.id">
          <section v-for="(unit, index) in formData.units" class="">
            <header class="flex space-x-4 items-center justify-between">
              <section class="grid grid-cols-4 gap-4 w-full">
                <AppFormField class="w-full" label="Precio de Renta" required>
                  <AtInput v-model="unit.price" class="w-full" rounded number-format />
                </AppFormField>
                <AppFormField class="w-full" label="Area" v-model="unit.area" rounded>
                  <template #prefix>
                    <span class="inline-blocks h-full flex items-center px-2">
                      <i-ic-sharp-photo-size-select-small />
                    </span>
                  </template>
                </AppFormField>
                <AppFormField
                  class="w-full"
                  label="Habitaciones"
                  v-model="unit.bedrooms"
                  rounded
                >
                  <template #prefix>
                    <span class="inline-blocks h-full flex items-center px-2">
                      <IIcTwotoneBed />
                    </span>
                  </template>
                </AppFormField>
                <AppFormField
                  class="w-full"
                  label="Baños"
                  v-model="unit.bathrooms"
                  rounded
                  placeholder="0"
                >
                  <template #prefix>
                    <span class="inline-blocks h-full flex items-center px-2">
                      <IIcTwotoneBathtub />
                    </span>
                  </template>
                </AppFormField>
              </section>
              <button
                class="bg-error/60 mt-4 justify-center flex text-white items-center h-8 w-8 rounded-full"
                @click="removeUnit(index)"
              >
                <IMdiMinus />
              </button>
            </header>
            <AppFormField label="Notas/Detalles">
              <AtTextarea
                v-model="unit.description"
                class="w-full p-2 border focus:outline-none"
                placeholder="Descripcion de la propiedad"
              />
            </AppFormField>
          </section>
          <AtButton class="text-primary hover:font-bold" @click="addUnit()">
            <i class="mr-2 fa fa-plus-circle"></i>
            Agregar unidad
          </AtButton>
        </template>
      </div>
    </main>
  </AppLayout>
</template>
