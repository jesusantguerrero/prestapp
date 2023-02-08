import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { formatMoney , formatDate } from "@/utils";
import { h } from "vue";

export default [
  {
    name: 'due_date',
    minWidth: 100,
    label: 'Fecha',
    render(row: ILoanInstallment) {
      return formatDate(row.due_date)
    }
  },
  {
    name: 'amount',
    label: 'Monto',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.amount)
    }
  },
  {
    name: 'capital',
    label: 'Capital',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.principal)
    }
  },
  {
    name: 'interest',
    label: 'Interés',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return h('div', {class: 'text-right'}, [
        h('span', { class: 'text-primary'}, formatMoney(row.interest)),
         ' + ',
        h('span', { class: 'text-error'},  formatMoney(row.late_fee))
      ]);
    }
  },
  {
    name: 'balance',
    label: 'Balance pendiente',
    width: 200,
    headerClass: 'text-right',
    class: "text-right",
    render(row: ILoanInstallment) {
      return h('div', {class: 'flex space-x-4 w-full justify-end'}, [
        h('span', row.payment_status),
        h('span', { class: 'text-right font-bold'}, formatMoney(row.final_balance))
      ])
    }
  },
  {
    name: 'actions',
    label: 'Acciones',
  }
]
