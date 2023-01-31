import { IClient } from './../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";
import IconMarker from '@/Components/icons/IconMarker.vue';

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

            return h('div', { class: 'px-4' }, [
              h('div', { class: 'flex items-center space-x-2' }, [
                h(ElAvatar, { shape: 'circle' }, initials),
                h('span', clientName)
              ]),
              h('div', { class: 'flex items-center text-primary font-bold'}, [
                h(IconMarker, { class: 'text-primary font-bold'}),
                h('span', row.property.short_name)
              ]),
          ]);
        }
    },
    {
            name: 'id',
            label: 'Terminos Contrato',
            class: "text-center",
            headerClass: "text-center",
            render(row) {
              return h('div', [
                h('p', `C_${row.id.toString().padStart(6, '0')}`),
                h('p', row.commission + ' %'),
              ])
            }
    },{
        name: 'total',
        class: "text-center",
        type: 'money',
        headerClass: "text-center",
        label: 'Total a pagar'
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            return h(ElTag, { type: getLoanStatusColor(row.status) }, getLoanStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]
