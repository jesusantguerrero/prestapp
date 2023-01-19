import { IClient, IClientSaved } from './../clients/clientEntity';
export const PROPERTY_TYPE = {
    APARTMENT: 'apartment',
    COMMERCIAL: 'commercial',
    DUPLEX: 'duplex',
    HOUSE: 'house',
    MID_USE: 'mid-use',
    OTHER: 'other'
}

export const propertyTypes = [{
    name: PROPERTY_TYPE.APARTMENT,
    label: 'Apartamento'
}, {
    name: PROPERTY_TYPE.COMMERCIAL,
    label: 'Comercial'
},
{
    name: PROPERTY_TYPE.DUPLEX,
    label: 'Duplex'
},
{
    name: PROPERTY_TYPE.HOUSE,
    label: 'Casa'
},
{
    name: PROPERTY_TYPE.MID_USE,
    label: 'Compartida'
},
{
  name: PROPERTY_TYPE.OTHER,
  label: 'otro'
}
];

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


interface IBarePayments {
  id: number;
  rent_id: number;
  client_id: number;
  balance: number;
  payment: number
  amount: number;
}

interface IRelatedPaymentsAddProps {payment: any, balance: number, rentId: number, client: IClientSaved}
interface IValidPayment {
  id: number;
  amount: number;
  original_amount: number;
}
export class RelatedPaymentGenerator {
  payments?: IBarePayments[];

  construct(transactions?: any[]) {
    if (transactions) {
      this.createFromPayments(transactions)
    }
  }

  createFromPayments(transactions: any[]) {
    const self = this;
    this.payments = transactions.reduce((payments, tran) => {
      if (tran?.transactionable) {
        payments.push(
          ...tran.transactionable.payments.map(self.parsePayment)
        );
      }
      return payments;
    },[])
  }

  add(barePayment: any) {
    this.payments?.push(this.parsePayment(barePayment));
  }

  parsePayment({ payment, balance, rentId, client}: IRelatedPaymentsAddProps) {
    return {
      rent_id: rentId,
      client_id: client.id,
      client_name: client.display_name,
      balance: balance,
      payment: 0,
    };
  }

  sum(validPayments: IValidPayment[], prop: string = 'amount') {
    validPayments.reduce((total, payment) => parseFloat(total) + parseFloat(payment[prop]), 0)
  }

  getValidPayments() {
    return this.payments?.reduce(
      (selectedPayments: IValidPayment[], doc) => {
        if (doc.payment) {
          selectedPayments.push({
            id: doc.id,
            amount: doc.payment,
            original_amount: doc.amount,
          });
        }
        return selectedPayments;
      },
      []
    )
  }
}
