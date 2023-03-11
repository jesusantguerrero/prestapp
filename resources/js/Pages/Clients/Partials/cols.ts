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
        width: 300,
        render(row: IClient) {
            const clientName = row.names + ' ' + row.lastnames
            const initials = row.names ? row.names[0] + row.lastnames[0] : '';
            const type = Object.entries(row).reduce((type, [field, value]) => {
              if (field.match(/owner|tenant|lender/) && value == 1) {
                type = field.replace('is_', '');
              }
              return type;
            }, "");

            return h('div', { class: 'flex items-center space-x-2 px-4' }, [
                h(ElAvatar, { shape: 'circle', width: 20, height: 20, maxWidth: 20, maxHeight: 20 }, initials),
                h('div', { class: 'ml-2 w-full text-left'},  [
                  h(Link, {class: 'font-bold text-primary', href: `/contacts/${row.id}/${type}`}, clientName),
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
            width: 130,
    },
    {
            name: 'address_details',
            label: 'Dirección',
            class: "text-left",
            headerClass: "text-left",
            render(row: IClient) {
              const address = row.rent ? row.rent.property.short_name : row.address_details
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
