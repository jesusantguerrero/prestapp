import UnitTitle  from '@/Components/realState/UnitTitle.vue';
import { formatMoney } from '../../../utils/formatMoney';
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
        width: 600,
        render(row: IProperty) {
          return h('div', { class: 'justify-center' }, [
              h(UnitTitle, {
                title:  row.short_name,
                ownerName: row.owner?.display_name,
                tenantName: row.contract?.client?.display_name,
              }),
          ]);
        }
    },
    {
        name: 'balance',
        label: 'Balance Pendiente',
        align: 'right',
        class: 'text-right pr-4',
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
        label: ' '
    }
]
