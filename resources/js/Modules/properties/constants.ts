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

export const PROPERTY_STATUS = {
    AVAILABLE: {
      label : 'Disponible'
    },
    PARTIALLY_PAID: {
      label: 'Parcialmente pagado'
    },
    PAID: {
      label: 'Pagado'
    },
    PENDING: {
      label: 'Pendiente'
    }
}

export const STATUS = Object.keys(PROPERTY_STATUS).reduce((keys, key: string) => {
  keys[key] = PROPERTY_STATUS[key]?.label;
  return keys;
}, { })

export const getPropertyStatus = (status: string): string => {
    return STATUS[status] || status;
}

export  type stateTypes =  'info'| 'danger'|'warning'|'primary'
export const getPropertyStatusColor = (status: string): stateTypes => {
    return PROPERTY_STATUS[status] ? PROPERTY_STATUS[status].color as stateTypes : 'info' as stateTypes;
}
