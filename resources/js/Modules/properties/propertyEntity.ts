import { IClientSaved } from './../clients/clientEntity';
import { IClient } from "../clients/clientEntity";

export interface IProperty {
    owner: IClientSaved;
    owner_id: number;
    name: string;
    short_name: string;
    property_type: string;
    // basic info
    address: string;
    // payment details
    description: string;
    price: number;
    units: IUnit[];
    contract?: IRent;
}

export interface IUnit {
  property_id: number;
  name: string;
  status: string;
  price: string;
  owner: IClientSaved;
  client: IClientSaved;
  contract: Record<string, any>
  bathrooms: number;
  bedrooms: number;
  area: number;
}

export interface IRent {
  id: number;
  property_id: number;
  unit_id: number;
  client_id: number;
  client: IClient;
  status: string;
  deposit: string;
  address: string;
  owner_name: string;
  client_name: string;
  date:  Date | string;
  end_date: string |Date;
  next_invoice_date: Date;
  commission_paid?: number;
  paid?: number;
  total: number
  property?: IProperty;
  unit?: IUnit;
  owner?: IClient;
  amount: number
}
