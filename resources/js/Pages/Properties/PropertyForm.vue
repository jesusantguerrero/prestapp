<script setup>
import { watch } from "vue";
import { AtButton, AtField, AtInput, AtSelect, AtTextarea } from "atmosphere-ui";
import { useForm } from "@inertiajs/vue3";

import AppLayout from "@/Components/templates/AppLayout.vue";
import PropertySectionNav from "./Partials/PropertySectionNav.vue";
import AppButton from "../../Components/shared/AppButton.vue";

import { propertyTypes } from "@/Modules/properties/constants";

const props = defineProps(["properties", "clients"]);

const formData = useForm({
  name: "",
  description: "",
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

function setInitialForm(entry, formData) {
  Object.keys(formData).forEach((field) => {
    formData[field] = entry[field] || formData[field];
    if (field == "owner_id") {
      formData.owner = props.clients.find((client) => client.id == formData["owner_id"]);
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
    .transform((data) => ({
      ...data,
    }))
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
</script>

<template>
  <AppLayout title="Agregar propiedad">
    <template #header>
      <PropertySectionNav>
        <template #actions>
          <section class="flex justify-end space-x-2">
            <AtButton
              class="font-bold text-red-400 bg-gray-100 rounded-md"
              variant="secondary"
              @click="goToList()"
            >
              Cancelar
            </AtButton>
            <AppButton variant="inverse" @click="onSubmit"> Guardar propiedad </AppButton>
          </section>
        </template>
      </PropertySectionNav>
    </template>

    <main class="mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8">
      <div class="w-full px-5 py-4 space-y-4 text-gray-600 bg-white rounded-md shadow-md">
        <div class="flex space-x-5">
          <AtField class="w-full" label="Dirección">
            <AtInput
              v-model="formData.address"
              class="w-full border-b focus:outline-none"
              placeholder="Dirección"
            />
          </AtField>
          <AtField class="w-4/12" label="Dueño de propiedad">
            <AtSelect
              v-model="formData.owner_id"
              v-model:selected="formData.owner"
              :options="clients"
              placeholder="Selecciona un dueño"
              label="display_name"
              key-track="id"
            />
          </AtField>
        </div>
        <div class="md:flex md:space-y-0 md:space-x-2 space-y-4">
          <AtField class="w-full" label="Nombre de propiedad">
            <AtInput
              v-model="formData.name"
              class="w-full"
              placeholder="Nombre de propiedad"
              rounded
            />
          </AtField>
          <AtField class="w-full" label="Tipo de propiedad">
            <AtSelect
              v-model="formData.property_type"
              v-model:selected="formData.type"
              :options="propertyTypes"
              placeholder="Selecciona un tipo"
              label="label"
              key-track="value"
            />
          </AtField>
        </div>

        <!-- Units -->
        <section
          class="space-x-4 grid grid-cols-[196px,1fr]"
          v-for="unit in formData.units"
        >
          <AtField class="w-full" label="Precio de Renta">
            <AtInput v-model="unit.price" class="w-full" rounded number-format />
          </AtField>
          <AtField label="Descripción">
            <AtTextarea
              v-model="unit.description"
              class="w-full p-2 border focus:outline-none"
              placeholder="Descripcion de la propiedad"
            />
          </AtField>
        </section>
        <AtButton class="text-gray-400" @click="addUnit()">
          <i class="mr-2 fa fa-plus-circle"></i>
          Add unit
        </AtButton>
      </div>
    </main>
  </AppLayout>
</template>
