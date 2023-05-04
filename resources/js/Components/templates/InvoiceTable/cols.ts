import { formatDate } from "@/utils";

export default [
    {
        label: "Concept",
        name: "concept",
        width: 400,
    },
    {
      label: "Fecha",
      name: "date",
      align: 'center',
      class: 'text-center',
      width: 90,
      render(row) {
          return formatDate(row.date);
      },
    },
    // {
    //     label: "Categoria / Propiedad",
    //     name: "category",
    //     width: 300,
    // },
    {
      label: "Total / Deuda",
      name: "total",
      type: 'money',
      width: 150,
    },
    // {
    //   label: "Status",
    //   name: "status",
    //   width: 150,
    // },
    {
      label: " ",
      minWidth: 300,
      name: "actions",
      type: "custom",
    },
];
