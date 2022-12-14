import { IProperty } from '@/Modules/properties/propertyEntity';
export interface IClient {
    id?: number;
    names: string;
    lastnames: string;
    fullName?: string;
    dni: string;
}

export interface IClientSaved extends IClient {
  id: number;
  is_owner: boolean;
  invoices: any[];
  leases: any[];
  properties: IProperty[];
  status: string;
  account: any;
}
