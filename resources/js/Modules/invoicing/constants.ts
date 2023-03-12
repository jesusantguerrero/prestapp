export const INVOICE_STATUS = {
  paid: 'Pagado',
  unpaid: 'Pendiente',
  overdue: 'Tardio'
}

export const invoicesStatus = {
  'unpaid': {
      color: 'text-body-1',
      iconClass: 'fa fa-circle'
  },
  'paid': {
      color: 'text-success',
      iconClass: 'fa fa-check'
  },
  'overdue': {
      color: 'text-warning',
      iconClass: 'fa fa-exclamation'
  },
  'PENDING': {
      color: 'text-info',
      iconClass: ''
  }
}

export const getStatus = (status: string): string => {
  return INVOICE_STATUS[status] || status;
}

export const getStatusIcon = (status: string): string => {
  return invoicesStatus[status] && invoicesStatus[status].iconClass;
}

export const getStatusColor = (status: string): stateTypes => {
  return invoicesStatus[status] && invoicesStatus[status].color;
}
