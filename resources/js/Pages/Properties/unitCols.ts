import { formatMoney } from './../../utils/formatMoney';
import { h } from "vue";
import {  ElTag } from "element-plus"
// @ts-ignore
import { IProperty } from '@/Modules/properties/propertyEntity';
// @ts-ignore
import IconMarker from "@/Components/icons/IconMarker.vue";
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";


export default [
    {
        name: 'property',
        label: 'Propiedad',
        class: "text-left",
        headerClass: "text-left",
        render(row: IProperty) {
          return h('div', { class: 'justify-center' }, [
              h('div', { class: 'flex items-start space-x-2 text-primary font-bold'}, [
                h(IconMarker, { class: 'text-primary font-bold mt-1'}),
                h('span', row.property?.short_name)
              ]),
              h('span',{ class: 'text-body-1 text-sm'}, row.owner?.display_name)
          ]);
      }
    },
    {
        name: 'address',
        label: 'Dirección',
        class: "text-left",
        headerClass: "text-left",
        render(row: IProperty) {
          return  h('span',{ class: 'text-body-1 text-sm'}, row.property?.address)
      }
    },
    {
      name: 'name',
      label: 'Unidad',
      render(row: IProperty) {
          return row.name;
      }
    },
    {
      name: 'contract',
      label: 'Inquilino',
      class: "text-left",
      headerClass: "text-left",
      minWidth: 200,
      render(row: IProperty) {
          return h('div', { class: 'flex items-center space-x-2' }, [
              h('span', row.contract?.client?.display_name)
          ]);
      }
    },

    {
      name: 'price',
      label: 'Precio de Renta',
      render(row: IProperty) {
          return formatMoney(row.price);
      }
  },
  {
        name: 'status',
        label: 'Estado',
        render(row: IProperty) {
            // @ts-ignore: got the right types
            return h(ElTag, { type: getPropertyStatusColor(row.status) }, getPropertyStatus(row.status))
        }
    },
]
