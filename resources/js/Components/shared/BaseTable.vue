<script setup lang="ts">
import { computed } from "vue";
import CustomCell from "../customCell";
import AppSearch from "./AppSearch/AppSearch.vue";
import { useResponsive } from "@/utils/useResponsive";

const { isMobile } = useResponsive();

const props = defineProps({
  selectable: {
    type: Boolean,
    default: false,
  },
  defaultExpandAll: {
    type: Boolean,
  },
  showSummary: {
    type: Boolean,
  },
  summaryMethod: {
    type: [null, Function],
  },
  cols: {
    type: Array,
    required: true,
  },
  hiddenCols: {
    type: [Array, null],
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
  pagination: {
    type: Object,
    default() {
      return {};
    },
  },
  total: {
    type: Number,
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
  responsive: {
    type: Boolean,
    default: false,
  },
  tableClass: {
    default: "px-4",
  },
  layout: {
    type: String,
    default: "table",
  },
});

const getHeaderClass = (row: Record<string, any>) => {
  return row.headerClass;
};

const visibleCols = computed(() => {
  return !props.hiddenCols
    ? props.cols
    : props.cols.filter((col) => !props.hiddenCols.includes(col.name));
});
</script>

<template>
  <section>
    <section class="flex justify-between items-center" :class="{ 'py-4': config.search }">
      <div class="w-full px-4" v-if="config.search">
        <AppSearch class="w-96" v-model="pagination.search" @search="$emit('search')" />
      </div>
      <ElPagination
        v-if="config.pagination"
        class="w-full flex justify-end pr-4 py-4"
        background
        @current-change="$emit('paginate', $event)"
        @size-change="$emit('size-change', $event)"
        layout="total,prev, pager, next,sizes"
        :current-page="pagination.page"
        :page-sizes="[10, 20, 50, 100, 200]"
        :page-size="pagination.limit"
        :total="total"
      />
    </section>
    <section :class="tableClass">
      <div
        class="space-y-2"
        v-if="layout == 'grid' || (responsive && $slots.card && isMobile)"
      >
        <slot name="card" :row="row" v-for="row in tableData"></slot>
      </div>
      <ElTable
        v-else
        class="table-fixed"
        style="width: 100%"
        :default-expand-all="defaultExpandAll"
        :show-summary="showSummary"
        :summary-method="summaryMethod"
        :data="tableData"
        :header-cell-class-name="getHeaderClass"
        @sort-change="$emit('sort', $event)"
        @row-click="$emit('row-click', $event)"
        @selection-change="$emit('selection-change', $event)"
      >
        <ElTableColumn type="selection" width="55" v-if="selectable" />
        <ElTableColumn type="expand" v-if="$slots.expand">
          <template #default="props">
            <slot name="expand" :row="props.row" />
          </template>
        </ElTableColumn>
        <ElTableColumn
          v-for="col in visibleCols"
          :prop="col.name"
          :key="col.name"
          :label="col.label || col.name"
          cell-class-name="px-2 py-4"
          :label-class-name="col.headerClass"
          :header-align="col.align"
          :class-name="col.class"
          :width="col.width"
          :min-width="col.minWidth"
          :class="[col.headerClass]"
        >
          <template v-slot="scope" v-if="$slots[col.name] || col?.render || col.formula">
            <slot :name="col.name" v-bind:scope="scope">
              <CustomCell
                v-if="col.render"
                :class="col.class"
                :col="col"
                :data="scope.row"
              />
            </slot>
          </template>
        </ElTableColumn>
      </ElTable>
    </section>
    <section class="flex justify-between items-center py-4" v-if="config.pagination">
      <div class="w-full"></div>
      <div class="w-full flex justify-end">
        <ElPagination
          class="w-full flex justify-end pr-4"
          background
          @current-change="$emit('paginate', $event)"
          @size-change="$emit('size-change', $event)"
          :current-page="pagination.page"
          layout="total,prev, pager, next,sizes"
          :page-sizes="[10, 20, 50, 100, 200]"
          :page-size="pagination.limit"
          :total="total"
        />
      </div>
    </section>
  </section>
</template>

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
