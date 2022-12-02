import { h } from "vue";
import { ElTag } from "element-plus"
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";

export default [
    {   name: 'client',
        label: 'Cliente',
        class: "text-center",
        headerClass: "text-center",
        render(row) {
            return row.client.names + ' ' + row.client.lastnames;
        }
    },{
            name: 'amount',
            label: 'Monto Prestado',
            type: 'money',
            class: "text-right",
            headerClass: "text-right",
        }, {
            name: 'interest_rate',
            label: 'Tasa de Interés',
            class: "text-right",
            headerClass: "text-right",
            render(row) {
                return row.interest_rate + ' %'
            }
        }, {
            name: 'start_date',
            class: "text-center",
            headerClass: "text-center",
            label: 'Fecha de inicio'
        }, {
            name: 'payment_status',
            label: 'Estado',
            render(row) {
                console.log(getLoanStatusColor(row.payment_status) );
                return h(ElTag, { type: getLoanStatusColor(row.payment_status) }, getLoanStatus(row.payment_status))
            }
        }, 
        {
            name: 'actions',
            label: 'Acciones'
        }
]