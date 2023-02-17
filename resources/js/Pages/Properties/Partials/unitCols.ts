import { formatMoney } from '../../../utils/formatMoney';
import { h } from "vue";
import {  ElTag } from "element-plus"
// @ts-ignore
import { IProperty } from '@/Modules/properties/propertyEntity';
// @ts-ignore
import IconMarker from "@/Components/icons/IconMarker.vue";
import UnitTitle from "@/Components/realState/UnitTitle.vue";
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";


export default [
    {
        name: 'property',
        label: 'Propiedad',
        class: "text-left",
        headerClass: "text-left",
        width: 550,
        render(row: IProperty) {
          return h('div', { class: 'justify-center' }, [
              h(UnitTitle, {
                title:  row.property?.short_name + ' / ' + row.name,
                ownerName: row.owner?.display_name,
                tenantName: row.contract?.client?.display_name,
              }),
          ]);
        }
    },

    {
      name: 'price',
      label: 'Precio de Renta',
      align: 'right',
      class: 'text-right',
      render(row: IProperty) {
          return h('span', {class: 'text-success font-bold'}, formatMoney(row.price));
      }
  },
    {
      name: 'actions',
      label: ' ',
    },
]
