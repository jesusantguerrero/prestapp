import { ILoan, ILoanWithInstallments } from "./../../Modules/loans/loanEntity";
import { h } from "vue";
import { ElTag } from "element-plus";
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";
import IconMarkerVue from "@/Components/icons/IconMarker.vue";
import { formatDate, formatMoney } from "@/utils";

export default [

  {
    name: "client",
    label: "Cliente",
    class: "text-left",
    headerClass: "text-left",
    render(row: ILoanWithInstallments) {
      return h('div', { class: 'px-4' }, [
        h('div', { class: 'flex items-start space-x-2 text-primary font-bold'}, [
          h(IconMarkerVue, { class: 'text-primary font-bold mt-1'}),
          h('span', row.client?.fullName)
        ]),
        h('span',{ class: 'text-body-1 text-sm'}, `${formatMoney(row.total)} (${formatMoney(row.amount)})`)
    ]);
    },
  },
  {
    name: "first_repayment_date",
    class: "text-center",
    headerClass: "text-center",
    label: "Fecha de inicio",
    render(row: ILoan) {
      return formatDate(row.first_repayment_date)
    }
  },

  {
    name: "interest_rate",
    label: "Terminos",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return h('div', { class: 'space-x-2' }, [
        h('span', row.frequency),
        h('span', row.interest_rate + " %")
      ]);
    },
  },
  {
    name: "amount_due",
    label: "Por pagar",
    class: "text-right",
    headerClass: "text-right",
    render(row: ILoanWithInstallments) {
      return h('div', {class: 'space-x-4  flex items-center justify-end'}, [
        // @ts-ignore
        h(ElTag,
          { type: getLoanStatusColor(row.payment_status) },
          getLoanStatus(row.payment_status)
        ),
        h('div',
          formatMoney(row.amount_due),
        )
      ]);
    },
  },
  {
    name: "actions",
    label: "Acciones",
    class: "text-right bg-red-500",
    headerClass: "text-center",
  },
];
