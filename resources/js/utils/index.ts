import { format, parseISO } from "date-fns"
export * from "./formatMoney";

export const formatDate = (stringDate: string) => {
  const emptyDate = '-- --- ----'
  try {
    const date = stringDate ? parseISO(stringDate) : new Date() 
    return stringDate ? format(date, 'd MMM, yyyy') : emptyDate
  } catch (err) {
    return emptyDate
  }
}