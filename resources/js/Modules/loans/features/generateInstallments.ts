import { ILoan } from "../loanEntity";
import { LoanTable } from "../loanInstallmentEntity";

export const generateInstallments = ({ interest_rate, amount, repayment_count, first_repayment_date, frequency } : ILoan) => {
    const loanTable = new LoanTable({
        startDate: first_repayment_date,
        frequency: frequency,
        interestMonthlyRate: interest_rate,
        capital: amount,
        count: repayment_count,
    });

    return loanTable;
}
