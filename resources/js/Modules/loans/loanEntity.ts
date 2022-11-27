import { IClient } from "../clients/clientEntity";

export interface ILoan {
    contact?: IClient;
    contact_id: number;
    // loan details
    interest_rate: number;
    amount: number;
    repayment_count: number;
    // payment details
    frequency: string,
    disbursement_date: string,
    first_installment_date: string,
    grace_days: number;
}

