<template>
  <ElTable
    v-loading="isLoading"
    stripe
    :style="{ width: '100%' }"
    :data="tableData"
    :header-cell-class-name="getHeaderClass"
    @sort-change="$emit('sort', $event)"
    @row-click="$emit('row-click', $event)"
  >
    <ElTableColumn v-if="config.selectable" type="selection" width="55"> </ElTableColumn>

    <ElTableColumn
      v-for="col in cols"
      :fixed="col.fixed"
      :key="col.name"
      :prop="col.name"
      :label="col.label"
      :width="col.width"
      :min-width="col.minWidth"
      :sortable="col.sortable"
      :cell-class-name="col.class"
      :header-cell-class-name="col.headerClass"
    >
      <template slot-scope="{ row }">
        <slot :name="col.name" v-bind:scope="scope">
          <div v-if="col.type == 'calc'" :class="col.class">
            {{ col.formula(row) }}
          </div>
          <span
            v-html="col.render(scope.row)"
            :class="col.class"
            v-else-if="col.render"
          />
        </slot>
      </template>
    </ElTableColumn>

    <template v-slot:append>
      <slot name="append"> </slot>
    </template>
  </ElTable>
</template>

<script>
export default {
  props: {
    cols: {
      type: Array,
      required: true,
    },
    isLoading: {
      type: Boolean,
      default: false,
    },
    tableData: {
      type: Array,
    },
    config: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  methods: {
    getHeaderClass({ row }) {
      return row.headerClass;
    },
  },
};
</script>
