<template>
<div class="flex space-x-2">
 <AtField label="Nombre">
    <AtInput v-model="state.taxData.name" @input="update" />
 </AtField>
 <AtField label="Monto/Porcentaje">
    <AtInput v-model="state.taxData.rate" @input="update">
      <template #suffix>
        <div class="h-10 mt-auto taxes-box__type">
          <div class="flex h-full">
              <button
                  v-for="type in state.types"
                  class="w-6 px-1 text-gray-400 border border-gray-200 focus:outline-none"
                  :class="{'bg-gray-200 text-gray-600': state.taxData.type === type.value}"
                  @click="setType(type.value)"
              >
                  {{ type.name }}
              </button>
          </div>
      </div>
      </template>
    </AtInput>
 </AtField>
 <AtField label="Tipo">
    <AtSimpleSelect v-model="state.taxData.type" v-model:selected="state.taxData.Type" @input="update" :options="[{
      label: 'Comision Prestamo'
    }, {
      label: 'Comision Propiedades'
    }]"/>
 </AtField>
 <AtField label="Acciones">
    <div class="flex">
        <button class="h-10 px-2 mt-auto ml-2 transition border focus:outline-none hover:text-gray-900 hover:bg-gray-200" @click="$emit('remove')">
          <IconTrash class="w-4 h-4 text-gray-400" /> 
        </button>
    </div>
 </AtField>
</div>
</template>

<script setup>
import { AtField, AtInput, AtSimpleSelect } from 'atmosphere-ui';
import { reactive, watch } from 'vue';
import IconTrash  from "@/Components/icons/IconTrash.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({
            name: '',
            rate: 0,
            description: '',
            type: 1,
            is_default: false
        })
    }
})

const emit = defineEmits(['modelValue:update'])

const state = reactive({
    types: [
        {
            name: '%',
            value: 1
        },
        {
            name: '$',
            value: 2
        }
    ],
    taxData: {
        ...props.modelValue
    },
})

watch(() => props.modelValue, () => {
    state.taxData = props.modelValue;
})

const update = () => {
    emit('update:modelValue', state.taxData);
}

const setType = (type) => {
    state.taxData.type = type;
    update();
}


</script>
