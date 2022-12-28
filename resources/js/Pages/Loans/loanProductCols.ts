import { ILoanWithInstallments } from "../../Modules/loans/loanEntity";
import { h } from "vue";
import { ElTag } from "element-plus";
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";

export default [
  {
    name: "name",
    class: "text-center",
    headerClass: "text-center",
    label: "Nombre",
  },
  {
    name: "client",
    label: "Terminos",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return row.frequency;
    },
  },
  {
    name: "interest_rates",
    label: "Tasas de Interés",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return row.interest_rates.map(interest => interest.trim()).join('%, ') + '%';
    },
  },
  {
    name: "status",
    label: "Estado",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return h(
        // @ts-ignore
        ElTag,
        { type: 'success' },
        'Activo'
      );
    },
  },
  {
    name: "actions",
    label: "Acciones",
    class: "text-right",
    headerClass: "text-center",
  },
];
