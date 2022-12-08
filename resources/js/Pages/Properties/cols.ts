import { IClient } from './../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";

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
            name: 'number',
            label: 'No. Contrato',
            class: "text-right",
            headerClass: "text-right",
    },
    {
            name: 'address',
            label: 'Dirección',
            class: "text-right",
            headerClass: "text-right",
    },
    {
        name: 'commission',
        label: 'Comisión',
        class: "text-right",
        headerClass: "text-right",
        render(row) {
            return row.commission + ' %'
        }
    }, {
        name: 'total',
        class: "text-center",
        type: 'money',
        headerClass: "text-center",
        label: 'Total a pagar'
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            console.log(getLoanStatusColor(row.status) );
            return h(ElTag, { type: getLoanStatusColor(row.status) }, getLoanStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]
