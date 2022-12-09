<script setup>
  import { reactive, nextTick, ref } from 'vue'
  import { Inertia } from '@inertiajs/inertia'
  import { format, parseISO, toDate } from 'date-fns'

  import AppLayout from '@/Components/templates/AppLayout.vue'
  import AppSectionHeader from '@/Components/AppSectionHeader.vue'
  import { AtField, AtSelect } from 'atmosphere-ui'
  import { useForm } from '@inertiajs/vue3'

  const props = defineProps([
    'form',
    'clients'
  ]);
  
const formData = useForm({
  name: '',
  code_name: '',
  description: '',
  owner_id: '',
  address: ''
});

const client = null;

const ActionButtons = ref(null);

const checkScroll = () => {
    nextTick(() => {
        ActionButtons.value.scrollIntoView({ smooth: true })
    })
}

// actions
const onCopy = (field) => {
    field.uuid = uuid();
    state.formData.boards.push(field)
    checkScroll()
}

const onDelete = (index) => {
    state.formData.boards.splice(index, 1)
    checkScroll()
}

const addFieldBlock = () => {
    const fieldName = uuid();
    const index = state.formData.boards.length + 1
    state.formData.boards.push({
        name: fieldName,
        title: `Board ${index}`,
        description: '',
        type: 'board',
        template: 'lists',
        color: [],
        show_standup: false,
        show_in_home: 0
    })

    checkScroll()
}

const addServiceBlock = () => {
    const index = state.formData.services.length + 1
    state.formData.services.push({
        index: index,
        concept: '',
        description: '',
        price: 0,
        quantity: 1,
        total: ''
    })

    checkScroll()
}

// api calls
const saveForm = () => {
    const method = formData.id ? "put" : "post";
    const param = formData.id ? `/${formData.id}` : "";

    formData.transform(data=> ({
      ...data,
    }))[method](`/properties${param}`, formData)
}
</script>

<template>
  <AppLayout title="Contratos de alquiler">
    <main class="p-5 mx-auto text-gray-500 sm:px-6 lg:px-8">
      <AppSectionHeader
          name="Propiedades"
          @saved="saveForm()"
          @create="saveForm()"
          class="px-4"
      />

      <div class="w-full px-5 py-10 space-y-5 text-gray-600 bg-white rounded-md shadow-md">
          <div class="flex space-x-5">
              <div class="w-9/12 text-3xl font-bold">
                  <input v-model="formData.name" class="w-full font-bold border-b focus:outline-none" placeholder="Nombre de propiedad" >
              </div>
              <AtField class="w-4/12" label="Dueño de propiedad">
                <AtSelect
                    v-model="formData.owner_id"
                    v-model:selected="formData.owner"
                    :options="clients"
                    placeholder="Selecciona un dueño"
                    label="names"
                    key-track="id"
                />
            </AtField>
          </div>
          <div class="space-y-4">
              <textarea v-model="formData.description" class="w-full border-b focus:outline-none" placeholder="Descripcion" />
              <textarea v-model="formData.address" class="w-full border-b focus:outline-none" placeholder="Direccion" />
          </div>
      </div>

      <at-board-block
          v-for="(field, index) in formData.boards" :key="`field-${index}`"
          :field="field"
          class="shadow-md"
          @delete="onDelete(index)"
          @copy="onCopy(field)"
      >
      </at-board-block>
    </main>
  </AppLayout>
</template>
