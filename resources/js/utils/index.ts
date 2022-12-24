import { format, parseISO } from "date-fns"
export * from "./formatMoney";

export const formatDate = (stringDate: string) => {
  const emptyDate = '-- --- ----'
  
  try {
    const date = stringDate ? parseISO(stringDate) : new Date() 
    return typeof stringDate == 'string' ? format(date, 'd MMM, yyyy') : format(stringDate, 'd MMM, yyyy')
  } catch (err) {
    return stringDate ?? emptyDate
  }
}