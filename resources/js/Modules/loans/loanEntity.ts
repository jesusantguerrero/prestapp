import { IClient } from "../clients/clientEntity";


export interface ILoan {
    contact?: IClient;
    contact_id: number;
    interest_rate: number;
    amount: number;
    count: number;
    start_date: string,
    grace_days: number;
}

type LoanInstallmentStatus = 'PENDING' | 'LATE' | 'PAID' | 'PARTIALLY_PAID' | 'GRACE'

export interface ILoanInstallment {
    due_date: string;
    installment_number: number;
    initial_balance: number;
    amount: number;
    interest: number;
    principal: number;
    final_balance: number;
    status?: LoanInstallmentStatus;
}

export class LoanTable {
    payments: ILoanInstallment[] = [];
    startDate: string;
    capital: number;
    interestMonthlyRate: number;
    count: number;
    payment: number;

    constructor(startDate: string, capital: number, interestMonthlyRate: number, count: number) {
        this.startDate = startDate;
        this.capital = capital;
        this.interestMonthlyRate = interestMonthlyRate / 100.00;
        this.count = count;
        this.payment = this.calculatePayment();
        this.generateAmortizationTable()
    }

    calculatePayment() {
        return this.capital * (this.interestMonthlyRate/(1-Math.pow(1+this.interestMonthlyRate, -this.count)))
    }

    getMonthlyPayment() {
        return this.payment.toFixed(2);
    }

    private generateAmortizationTable() {
        let interest = 0;
        let monthlyPrincipal = 0;
        let balance  = this.capital;
        for (let index = 0; index < this.count; index++) {
            interest = balance * this.interestMonthlyRate;
            monthlyPrincipal = this.payment - interest;
            const finalBalance = balance - monthlyPrincipal 

            this.payments.push({
                due_date: '',
                installment_number: index + 1,
                initial_balance: balance,
                amount: this.payment,
                interest,
                principal: parseFloat(monthlyPrincipal.toFixed(2)),
                final_balance: finalBalance
            });

            balance = finalBalance;
        }
    }

    getInstallment(installmentNumber: number) {
        return this.payments[installmentNumber -1];
    }

    getLastPayment() {
        return this.payments[this.payments.length -1];
    }
}

