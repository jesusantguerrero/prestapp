import { IClient } from './../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";
import { IProperty } from '../../Modules/properties/propertyEntity';


export default [
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
            name: 'address',
            label: 'Dirección',
            class: "text-left",
            headerClass: "text-left",
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            // @ts-ignore: got the right types
            return h(ElTag, { type: getLoanStatusColor(row.status) }, getLoanStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]
