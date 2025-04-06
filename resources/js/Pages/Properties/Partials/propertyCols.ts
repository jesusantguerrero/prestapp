import UnitTitle  from '@/Components/realState/UnitTitle.vue';
import { formatMoney } from '../../../utils/formatMoney';
import { h } from "vue";
import { ElTag } from "element-plus";
import MaterialSymbolsHomeWorkOutline from '~icons/material-symbols/home-work-outline'
// @ts-ignore
import { IProperty } from '@/Modules/properties/propertyEntity';
// @ts-ignore
import IconMarker from "@/Components/icons/IconMarker.vue";
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";
import IconMarkerVue from '@/Components/icons/IconMarker.vue';
import { Link } from '@inertiajs/vue3';


export default (t = (text: string) => text) => ([
    {
        name: 'address',
        label: t('Address'),
        class: "text-left",
        width: 500,
        render(row: IProperty) {
          return h(Link, { class: 'justify-center cursor-pointer', href: `/properties/${row.id}` }, [
            // @ts-ignore
              h(UnitTitle, {
                icon: MaterialSymbolsHomeWorkOutline,
                title:  `${row.name}`,
                ownerName: row.owner?.display_name,
                tenantName: row.contract?.client?.display_name,
              }),
              h('div', { class: 'flex items-center pl-2 mt-2 space-x-2 text-body-1 font-bold'}, [
                h(IconMarkerVue, { class: 'font-bold mt-1 w-6 h-6'}),
                h('span', row.address)
              ])
          ]);
        }
    },
    {
        name: 'balance',
        label: t('Pending balance'),
        align: 'right',
        class: 'text-right pr-4',
        width: 200,
        render(row: IProperty) {
            // @ts-ignore: got the right types
            return formatMoney(row.balance)
        }
    },
    {
        name: 'status',
        label: t('Status'),
        width: 300,
        render(row: IProperty) {
            // @ts-ignore: got the right types
            return h(ElTag, { type: getPropertyStatusColor(row.status) }, getPropertyStatus(row.status))
        }
    },
    {
        name: 'actions',
        label: ' '
    }
]);
