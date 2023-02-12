import { formatMoney } from "@/utils";

export const cols = [
              {
                name: 'loan_id',
                label: 'Prestamo #',
              },
              {
                name: 'client.display_name',
                label: 'Cliente',
              },
              {
                name: 'amount',
                label: 'Monto del pagare',
                render(row: Record<string, any>) {
                  return formatMoney(row.amount);
                },
              },
              {
                name: 'amount_paid',
                label: 'Balance',
                render(row: Record<string, any>) {
                  return formatMoney(row.amount_due);
                },
              },
              {
                name: 'payment',
                label: 'Monto de reembolso',
              },
]
           