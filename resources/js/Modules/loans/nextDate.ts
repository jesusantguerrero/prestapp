import { LOAN_FREQUENCY } from './constants';
import { addDays, addMonths, endOfMonth, format, parseISO } from "date-fns";

export type FrequencyType = 'MONTHLY' | 'WEEKLY' | 'BIWEEKLY';
export const getNextDate = (dateString: string| Date, frequency: FrequencyType, formatted = true ) => {
    const methods = {
        [LOAN_FREQUENCY.WEEKLY]: {
            method: addDays,
            interval: 7
        },
        [LOAN_FREQUENCY.BIWEEKLY]: {
            method: addDays,
            interval: 15
        },
        [LOAN_FREQUENCY.SEMIMONTHLY]: {
            method: addSemiMonth,
            interval: 1
        },
        [LOAN_FREQUENCY.MONTHLY]: {
            method: addMonths,
            interval: 1
        }
    }

    const config = methods[frequency]
    const date = typeof dateString == 'string' ? parseISO(dateString) : dateString; 
    const newDate = config.method(date, config.interval)
    return formatted ?  format(newDate, 'yyyy-MM-dd'): newDate;
}


const addSemiMonth = (date: Date, interval: number) => {
    if (date.getDate() == 15) {
        return endOfMonth(date);
    } else {
        return addDays(date, 15)
    }
}

