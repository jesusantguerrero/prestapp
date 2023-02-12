import IconMarkerVue from "@/Components/icons/IconMarker.vue";
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
    name: 'loan_id',
    minWidth: 170,
    label: 'Prestamo',
    align: 'center',
    render(row: ILoanInstallment) {
      return h('div', { class: 'px-4' }, [
        h('div', { class: 'flex items-start space-x-2 text-primary font-bold'}, [
          h(IconMarkerVue, { class: 'text-primary font-bold mt-1'}),
          h('span', row.client?.fullName)
        ]),
        h('span',{ class: 'text-body-1 text-sm'}, `${formatMoney(row.total)} (${formatMoney(row.amount)})`)
      ]);
    }
  },
  {
    name: 'amount',
    label: 'Cuota',
    align: 'right',
    class: 'text-right',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.amount)
    }
  },
  {
    name: 'capital',
    label: 'Capital',
    align: 'right',
    class: 'text-right',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return formatMoney(row.principal)
    }
  },
  {
    name: 'interest',
    label: 'Interés',
    align: 'right',
    class: 'text-right',
    minWidth: 100,
    render(row: ILoanInstallment) {
      return h('div', {class: 'text-right'}, [
        h('span', { class: 'text-primary'}, formatMoney(row.interest)),
        row.late_fee > 0 ? h('span', [
          ' + ',
          h('span', { class: 'text-error'},  formatMoney(row.late_fee))
        ]): null
      ]);
    }
  },
  {
    name: 'balance',
    label: 'Balance pendiente',
    width: 200,
    align: 'right',
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
    align: 'center',
    label: 'Acciones',
  }
]
