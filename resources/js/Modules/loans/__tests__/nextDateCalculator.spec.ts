import { it, expect, describe} from "vitest";
import { LOAN_FREQUENCY } from '../constants';
import { getNextDate } from '../nextDate';

describe("Instalment calculation", () => {
    const date = '2022-01-15';

    it('calculates biweekly the right dates', () => {
        const payment1 = getNextDate(date, LOAN_FREQUENCY.BIWEEKLY)
        expect(payment1).toBe('2022-01-30')
        
        const payment2 = getNextDate(payment1, LOAN_FREQUENCY.BIWEEKLY)
        expect(payment2).toBe('2022-02-14')
        
        const payment3 = getNextDate(payment2, LOAN_FREQUENCY.BIWEEKLY)
        expect(payment3).toBe('2022-03-01')
    })

    it('calculates semimonthly the right dates', () => {
        const payment1 = getNextDate(date, LOAN_FREQUENCY.SEMIMONTHLY)
        expect(payment1).toBe('2022-01-31')
        
        const payment2 = getNextDate(payment1, LOAN_FREQUENCY.SEMIMONTHLY)
        expect(payment2).toBe('2022-02-15')
        
        const payment3 = getNextDate(payment2, LOAN_FREQUENCY.SEMIMONTHLY)
        expect(payment3).toBe('2022-02-28')
    })
})