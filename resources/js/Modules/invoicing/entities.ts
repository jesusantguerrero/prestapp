import { IClient } from "../clients/clientEntity";

export interface InvoiceItem {
  price: number;
  quantity: number;
  concept: string;
}
export interface IInvoice {
  id: number;
  client_id: number;
  invoiceable_id: number;
  concept: string;
  description: string;
  due_date: Date;
  debt: number;
  transaction: Record<string, string>;
  payments: Record<string, string>[]
  total: number;
  status: string;
}

export interface IInvoiceWithRelations extends IInvoice {
  client: IClient
}
