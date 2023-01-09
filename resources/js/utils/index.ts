import { format, parseISO } from "date-fns"
export * from "./formatMoney";

export const formatDate = (stringDate: string|Date, formatText = 'd MMM, yyyy') => {
  const emptyDate = '-- --- ----'

  try {
    return typeof stringDate == 'string' ? format(parseISO(stringDate), formatText) : format(stringDate, formatText);
  } catch (err) {
    return stringDate ?? emptyDate
  }
}
