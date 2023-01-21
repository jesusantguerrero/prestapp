<template>
  <div class="flex items-center app-search">
    <div :class="containerClasses" class="col-search">
      <div class="input-group search-container" :class="state.classes">
        <div class="input-group-prepend">
          <label class="input-group-text" :for="state.id"
            ><font-awesome-icon :icon="icon"
          /></label>
        </div>
        <input
          type="search"
          class="form-control"
          :placeholder="placeholder"
          name="main-search"
          :id="state.id"
          :autocomplete="$attrs.autocomplete"
          @focus="focus"
          @blur="blur"
          @keydown="$emit('keydown', $event)"
          @keypress="$emit('keypress', $event)"
          @keyup="$emit('keyup', $event)"
          @click="$emit('click', $event)"
          :value="value"
          @input="$emit('input', $event.target?.value)"
        />

        <div class="input-group-append" v-if="includeDates && showButton">
          <label class="input-group-text" :for="`${state.id}-first-date`"
            ><font-awesome-icon icon="calendar"
          /></label>
        </div>
      </div>
    </div>

    <div class="col-md-4 dates-container" v-if="includeDates">
      <slot name="date" v-bind="{ dates: state.dates, emitDates }">
        <div class="d-flex dates-container__group">
          <ElDatePicker
            v-model="state.dates.start"
            :id="`${state.id}-first-date`"
            type="date"
            title="seleccione una fecha"
            placeholder="selecciona una fecha"
            @change="emitDates"
          />
          <ElDatePicker
            v-model="state.dates.end"
            :id="`${state.id}-end-date`"
            type="date"
            title="seleccione una fecha"
            placeholder="selecciona una fecha"
            @change="emitDates"
          />
        </div>
      </slot>
    </div>

    <div class="col-md-2" v-if="includeFilters">
      <slot name="filter">
        <select name="" id="" class="form-control">
          <option value="">One</option>
          <option value="">Two</option>
          <option value="">Three</option>
        </select>
      </slot>
    </div>
  </div>
</template>

<script setup lang="ts">
// @ts-ignore
import { v4 as uuid } from "uuid";
import { lastDayOfYear, format as formatDate } from "date-fns";
import { reactive } from "vue";

const props = defineProps({
  placeholder: {
    type: String,
    default: "Type to search",
  },
  icon: {
    type: String,
    default: "search",
  },
  value: {
    type: String,
    default: "",
  },
  includeDates: Boolean,
  includeFilters: Boolean,
  includeSort: Boolean,
  includeSlots: Boolean,
  showButton: {
    type: Boolean,
    dafault: true,
  },
});

const emit = defineEmits(["date-changed"]);

const state = reactive({
  classes: {},
  id: `${uuid()}-main-search`,
  dates: {
    start: new Date(),
    end: lastDayOfYear(new Date()),
  },
});

const blur = () => {
  state.classes = { focus: false };
};

const focus = () => {
  state.classes = { focus: true };
};

interface IDateRange {
  start: Date;
  end: Date;
}
const emitDates = (dates: IDateRange) => {
  const date = dates || state.dates;
  emit(
    "date-changed",
    formatDate(date.start, "YYYY-MM-DD"),
    formatDate(date.end, "YYYY-MM-DD")
  );
};
const containerClasses = () => {
  const includedCompoments = [
    props.includeSort,
    props.includeDates,
    props.includeFilters,
    props.includeSlots,
  ];
  const result = includedCompoments.every((value) => !value);
  return result ? "col-md-8" : "col-md-4";
};
</script>

<style lang="scss" scoped>
.input-group-prepend label {
  background: white;
  border-right: 0;
  cursor: pointer;
}
.input-group-prepend {
  label {
    background: white;
    border-right: 0;
    border-radius: 0 0 0 0 !important;
    cursor: pointer;
  }
}
.input-group-append {
  label {
    background: white;
    border-left: 0;
    border-right: 0;
    border-radius: 0 0 0 0 !important;
    cursor: pointer;
  }
}

input[type="search"] {
  border-left: 0;
  border-radius: 0 0 0 0 !important;
}

.search-container {
  transition: all ease 0.3s;
}
.search-container.focus {
  input {
    outline: none !important;
    box-shadow: none !important;
    border: 0 solid !important;
  }

  label {
    color: var(--primary-color);
    border: 0 solid !important;
  }

  border: 0 solid !important;
  outline-color: var(--primary-color);
  outline: 1px solid var(--primary-color);
  box-shadow: 0 0 0 0.2rem transpaterize(#087a9c, 0.9);
}
</style>

<style lang="scss">
.el-date-editor.el-input {
  width: auto !important;
}
.app-search {
  .el-date-editor.el-input,
  .el-date-editor.el-input__inner {
    width: 100%;
    height: 100% !important;
    input {
      border: 0 !important;
    }
  }
}

.col-search {
  padding-right: 0 !important;
}

.dates-container {
  padding-left: 0 !important;
  height: 38px;
  border: 1px solid #ddd;
  border-left: none;
  overflow: hidden;

  &__group {
    height: 100%;
  }
  .el-input--prefix .el-input__inner {
    border: none !important;
    height: 100%;
    background: transparent;

    &:focus {
      color: var(--primary-color);
    }
  }

  .el-input__icon {
    line-height: 35px;
    background: transparent;
  }
}
</style>
