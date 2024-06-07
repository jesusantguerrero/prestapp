import { IClient } from '../../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";

interface IRent {
    client: IClient,
}

export default [
    {
        name: 'client',
        label: 'Inquilino',
        class: "text-center",
        headerClass: "text-center",
        minWidth: 200,
        render(row: IRent) {
            const clientName = row.client.names + ' ' + row.client.lastnames
            const initials = row.client.names[0] + row.client.lastnames[0];

            return h('div', { class: 'flex items-center space-x-2' }, [
                h(ElAvatar, { shape: 'circle' }, initials),
                h('span', clientName)
            ]);
        }
    },
 {
        name: 'total',
        class: "text-center",
        type: 'money',
        headerClass: "text-center",
        label: 'Total a pagar'
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            return h(ElTag, { type: getPropertyStatusColor(row.status) }, getPropertyStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: ' '
    }
]
