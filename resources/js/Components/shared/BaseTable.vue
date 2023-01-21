<template>
  <ElTable
    class="table-fixed"
    style="width: 100%"
    :data="tableData"
    :header-cell-class-name="getHeaderClass"
    @sort-change="$emit('sort', $event)"
    @row-click="$emit('row-click', $event)"
  >
    <ElTableColumn
      v-for="col in cols"
      :prop="col.name"
      :key="col.name"
      :label="col.label || col.name"
      cell-class-name="px-2 py-4"
      :header-cell-class-name="col.headerClass"
      :class-name="col.class"
      :width="col.width"
      :min-width="col.minWidth"
      :class="[col.headerClass]"
    >
      <template v-slot="scope" v-if="$slots[col.name] || col.render">
        <slot :name="col.name" v-bind:scope="scope">
          <CustomCell v-if="col.render" :class="col.class" :col="col" :data="scope.row" />
        </slot>
      </template>
    </ElTableColumn>
  </ElTable>
</template>

<script setup>
import formatMoney from "@/utils/formatMoney";
import CustomCell from "../customCell";

defineProps({
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
  showPrepend: {
    type: Boolean,
    default: false,
  },
  showAppend: {
    type: Boolean,
    default: false,
  },
  emptyText: {
    type: String,
    default: "No data found",
  },
  hideEmptyText: {
    type: Boolean,
    default: false,
  },
  hideHeaders: {
    type: Boolean,
    default: false,
  },
});

const getHeaderClass = ({ row }) => {
  return row.headerClass;
};
</script>

<style lang="scss">
.section-actions {
  display: flex;
  background: white;

  .app-search__container {
    width: 70%;
    margin-right: 15px;
  }

  .action-buttons {
    width: 30%;
    display: flex;

    .btn {
      text-align: center !important;
    }
  }
}
.pagination-container {
  background: white;
  height: 42px;
  width: 100%;
}

.el-table .cell {
  padding: 8px 4px;
}

.el-pagination {
  display: flex;
  align-items: center;

  .btn-next .el-icon,
  .btn-prev .el-icon,
  .el-pager li {
    font-size: 16px;
  }
}

.el-loading-mask {
  z-index: 999;
}
</style>
