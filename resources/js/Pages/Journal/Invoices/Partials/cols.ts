import { formatDate, formatMoney } from "@/utils";
interface ITableItem {
  name: string;
  label: string;
  class: string;
  headerClass: string;
  render?: Function
}

export default [
  {
    label: "Descripción",
    name: "concept",
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
    class: "text-right text-normal",
    headerClass: 'text-right'
  },
  {
    label: "Discount %",
    name: "discount",
    width: "100",
    class: "text-right",
    headerClass: 'text-right'
  },
  {
    label: "Monto",
    name: "price",
    width: 120,
    class: "text-right",
    headerClass: 'text-right'
  },
  {
    label: "Descuento de abogado",
    name: "taxes",
    width: 190,
    class: "text-center",
    headerClass: 'text-center',
    render(row: any) {
      return row.taxes?.map(row => `${formatMoney(row.amount)} ${!row.isFixed &&  '(' + row.rate + '%)'}`).join(', ')
    }
  },
  {
    label: "Total",
    name: "amount",
    width: 100,
    type: "calc",
    class: "text-right",
    align: 'right',
    headerClass: 'text-right',
    render(row: any) {
      const discount = Number(row.discount ?? 0);
      row.subtotal = row.quantity * row.price;
      row.discountTotal = (row.subtotal * discount) / 100;
      const amount = row.subtotal - row.discountTotal;
      row.amount = amount;
      return formatMoney(amount, "DOP", { hideSymbol: true });
    }
  }, {
    name: 'actions'
  }
];
