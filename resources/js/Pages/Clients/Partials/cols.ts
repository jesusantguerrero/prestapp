import { IClient } from '@/Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
import { getClientLink } from "@/Modules/clients/constants";
// @ts-ignore
import { getPropertyStatusColor } from "@/Modules/properties/constants";
import IconMarker from '@/Components/icons/IconMarker.vue';
import { Link, router } from '@inertiajs/vue3';
import UnitTitle from '@/Components/realState/UnitTitle.vue';
import { formatMoney } from '@/utils';

export default function (t: Function, defaultType = 'lender') {
  return [
    {
        name: 'client',
        label: 'Cliente',
        class: "text-center",
        headerClass: "text-center",
        width: 350,
        render(row: IClient) {
            const clientName = row.names + ' ' + row.lastnames
            const initials = row.names ? row.names[0] + row.lastnames[0] : '';

            return h('div', { class: 'flex items-center space-x-2 px-4 text-sm' }, [
                h(ElAvatar, { shape: 'circle', width: 20, height: 20, maxWidth: 20, maxHeight: 20 }, initials),
                h('div', { class: 'ml-2 w-full text-left'},  [
                  h(Link, {class: 'font-bold text-primary text-xs', href: getClientLink(row, defaultType)}, clientName),
                  h('p', { class: 'text-body-1/80 text-sm'}, row.dni)
                ]),
            ]);
        }
    },
    {
            name: 'address_details',
            label: 'Dirección',
            class: "text-left",
            headerClass: "text-left",
            width: 400,
            render(row: IClient) {
              const address = row.rent ? row.rent.property.short_name : row.address_details
              if (row.rent) {
                return h(UnitTitle, {
                  class: 'cursor-pointer',
                  title:  row.rent.address,
                  ownerName: row.rent.owner_name,
                  tenantName: formatMoney(row.rent?.amount),
                  detailDisplay: 'col',
                  onClick() {
                    router.visit(`/rents/${row.rent.id}`)
                  }
                })
              }

              return h('div', { class: 'justify-center' }, [
                h('div', { class: 'flex items-start space-x-2 text-body-1 font-bold'}, [
                  h(IconMarker, { class: 'font-bold mt-1 w-6 h-6'}),
                  h('span', address)
                ]),
              ]);
            }
    },
    {
        name: 'status',
        label: 'Estado',
        align: 'center',
        class: 'text-center',
        render(row: any) {
            if (row.rent) {
              return h(ElTag, { type: getPropertyStatusColor(row.rent.status) }, t(`commons.${row.status}`))
            } else {
              return h(ElTag, { type: getPropertyStatusColor(row.status) }, t(`commons.${row.status}`))
            }
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]
}
