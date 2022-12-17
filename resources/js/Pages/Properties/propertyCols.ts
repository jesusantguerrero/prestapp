import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
import { IProperty } from '../../Modules/properties/propertyEntity';
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";
import { property } from "lodash";


export default [
    {
        name: 'address',
        label: 'Dirección',
        class: "text-left",
        headerClass: "text-left",
    },
    {
        name: 'id',
        label: 'No.',
        class: "text-center",
        headerClass: "text-center",
    },
    {
      name: 'owner',
      label: 'Dueño',
      class: "text-center",
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
        name: 'status',
        label: 'Estado',
        render(row) {
            // @ts-ignore: got the right types
            return h(ElTag, { type: getPropertyStatusColor(row.status) }, getPropertyStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]
