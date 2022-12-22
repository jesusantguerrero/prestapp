import { formatDate } from "@/utils";

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
        label: "Categoria / Propiedad",
        name: "category",
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
        label: "Total / Deuda",
        name: "total",
        type: 'money'
    },
    {
        label: "",
        name: "actions",
        width: 300,
        type: "custom",
    },
];
