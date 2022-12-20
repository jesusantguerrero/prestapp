<template>
  <div class="w-full">
    <AtTable :cols="renderedCols" :tableData="tableData" :hide-empty-text="true">
      <template v-slot:item="{ scope }">
        <div class="d-flex">
          <AtInput name="" v-model="scope.row.concept" class="bg-transparent border-0 border-transparent form-control" v-if="isEditing"/>
          <span v-else> {{ scope.row.concept }}</span>
        </div>
      </template>

      <template v-slot:quantity="{ scope }"  v-if="isEditing">
        <AtInput
          name=""
          min="1"
          type="number"
          v-model="scope.row.quantity"
          class="form-control"
        />
      </template>

      <template v-slot:discount="{ scope }"  v-if="isEditing">
        <AtInput
          class="form-control"
          type="number"
          max="100"
          min="0"
          :options="cleaveOptions.percent"
          v-model="scope.row.discount"
        />
      </template>

      <template v-slot:price="{ scope }"  v-if="isEditing">
        <div class="space-y-2">
            <AtInput
                v-model="scope.row.price"
                type="number"
                v-bind="cleaveOptions.money"
            />
            <div class="flex items-center space-x-1" v-for="(tax, index) in scope.row.taxes" :key="`tax-${index}`">
                <AtSelect
                    :options="availableTaxes"
                    :modelValue="taxes"
                    v-model:selected="scope.row.taxes[index]"
                    placeholder="Tax"
                    option-template="${name} - ${rate}%"
                    class="w-full"
                />
                <button class="h-10 px-2 mt-auto ml-2 transition border focus:outline-none hover:text-gray-900 hover:bg-gray-200"  @click="removeTax(scope.$index, index)"><IconTrash class="w-4 h-4 text-gray-400" /> </button>
            </div>
        </div>
      </template>

      <template v-slot:actions="{ scope }" v-if="isEditing">
        <button
          @click="removeRow(scope.$index, scope.row)"
          class="invoice-grid__remove-row"
        >
          <font-awesome-icon icon="trash-alt" />
        </button>
      </template>

      <!-- table slots -->
      <template v-slot:append v-if="isEditing">
        <button
          @click="addMode = true"
          class="invoice-grid__add-row"
          v-if="!addMode"
        >
          Add Row
        </button>
        <div v-else class="service-select">
          <AtSelect
            v-model:selected="rowToAdd"
            :options="products"
            label="name"
            key-track="id"
            @update:selected="addRow()"
          />

          <button @click="addMode = false" class="invoice-grid__remove-row">
            <i class="fa fa-trash-alt" />
          </button>
        </div>
      </template>
    </AtTable>
  </div>
</template>

<script setup>
import { AtInput, AtTable } from "atmosphere-ui";
import { computed, reactive, toRefs } from "vue"

import IconTrash from "@/Components/icons/IconTrash.vue";

import cols from "./cols";

const props = defineProps({
    tableData: {
      type: Array,
      default() {
          return []
      }
    },
    availableTaxes: {
      type: Array,
      default() {
          return []
      }
    },
    products: {
        type: Array,
        default() {
            return [];
        }
    },
    resourceUrl: {
      type: String,
      default: "/services?filter[name]=%${query}%&relationships=stock"
    },
    isEditing: {
        type: Boolean,
        default: true
    },
    taxes: {
        type: Array,
        default() {
            return [];
        }
    }
})

const emit = defineEmits(['taxes-updated'])

const state = reactive({
    cleaveOptions: {
      percent: {
        numericOnly: true,
        blocks: [3]
      },
      money: {
        decimal: ".",
        thousands: ",",
        precision: 2,
        masked: false
      }
    },
    services: [],
    isLoading: false,
    rowToAdd: {},
    addMode: false,
    renderedCols: computed(() => {
        return props.isEditing ? cols : cols.filter(col => col.name != 'actions')
    })
})

const addRow = () => {
    const itemTaxes = state.rowToAdd.taxes.length ? state.rowToAdd.taxes : [];
    props.tableData.push({
        product_name: state.rowToAdd.name,
        concept: state.rowToAdd.name,
        product_id: state.rowToAdd.id,
        quantity: 1,
        discount: 0,
        price: state.rowToAdd.price.value || 0,
        amount: 0,
        taxes: [
            ...itemTaxes,
            {
                id: 'new',
            }],
    });

    state.rowToAdd = {};
    state.addMode = false;
}

const removeRow = (index, row) => {
      const isConfirmed = confirm("Do you want to delete this?");
      if (!isConfirmed) {
        return;
      }
      const deleted = { ...row };
      state.tableData.splice(index, 1);
      emit("deleted", deleted);
}

const removeTax = (rowIndex, taxIndex) => {
   const itemRow = props.tableData[rowIndex];
   const taxes = itemRow.taxes.filter((_tax, index) => index !== taxIndex)
   emit('taxes-updated', { rowIndex, taxes })
}

const { rowToAdd, renderedCols, addMode, cleaveOptions } = toRefs(state)
</script>

<style lang="scss">
.el-table,
.el-table__body-wrapper {
  overflow: visible;

  td {
    padding: 0 0 0 0 !important;
  }

  .form-control,
  td {
    border: none;
    background: transparent;
    font-size: 13px;

    &:focus {
      outline: none;
      box-shadow: none;
    }
  }
}
.el-table__append-wrapper {
  overflow: visible;
}
</style>

<style lang="scss" scoped>
.invoice-grid {
  &__add-row {
    width: 100%;
    height: 34px;
    color: dodgerblue;
    background: white;
    border: none;
    font-weight: bolder;
    transition: all ease 0.3s;

    &:hover {
      font-size: 1.1em;
    }

    &:focus {
      outline: none;
    }
  }

  &__remove-row {
    background: transparent;
    color: red;
    border: none;
  }
}

.service-select {
  display: grid;
  grid-template-columns: 95% 5%;
  padding: 12px 10px;
  overflow: visible;
}
</style>
