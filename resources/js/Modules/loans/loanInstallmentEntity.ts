import { LOAN_FREQUENCY } from './constants';
import { FrequencyType, getNextDate } from './nextDate';
import exactMath from "exact-math";
import { MathHelper } from './mathHelper';

type LoanInstallmentStatus = 'PENDING' | 'LATE' | 'PAID' | 'PARTIALLY_PAID' | 'GRACE'

export interface ILoanInstallment {
    id?: number;
    installment_number: number;
    due_date: string;
    paid_at?: string;
    days: number;
    // amounts
    amount: number;
    amount_debt: number;
    amount_paid: number;
    principal: number;
    interest: number;
    fees: number;
    penalty: number
    // payment track
    principal_paid: number;
    interest_paid: number;
    fees_paid: number;
    penalty_paid: number
    // Balance summary
    initial_balance: number;
    final_balance: number;
    // status
    status?: LoanInstallmentStatus;
}

export interface LoanTableParams {
    startDate: string; 
    frequency: FrequencyType;
    capital: number;
    interestMonthlyRate: number; 
    count: number;
}

export class LoanTable {
    payments: ILoanInstallment[] = [];
    startDate: string;
    frequency: FrequencyType;
    capital: number;
    interestMonthlyRate: number;
    count: number;
    payment: number;
    nextDateCalculator: (dateString: string, frequency: FrequencyType) => string;

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
        return MathHelper.loanPayment({
            interestRate,
            capital: this.capital,
            installments: this.count
        })
        
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
            interest = MathHelper.mulWithRounding(balance, interestRate);
            monthlyPrincipal = MathHelper.subWithRounding(this.payment, interest);
            const finalBalance = MathHelper.subWithRounding(balance,  monthlyPrincipal)

            this.payments.push({
                installment_number: index + 1,
                due_date: dueDate,
                days: 0,
                amount: this.payment,
                amount_paid: 0,
                amount_debt: this.payment,
                interest,
                principal: monthlyPrincipal,
                fees: 0,
                penalty: 0,
                principal_paid: 0,
                interest_paid: 0,
                fees_paid: 0,
                penalty_paid: 0,
                initial_balance: balance,
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

