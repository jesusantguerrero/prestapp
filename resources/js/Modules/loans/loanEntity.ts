import { IClient } from "../clients/clientEntity";
import { ILoanInstallment } from "./loanInstallmentEntity";

export interface ILoan {
    id?: number;
    client?: IClient;
    client_id: number;
    total?: number;
    payment?: number;
    // loan details
    interest_rate: number;
    amount: number;
    amount_paid?: number;
    repayment_count: number;
    // payment details
    frequency: string,
    date: string | Date;
    disbursement_date: string | Date;
    first_repayment_date: string | Date;
    grace_days: number;
    payment_status: string;
    // cancel optons
    cancelled_at: Date;
    cancel_reason: string;
}


export type ILoanWithInstallments = ILoan & {
  installments: ILoanInstallment[]
}

export interface IPayment {
  id: number;
  amount: number;
  payment_date: Date;
}

export interface IInvoice {

}
export interface ILoanWithPayments extends ILoan {
  payment_documents: IPayment[]
}

export interface ILoanWithAgreements extends ILoan {
  agreements: IInvoice[]
}
