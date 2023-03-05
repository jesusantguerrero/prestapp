import { getRentStatusColor, getRentStatus } from './../../Modules/properties/constants';
import { formatDate } from './../../utils/index';
import { formatMoney } from './../../utils/formatMoney';
import { IClient } from './../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import IconMarker from '@/Components/icons/IconMarker.vue';
import { differenceInCalendarDays, parseISO } from 'date-fns';
import { Link } from '@inertiajs/vue3';

interface IRent {
    client: IClient,
}

export default [
    {
        name: 'client',
        label: 'Inquilino',
        class: "text-center",
        headerClass: "text-center",
        minWidth: 150,
        render(row: IRent) {
            const clientName = row.client?.names + ' ' + row.client?.lastnames
            const initials = row.client?.names[0] + row.client?.lastnames[0];

            return h('div', { class: 'px-4' }, [
              h('div', { class: 'flex items-center space-x-2' }, [
                h(ElAvatar, { shape: 'circle' }, initials),
                h('span', clientName)
              ]),
              h('div', { class: 'flex items-center text-primary font-bold'}, [
                h(IconMarker, { class: 'text-primary font-bold'}),
                h('span', row.property?.short_name)
              ]),
          ]);
        }
    },
    {
            name: 'id',
            label: 'Detalles de Contrato',
            class: "text-left",
            headerClass: "text-center",
            align: 'left',
            minWidth: 200,
            render(row) {
              return h('div', [
                h('p', {class: 'font-bold' }, formatMoney(row.amount)),
                h('p', `Duración: ${formatDate(row.date)} - ${formatDate(row.end_date)} | Mora: ${row.commission}  %`),
              ])
            }
    },{
        name: 'owner_name',
        class: "text-center",
        type: 'money',
        headerClass: "text-center",
        label: 'Propietario'
    }, {
        name: 'days',
        label: 'Dias restantes',
        render(row) {
            let daysLeft = null;
            if (row.end_date) {
              daysLeft = differenceInCalendarDays(parseISO(row.end_date), new Date())
            }
            return h(Link, {href: `/contacts/${row.client?.id}/tenants/rents/${row.id}/renew`} , h(ElTag, { type: getRentStatusColor(row.status) }, daysLeft))
        }
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            return h(ElTag, { type: getRentStatusColor(row.status) }, getRentStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones',
        minWidth: 150,
    }
]
