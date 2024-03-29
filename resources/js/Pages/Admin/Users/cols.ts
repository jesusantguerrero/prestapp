import { getRentStatusColor, getRentStatus } from '@/Modules/properties/constants';
import { formatDate } from '@/utils/index';
import { formatMoney } from '@/utils/formatMoney';
import { IClient } from '@/Modules/clients/clientEntity';
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
        name: 'name',
        label: 'Name',
        align: 'left',
        minWidth: 150,
        render(row: Record<string, any>) {
            return h(Link, { href: `/admin/teams/${row.id}` }, `${row.name}<${row.email}>` )
        }
      },
      {
          name: 'last_login_at',
          label: 'Last login',
          class: "text-left",
          headerClass: "text-center",
          align: 'left',
          minWidth: 200,
          render(row: Record<string, any>) {
            return h('div', formatDate(row.last_login_at))
          }
    },{
        name: 'days',
        label: 'Dias de registro',
        class: "text-center",
        align: 'center',
        render(row: IRent) {
            let daysLeft =  differenceInCalendarDays(new Date(), parseISO(row.created_at))
            return h(Link, {href: `/contacts/${row.client?.id}/tenants/rents/${row.id}/renew`} , h(ElTag, { type: getRentStatusColor(row.status) }, daysLeft))
        }
    },
    {
        name: 'actions',
        label: 'Acciones',
        minWidth: 150,
    }
]
