import { LOAN_FREQUENCY } from './constants';
import { addDays, addMonths, endOfMonth, format, parseISO, startOfMonth } from "date-fns";

export const getNextDate = (dateString: string, frequency: 'MONTHLY' | 'WEEKLY' | 'BIWEEKLY' ) => {
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
    return format(config.method(parseISO(dateString), config.interval), 'yyyy-MM-dd');
}


const addSemiMonth = (date: Date, interval: number) => {
    if (date.getDate() == 15) {
        return endOfMonth(date);
    } else {
        return addDays(date, 15)
    }
}

