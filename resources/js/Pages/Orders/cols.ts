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
        label: 'Vendor',
        class: "text-center",
        headerClass: "text-center",
        minWidth: 150,
        render(row: IRent) {
            const clientName = row.client?.names + ' ' + row.client?.lastnames
            const initials = row.client?.names[0] + row.client?.lastnames[0];

            return h('div', { class: 'px-4' }, [
              h('div', { class: 'flex items-center space-x-2' }, [
                h(ElAvatar, { shape: 'circle' }, initials),
                h('span', { class: 'text-xs'}, clientName)
              ]),
              h('div', { class: 'flex items-center text-primary font-bold mt-2'}, [
                h(IconMarker, { class: 'text-primary font-bold'}),
                h('span', { class: 'text-xs' }, row.property?.short_name)
              ]),
          ]);
        }
    },
    {
            name: 'id',
            label: 'Order details',
            class: "text-left",
            headerClass: "text-center",
            align: 'left',
            minWidth: 200,
            render(row: Record<any, any>) {

              const lateFee = row.commission > 100 ? parseFloat(row.commission ?? 0).toFixed(2) : `${parseFloat(row.commission ?? 0)?.toFixed?.(2)}%`
              return h('div', [
                h('p', {class: 'font-bold' }, formatMoney(row.amount)),
                h('p', `Duración: ${formatDate(row.date)} - ${formatDate(row.end_date)} | Mora: ${lateFee}`),
              ])
            }
    },{
        name: 'owner_name',
        class: "text-center",
        type: 'money',
        headerClass: "text-center",
        label: 'clients'
    }, {
        name: 'status',
        label: 'Estado',
        render(row) {
            return h(ElTag, { type: getRentStatusColor(row.status), class: 'capitalize' }, getRentStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: 'Acciones',
        align: 'center',
        minWidth: 150,
    }
]
