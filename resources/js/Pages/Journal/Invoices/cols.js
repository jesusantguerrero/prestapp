import { formatDate } from "@/Atmosphere/utils/formatDate";

export default [
    {
        label: "Date",
        name: "date",
        width: 200,
        render(row) {
            return format(row.date);
        },
    },
    {
        label: "Concept",
        name: "concept",
        width: 300,
    },
    {
        label: "Order No.",
        name: "order_number",
        width: 100,
    },
    {
        label: "Client",
        name: "client_name",
        width: 300,
    },
    {
        label: "Status",
        name: "status",
        width: 200,
    },
    {
        label: "Total",
        name: "total",
        type: 'money'
    },
    {
        label: "Balance Due",
        name: "debt",
        type: 'money'
    },
    {
        label: "",
        name: "actions",
        width: 300,
        type: "custom",
    },
];
