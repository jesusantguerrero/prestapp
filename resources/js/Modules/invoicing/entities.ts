export interface IInvoice {
  id: number;
  client_id: number;
  concept: string;
  debt: number;
  transaction: Record<string, string>;
  payments: Record<string, string>[]
}

export interface IInvoiceWithRelations extends IInvoice {

}
