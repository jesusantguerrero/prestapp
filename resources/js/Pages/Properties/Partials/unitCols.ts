import IIcSharpArea from '~icons/ic/sharp-photo-size-select-small';
import  IIcTwotoneBed  from '~icons/ic/twotone-bed';
import  IIcTwotoneBathtub  from '~icons/ic/twotone-bathtub';
import { formatMoney } from '../../../utils/formatMoney';
import { h } from "vue";
// @ts-ignore
import { IProperty } from '@/Modules/properties/propertyEntity';
// @ts-ignore
import UnitTitle from "@/Components/realState/UnitTitle.vue";


export default (t: Function = (text: string) => text) => ([
    {
        name: 'property',
        label: t('Property/Unit'),
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
              h('div', { class: 'flex mt-2 space-x-2'} , [
                h('span', {class: 'flex items-center space-x-1'}, [h(IIcSharpArea), h('span', row.area ?? 0)]),
                h('span', {class: 'flex items-center space-x-1'}, [h(IIcTwotoneBed), h('span', row.bedrooms ?? 0)]),
                h('span', {class: 'flex items-center space-x-1'}, [h(IIcTwotoneBathtub), h('span', row.bathrooms ?? 0)]),
              ])
          ]);
        }
    },

    {
      name: 'price',
      label: t('Rent price'),
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
])
