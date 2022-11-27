import { LoanTable } from './../loanInstallmentEntity';
import { it, expect, describe, beforeAll} from "vitest";
import { LOAN_FREQUENCY } from '../constants';
import { FrequencyType } from '../nextDate';

describe("Instalment calculation", () => {
    const date = '2022-01-15';
    let loanTable;

    beforeAll(() => {
        loanTable = new LoanTable({
            startDate: date, 
            frequency: LOAN_FREQUENCY.MONTHLY as FrequencyType,
            capital: 20000, 
            interestMonthlyRate: 20, 
            count: 12
        });
    })

    it('calculates the right payment', () => {
        expect(loanTable.getMonthlyPayment()).toBe("4505.30")
    })

    it('calculates the right installments', () => {
        const firstPayment = loanTable.getInstallment(1)
        expect(firstPayment.initial_balance).toBe(20000)
        expect(firstPayment.interest).toBe(4000)
        expect(firstPayment.principal).toBe(505.3)

        const lastPayment = loanTable.getInstallment(12)
        expect(lastPayment.initial_balance).toBe(3754.38)
        expect(lastPayment.interest).toBe(750.88)
        expect(lastPayment.principal).toBe(3754.42)
        expect(lastPayment.final_balance).toBe(0)
    })

    it('calculates monthly the right dates', () => {
        const firstPayment = loanTable.getInstallment(1)
        expect(firstPayment.due_date).toBe('2022-01-15')

        const payment2 = loanTable.getInstallment(2)
        expect(payment2.due_date).toBe('2022-02-15')
        
        const payment12 = loanTable.getInstallment(12)
        expect(payment12.due_date).toBe('2022-12-15')
    })
})