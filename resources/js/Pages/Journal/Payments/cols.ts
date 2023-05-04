export default [
    {
        label: "Date",
        name: "payment_date",
        width: 200,
    },
    {
        label: "Concept",
        name: "concept",
        width: 360,
    },
    {
        label: "Client",
        name: "client_name",
        align: 'center',
        class: 'text-center',
        width: 300,
        render(row: Record<string, any>) {
          return row.payable?.client?.display_name ?? "--"
        }
    },
    {
        label: "Total",
        name: "total",
        type: 'money',
        width: 130,
    },
    {
        label: "",
        name: "actions",
        type: "custom",
    },
];
