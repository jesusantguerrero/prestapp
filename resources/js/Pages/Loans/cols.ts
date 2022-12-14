import { ILoanWithInstallments } from "./../../Modules/loans/loanEntity";
import { h } from "vue";
import { ElTag } from "element-plus";
// @ts-ignore
import { getLoanStatus } from "@/Modules/loans/constants";
import { getLoanStatusColor } from "../../Modules/loans/constants";

export default [
  {
    name: "first_installment_date",
    class: "text-center",
    headerClass: "text-center",
    label: "Fecha de inicio",
  },
  {
    name: "client",
    label: "Cliente",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return row.client?.fullName;
    },
  },
  {
    name: "amount",
    label: "Prestado/Deuda",
    type: "money",
    class: "text-right",
    headerClass: "text-right",
  },
  {
    name: "interest_rate",
    label: "Tasa de Interés",
    class: "text-center",
    headerClass: "text-center",
    render(row: ILoanWithInstallments) {
      return row.interest_rate + " %";
    },
  },
  {
    name: "payment_status",
    label: "Estado",
    render(row: ILoanWithInstallments) {
      return h(
        // @ts-ignore
        ElTag,
        { type: getLoanStatusColor(row.payment_status) },
        getLoanStatus(row.payment_status)
      );
    },
  },
  {
    name: "actions",
    label: "Acciones",
    class: "text-right",
    headerClass: "text-right",
  },
];
