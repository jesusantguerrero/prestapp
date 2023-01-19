import { formatMoney } from './../../utils/formatMoney';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { IProperty } from '@/Modules/properties/propertyEntity';
// @ts-ignore
import IconMarker from "@/Components/icons/IconMarker.vue";
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";


export default [
    {
        name: 'address',
        label: 'Dirección',
        class: "text-left",
        headerClass: "text-left",
        render(row: IProperty) {
          const ownerName = row.owner.names + ' ' + row.owner.lastnames
          const initials = row.owner.names[0] + row.owner.lastnames[0];

          return h('div', { class: 'justify-center' }, [
              h('div', { class: 'flex items-center text-primary font-bold'}, [
                h(IconMarker, { class: 'text-primary font-bold'}),
                h('span', row.short_name)
              ]),
              h('span',{ class: 'text-body-1 text-sm'}, row.address)
          ]);
      }
    },
    {
      name: 'owner',
      label: 'Dueño',
      class: "text-left",
      headerClass: "text-left",
      minWidth: 200,
      render(row: IProperty) {
          const ownerName = row.owner.names + ' ' + row.owner.lastnames
          const initials = row.owner.names[0] + row.owner.lastnames[0];

          return h('div', { class: 'flex items-center space-x-2' }, [
              h(ElAvatar, { shape: 'circle' }, initials),
              h('span', ownerName)
          ]);
      }
    },
    {
        name: 'balance',
        label: 'Balance Pendiente',
        render(row: IProperty) {
            // @ts-ignore: got the right types
            return formatMoney(row.balance)
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
    {
        name: 'actions',
        label: 'Acciones'
    }
]
