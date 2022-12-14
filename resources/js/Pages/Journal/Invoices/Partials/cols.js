import { formatMoney } from "@/utils";

export default [
  {
    label: "Items",
    name: "item",
    type: "custom",
    fixed: "true",
    class: "text-primary text-left",
    headerClass: 'text-left'
  },
  {
    label: "Quantity",
    name: "quantity",
    width: "100",
    type: "custom",
    class: "text-primary text-right",
    headerClass: 'text-right'
  },
  {
    label: "Discount %",
    name: "discount",
    width: "100",
    type: "custom",
    class: "text-primary text-right",
    headerClass: 'text-right'
  },
  {
    label: "Price",
    name: "price",
    width: "150",
    type: "custom",
    class: "text-primary text-right",
    headerClass: 'text-right'
  },
  {
    label: "Taxes",
    name: "taxes",
    width: "150",
    type: "custom",
    class: "text-primary text-center",
    headerClass: 'text-center',
    render(row) {
      return row.taxes.map(row => row.label + ' ' + row.rate + '%').join(', ')
    }
  },
  {
    label: "Amount",
    name: "amount",
    width: "120",
    type: "calc",
    class: "text-primary text-right",
    headerClass: 'text-right',
    formula(row) {
      const discount = Number(row.discount, 0);
      row.subtotal = row.quantity * row.price;
      row.discountTotal = (row.subtotal * discount) / 100;
      const amount = row.subtotal - row.discountTotal;
      row.amount = amount;
      return formatMoney(amount);
    }
  }, {
    name: 'actions'
  }
];
