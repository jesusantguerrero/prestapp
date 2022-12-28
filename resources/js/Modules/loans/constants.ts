export const paymentMethods = [
  {
    id: "cash",
    name: "Efectivo",
  },
  {
    id: "check",
    name: "Cheque",
  },
  {
    id: "bank",
    name: "Banco",
  },
];

export const LOAN_FREQUENCY = {
    WEEKLY: 'WEEKLY',
    BIWEEKLY: 'BIWEEKLY',
    MONTHLY: 'MONTHLY',
    SEMIMONTHLY: 'SEMIMONTHLY'
}

export const loanFrequencies = [{
    name: LOAN_FREQUENCY.WEEKLY,
    label: 'Semanal'
}, {
    name: LOAN_FREQUENCY.BIWEEKLY,
    label: 'Bi Semanal'
},
{
    name: LOAN_FREQUENCY.SEMIMONTHLY,
    label: 'Quincenal'
},
{
    name: LOAN_FREQUENCY.MONTHLY,
    label: 'Mensual'
}];

export const LOAN_STATUS = {
    LATE: 'En mora',
    PARTIALLY_PAID: 'Parcialmente pagado',
    PAID: 'Pagado',
    PENDING: 'Pendiente'
}

export const loanStatus = {
    'LATE': {
        color: 'danger'
    },
    'PARTIALLY_PAID': {
        color: 'success'
    },
    'PAID': {
        color: 'success'
    },
    'PENDING': {
        color: 'info'
    }
}

export const getLoanStatus = (status: string): string => {
    return LOAN_STATUS[status] || status;
}

export  type stateTypes =  'info'| 'danger'|'warning'|'primary'
export const getLoanStatusColor = (status: string): stateTypes => {
    console.log(status, loanStatus[status])
    return loanStatus[status] ? loanStatus[status].color as stateTypes : 'info' as stateTypes;
}
