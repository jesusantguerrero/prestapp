import { LOAN_FREQUENCY } from './constants';
import { getNextDate } from './nextDate';
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

export interface LoanTableParams {
    startDate: string; 
    frequency: string;
    capital: number;
    interestMonthlyRate: number; 
    count: number;
}

export class LoanTable {
    payments: ILoanInstallment[] = [];
    startDate: string;
    frequency: string;
    capital: number;
    interestMonthlyRate: number;
    count: number;
    payment: number;
    nextDateCalculator: (dateString: string, frequency: string) => string;

    constructor({startDate, capital, interestMonthlyRate, count, frequency }: LoanTableParams, nextDateCalculator = getNextDate) {
        this.startDate = startDate;
        this.capital = capital;
        this.interestMonthlyRate = interestMonthlyRate / 100.00;
        this.count = count;
        this.frequency = frequency;
        this.nextDateCalculator = nextDateCalculator;
        this.payment = this.calculatePayment();
        this.generateAmortizationTable()
    }

    calculatePayment() {
        const interestRate = this.getFrequencyRate();
        return this.capital * (interestRate/(1-Math.pow(1+interestRate, -this.count)))
    }

    getMonthlyPayment() {
        return this.payment.toFixed(2);
    }

    private generateAmortizationTable() {
        let interest = 0;
        let monthlyPrincipal = 0;
        let balance  = this.capital;
        let dueDate = this.startDate;
        const interestRate = this.getFrequencyRate()
        for (let index = 0; index < this.count; index++) {
            interest = balance * interestRate;
            monthlyPrincipal = this.payment - interest;
            const finalBalance = balance - monthlyPrincipal 

            this.payments.push({
                due_date: dueDate,
                installment_number: index + 1,
                initial_balance: balance,
                amount: this.payment,
                interest,
                principal: parseFloat(monthlyPrincipal.toFixed(2)),
                final_balance: finalBalance
            });

            balance = finalBalance;
            dueDate = this.nextDateCalculator(dueDate, this.frequency);
        }
    }

    getInstallment(installmentNumber: number) {
        return this.payments[installmentNumber -1];
    }

    getLastPayment() {
        return this.payments[this.payments.length -1];
    }

    getFrequencyRate() {
        const intervals = {
            [LOAN_FREQUENCY.WEEKLY]: 4,
            [LOAN_FREQUENCY.BIWEEKLY]: 2,
            [LOAN_FREQUENCY.SEMIMONTHLY]: 2,
            [LOAN_FREQUENCY.MONTHLY]: 1,
        }
        return this.interestMonthlyRate / intervals[this.frequency];
    }
}

