import { LoanTable } from "../loanInstallmentEntity";
import { FrequencyType } from "../nextDate";

export const generateInstallments = ({ interest_rate, amount, repayment_count, first_repayment_date, frequency } : {
  interest_rate: number,
  amount: number,
  repayment_count: number,
  first_repayment_date: string,
  frequency: FrequencyType
}) => {
    const loanTable = new LoanTable({
        startDate: first_repayment_date,
        frequency: frequency,
        interestMonthlyRate: interest_rate,
        capital: amount,
        count: repayment_count,
    });

    return loanTable;
}
