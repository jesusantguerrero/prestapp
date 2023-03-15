/* eslint-disable vue/valid-v-on */
<script lang="ts" setup>
import { formatMoney, formatDate } from "@/utils";
import { computed, reactive, watch, toRefs } from "vue";
import ExactMath from "exact-math";
import AppButton from "@/Components/shared/AppButton.vue";

const props = defineProps({
  tableData: {
    type: Array,
    required: true,
  },
  subtotalField: {
    type: String,
    required: true,
  },
  discountField: {
    type: String,
    required: true,
  },
  payments: {
    type: Array,
  },
  totalField: {
    type: String,
    required: true,
  },
  totalValues: {
    type: Object,
    required: true,
  },
  isTaxIncluded: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["edit-payment", "delete-payment"]);

const getRowTaxes = (row: Record<string, string>, taxFields: string[]) => {
  const taxes = {};
  taxFields.forEach((taxField) => {
    taxes[taxField] = row[taxField];
  });
  return taxes;
};

const calculateTaxableAmount = (amount, taxRate) => {
  return ExactMath.formula(`(${amount} / ((100 + ${taxRate}) / 100))`);
};

const calculateRowTaxes = (
  rowTotal: number,
  taxes: Record<string, any>,
  total: Record<string, any>
) => {
  let taxesRowTotal = 0;
  let rowSubtotal = 0;
  taxes.forEach((tax: Record<string, any>) => {
    const taxLabel = `${tax.label || tax.name} ${tax?.rate}%`;
    if (taxLabel && tax.rate) {
      const taxableTotal = props.isTaxIncluded
        ? calculateTaxableAmount(rowTotal, tax.rate)
        : rowTotal;
      const rowTax =
        ExactMath.formula(`${taxableTotal} * (${tax.rate} / ${100})`) * (tax.type ?? 1);
      total.taxes[taxLabel] = (total.taxes[taxLabel] || 0) + (rowTax || 0);
      total.taxesTotal += rowTax;
      taxesRowTotal += rowTax;
      rowSubtotal += taxableTotal;
    }
  });

  return {
    taxesRowTotal,
    rowSubtotal,
  };
};

const state = reactive({
  totals: computed(() => {
    const totals = props.tableData.reduce(
      (total: Record<string, any>, row: Record<string, any>) => {
        const rowTotal = row[props.totalField];
        const { rowSubtotal, taxesRowTotal } = calculateRowTaxes(
          rowTotal,
          row.taxes,
          total
        );

        const subtotal = rowSubtotal || rowTotal;
        total.subtotal += subtotal;
        total.discountTotal += Number(row[props.discountField]);
        total.total += ExactMath.add(subtotal, taxesRowTotal ?? 0);
        total.taxesTotal += taxesRowTotal;

        return total;
      },
      {
        total: 0,
        discountTotal: 0,
        subtotal: 0,
        taxesTotal: 0,
        taxes: {},
      }
    );

    return totals;
  }),
  hasTaxes: computed(() => {
    return Object.keys(state.totals.taxes).length > 0;
  }),

  paymentTotal: computed(() => {
    if (props.payments && props.payments.length) {
      return props.payments.reduce((total, payment) => {
        return (total += parseFloat(payment.amount));
      }, 0);
    }
    return 0;
  }),

  debt: computed(() => {
    return state.totals.total - state.paymentTotal;
  }),
});

watch(
  () => state.totals,
  () => {
    if (state.totals && Object.keys(state.totals).length) {
      Object.keys(state.totals).forEach((key) => {
        props.totalValues[key] = state.totals[key];
      });
    }
  },
  {
    deep: true,
    immediate: true,
  }
);

const { totals, debt, hasTaxes, paymentTotal } = toRefs(state);
</script>

<template>
  <div class="invoice-totals">
    <p class="total-labels">
      <span class="label">Subtotal: </span>
      <span class="value">{{ formatMoney(totals.subtotal) }}</span>
    </p>
    <template v-if="hasTaxes">
      <p class="total-labels" v-for="(tax, taxName) in totals.taxes">
        <span class="label">{{ taxName }}: </span>
        <span class="value">{{ formatMoney(tax) }}</span>
      </p>
    </template>

    <!-- <p class="total-labels">
      <span class="label">Descuento:</span>
      <span class="value">{{ formatMoney(totals.discount) }}</span>
    </p> -->

    <p class="total-labels total-remark">
      <span class="label"> Total: </span>
      <span class="value">{{ formatMoney(totals.total) }}</span>
    </p>

    <div v-if="payments && payments.length">
      <i
        class="text-green-500 total-labels payment-label group"
        v-for="payment in payments"
        :key="payment.id"
        @click.stop="$emit('edit-payment', payment)"
      >
        <p class="label flex">Pagado en {{ formatDate(payment.payment_date) }}</p>
        <p class="value flex items-center justify-end text-right">
          - {{ formatMoney(payment.amount) }}
          <AppButton
            class="text-gray hover:text-error group-hover:flex hidden"
            @click.stop="$emit('delete-payment', payment)"
          >
            <IMdiTrash class="ml-2" />
          </AppButton>
        </p>
      </i>
    </div>

    <p class="total-labels total-remark">
      <span class="label"> Deuda: </span>
      <span class="value">{{ formatMoney(debt) }}</span>
    </p>
    <slot name="add-payment" v-if="debt" :slot-scope:scope="{ debt }"> </slot>
  </div>
</template>

<style lang="scss">
.total-labels {
  color: #909399;
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-column-gap: 5px;

  &.payment-label {
    cursor: pointer;
    @apply text-green-500;
  }

  .value {
    text-align: right;
    color: #666;
    font-weight: bold;
  }

  .label {
    text-align: right;
    margin-right: 10px;
  }
}

.total-remark {
  font-size: 16px;
  margin: 10px 0;

  .value {
    border-top: 1px solid #666;
  }
}

.invoice-totals {
  margin-top: 1rem;
}
</style>
