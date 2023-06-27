import { addDays, format, parseISO, subDays } from "date-fns"
import { es } from "date-fns/locale"
export * from "./formatMoney";

export const formatDate = (stringDate: string|Date, formatText = 'd MMM, yyyy') => {
  const emptyDate = '-- --- ----'
  const dateOptions = {
    locale: es
  }

  try {
    const date = typeof stringDate == 'string' ? parseISO(stringDate) : stringDate;
    return format(date, formatText, dateOptions);
  } catch (err) {
    return stringDate ?? emptyDate
  }
}
export const parseDate = (stringDate: string|Date): Date => {
  try {
    const date = typeof stringDate == 'string' ? parseISO(stringDate) : stringDate;
    return date;
  } catch (err) {
    return stringDate
  }
}

/**
 * Creates a list of a range of years between 'from' and 'to'
 * @param {number|string} from Number used as the start of the year's range
 * @param {number|string} to Number used as the end of the year's range. Default: new Date().getFullYear()
 * @returns {Array.<number>} List of years based on range (ascending order)
 */
export function composeRangeYears(from: number|string, to: number|string = new Date().getFullYear()) {
  const fromYear = Number(from);
  let toCounter = Number(to);

  if (Number.isNaN(fromYear)) {
    throw new TypeError(
      `Parameter 'from' should be a number or string with number`
    );
  } else if (Number.isNaN(toCounter)) {
    throw new TypeError(
      `Parameter 'to' should be a number or string with number`
    );
  }

  const years = [];
  while (toCounter >= fromYear) {
    years.push(toCounter);
    toCounter--;
  }
  return years;
}

export const dateToIso = (date: Date | null) => {
  return date ? formatDate(date, "yyyy-MM-dd") : null;
};



const setRange = (dateCount: number|Date, direction: string): Date => {
  const date = new Date();
  const method = direction == 'back' ? subDays : addDays;

  return typeof dateCount == 'number' ? dateToIso(method(date, dateCount)) : dateToIso(dateCount)

}

type RangeValue = number|Date;
export const getRangeParams = (field: string, range: RangeValue[]|null, direction = 'back') => {
    let rangeString: string = '';

    if (!range) return '';

    if (range.every(value => value !== null)) {
      rangeString = range.map((dateCount) => setRange(dateCount, direction)).join("~");
    } else if (range.at(0) == null && range.at(1) !== null) {
      rangeString = '<' + setRange(range?.at?.(1) ?? 0, direction)
    } else if (range.at(1) !== null) {
      rangeString = '>' + setRange(range.at(0) ?? 0, direction)
    }
    return `filter[${field}]=${rangeString}`;
}
