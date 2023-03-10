import { IClientSaved } from './../clients/clientEntity';
import { IClient } from "../clients/clientEntity";

export interface IProperty {
    owner: IClient;
    owner_id: number;
    // basic info
    address: string;
    // payment details
    price: number
    units: IUnit[]
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
  date: Date;
  next_invoice_date: Date;
  commission_paid?: number;
  paid?: number;
  total: number
}
