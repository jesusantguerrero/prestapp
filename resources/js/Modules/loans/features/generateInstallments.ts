import { ILoan, LoanTable } from "../loanEntity";

export const generateInstallments = ({ interest_rate, amount, count, start_date} : ILoan) => {
    const loanTable = new LoanTable(start_date, amount, interest_rate, count);

    return loanTable.payments;
}