export interface IInvoice {
  id: number;
  client_id: number;
  invoiceable_id: number;
  concept: string;
  debt: number;
  transaction: Record<string, string>;
  payments: Record<string, string>[]
  total: number;
}

export interface IInvoiceWithRelations extends IInvoice {

}
