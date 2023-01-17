import { formatDate } from "@/utils";

export default [
    {
        label: "Concept",
        name: "concept",
        width: 300,
    },
    {
      label: "Date",
      name: "date",
      width: 200,
      render(row) {
          return format(row.date);
      },
    },
    {
        label: "Categoria / Propiedad",
        name: "category",
        width: 100,
    },
    {
      label: "Total / Deuda",
      name: "total",
      type: 'money'
    },
    {
        label: "Status",
        name: "status",
        width: 200,
    },
    {
        label: "",
        name: "actions",
        width: 300,
        type: "custom",
    },
];
