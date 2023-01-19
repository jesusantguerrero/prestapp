import { ILoanInstallment } from "@/Modules/loans/loanInstallmentEntity";
import { formatMoney , formatDate } from "@/utils";

export default [
  {
    name: 'due_date',
    width: 100,
    label: 'Fecha',
    render(row: ILoanInstallment) {
      return formatDate(row.due_date)
    }
  },
  {
    name: 'amount',
    label: 'Monto',
    width: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.amount)
    }
  },
  {
    name: 'capital',
    label: 'Capital',
    width: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.principal)
    }
  },
  {
    name: 'interest',
    label: 'Interés',
    width: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.interest)
    }
  },
  {
    name: 'balance',
    label: 'Balance pendiente',
    width: 200,
    render(row: ILoanInstallment) {
      return formatMoney(row.final_balance)
    }
  },
  {
    name: 'status',
    label: 'Estatus',
    render(row: ILoanInstallment) {
      return row.status
    }
  },
  {
    name: 'actions',
    label: 'Acciones',
  }
]
