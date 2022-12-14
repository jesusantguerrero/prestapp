import { IClient } from "../clients/clientEntity";
import { ILoanInstallment } from "./loanInstallmentEntity";

export interface ILoan {
    id?: number;
    client?: IClient;
    client_id: number;
    // loan details
    interest_rate: number;
    amount: number;
    repayment_count: number;
    // payment details
    frequency: string,
    disbursement_date: string | Date;
    first_installment_date: string | Date;
    grace_days: number;
    payment_status: string;
}


export type ILoanWithInstallments = ILoan & {
    installments: ILoanInstallment[]
}
