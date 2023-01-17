import { IClient } from './../../Modules/clients/clientEntity';
import { h } from "vue";
import { ElAvatar, ElTag } from "element-plus"
// @ts-ignore
import { getPropertyStatus, getPropertyStatusColor } from "@/Modules/properties/constants";
import IconMarker from '@/Components/icons/IconMarker.vue';
import { getLoanStatus, getLoanStatusColor } from '@/Modules/loans/constants';
import { Link } from '@inertiajs/vue3';

interface IRent {
    client: IClient,
}

export default [
    {
        name: 'client',
        label: 'Cliente',
        class: "text-center",
        headerClass: "text-center",
        minWidth: 200,
        render(row: IClient) {
            const clientName = row.names + ' ' + row.lastnames
            const initials = row.names[0] + row.lastnames[0];

            return h('div', { class: 'flex items-center space-x-2' }, [
                h(ElAvatar, { shape: 'circle', width: 20, height: 20, maxWidth: 20, maxHeight: 20 }, initials),
                h('div', { class: 'ml-2 w-full text-left'},  [
                  h(Link, {class: 'font-bold text-primary', href: `/clients/${row.id}`}, clientName),
                  h('p', { class: 'text-body-1/80 text-sm'}, row.dni)
                ]),
            ]);
        }
    },
    {
            name: 'cellphone',
            label: 'Celular',
            class: "text-center",
            headerClass: "text-center",
    },
    {
            name: 'address',
            label: 'Dirección',
            class: "text-left",
            headerClass: "text-left",
            render(row: IClient) {
              if (row.rent) {
                return h('div', { class: 'justify-center' }, [
                  h('div', { class: 'flex items-start space-x-2 text-body-1 font-bold'}, [
                    h(IconMarker, { class: 'font-bold mt-1'}),
                    h('span', row.rent.property.short_name)
                  ]),
                ]);
              }
              return row.address;
            }
    },
    {
        name: 'status',
        label: 'Estado',
        render(row) {
            if (row.rent) {
              return h(ElTag, { type: getPropertyStatusColor(row.rent.status) }, getPropertyStatus(row.rent.status))
            } else {
              return h(ElTag, { type: getPropertyStatusColor(row.status) }, getPropertyStatus(row.status))
            }
        }
    },
    {
        name: 'actions',
        label: 'Acciones'
    }
]