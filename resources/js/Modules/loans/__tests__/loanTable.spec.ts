import { it, expect, describe, beforeAll} from "vitest";
import { LoanTable } from "../loanEntity";

describe("Instalment calculation", () => {
    const date = '2022-01-15';
    let loanTable;

    beforeAll(() => {
        loanTable = new LoanTable(date, 20000, 20, 12);
    })

    it('calculates the right payment', () => {
        expect(loanTable.getMonthlyPayment()).toBe("4505.30")
    })

    it('calculates the right installments', () => {
        // first
        const firstPayment = loanTable.getInstallment(1)
        expect(firstPayment.initialBalance).toBe(20000)
        expect(firstPayment.interest).toBe(4000)
        expect(firstPayment.principal).toBe(505.3)
        //middle

        //last
    })
})