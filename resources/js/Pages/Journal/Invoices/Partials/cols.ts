import { formatDate, formatMoney } from "@/utils";
import { es } from "date-fns/locale";

export default [
  {
    label: "Descripción",
    name: "item",
    type: "custom",
    fixed: "true",
    class: "text-left",
    headerClass: 'text-left',
    render(row: any) {
      const date = row.concept?.slice(-10);
      const dateString = formatDate(date, 'MMMM') ?? ''
      return `${row.concept} ${dateString}`
    }
  },
  {
    label: "Cant.",
    name: "quantity",
    width: "100",
    type: "custom",
    class: "text-right text-normal",
    headerClass: 'text-right'
  },
  {
    label: "Discount %",
    name: "discount",
    width: "100",
    type: "custom",
    class: "text-right",
    headerClass: 'text-right'
  },
  {
    label: "Monto",
    name: "price",
    width: "150",
    type: "custom",
    class: "text-right",
    headerClass: 'text-right'
  },
  {
    label: "Descuento de abogado",
    name: "taxes",
    width: "150",
    type: "custom",
    class: "text-center",
    headerClass: 'text-center',
    render(row) {
      return row.taxes?.map(row => `${formatMoney(row.amount)} ${!row.isFixed &&  '(' + row.rate + '%)'}`).join(', ')
    }
  },
  {
    label: "Total",
    name: "amount",
    width: 80,
    type: "calc",
    class: "text-right",
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
