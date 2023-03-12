import{_ as k}from"./note-off-8ea22dac.js";import{F as C,t as N}from"./atmosphere-ui-86b7f8ed.js";import{_ as $}from"./IncomeSummaryWidget.vue_vue_type_script_setup_true_lang-7bf9842c.js";import{_}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-f5ad8b33.js";import{_ as B}from"./NextPaymentsWidget.vue_vue_type_script_setup_true_lang-a78ff0fd.js";import{_ as Y}from"./DashboardTemplate.vue_vue_type_script_setup_true_lang-99593b99.js";import F from"./ChartBar-58780b14.js";import{f as m}from"./formatMoney-b7ef7683.js";import{_ as M}from"./PaymentsCard.vue_vue_type_script_setup_true_lang-461e7af0.js";import{c}from"./config-3e030ca7.js";import{_ as T}from"./RepaymentWidget.vue_vue_type_script_setup_true_lang-ab110fdf.js";import{u as j}from"./user-outline-dcfa7b13.js";import{l as E,r as P,c as p,w as t,o as n,b as a,a as o,u as i,h as b,y as D,f as y,j as L,F as R}from"./app-e7293397.js";import"./index-9dc2d84c.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./index-3624ec38.js";import"./InstallmentTable.vue_vue_type_script_setup_true_lang-cbcb4ed7.js";import"./sharp-payment-a1c72b2d.js";import"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import"./BaseTable.vue_vue_type_style_index_0_lang-efd97e4a.js";import"./customCell-f0a6687f.js";import"./close-f1b40d6b.js";import"./usePaymentModal-ad844d5c.js";import"./IconMarker-562faee5.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./ButtonGroup.vue_vue_type_script_setup_true_lang-6c7ef160.js";import"./receipt-34b79759.js";import"./document-aa315096.js";const A={class:"flex flex-col mt-8 lg:space-x-4 lg:flex-row"},G={class:"lg:w-8/12"},O={class:"flex space-x-2"},S={class:"order-1 space-y-5 lg:w-5/12 lg:order-2"},V={class:"py-4 my-2 h-[380px] overflow-auto ic-scroller"},q={key:1,class:"flex text-body-1 flex-col justify-center items-center w-full"},z=o("p",{class:"mt-8"},"No hay pagos realizados en este rango de fechas",-1),Le=E({__name:"Loans",props:{revenue:{type:Object,default(){return{previousYear:{values:[]},currentYear:{values:[]}}}},user:{type:Object,required:!0},activeLoanClients:{type:Number},loanCapital:{type:Number},loanExpectedInterest:{type:Number},loanPaidInterest:{type:Number},paidInterest:{type:Array},bank:{type:Number}},setup(d){var v;const e=d,g=[{label:"Clientes con Prestamos",value:e.activeLoanClients,icon:"fa-users"},{label:"Capital Prestado",icon:"fa-money",value:m(e.loanCapital??0)},{label:"Interes Esperado",icon:"fa-wallet",value:m(e.loanExpectedInterest??0)},{label:"Total Interes pagado",value:m(e.loanPaidInterest??0),accent:!0}],f={headers:{gapName:"Year",previous:e.revenue.previousYear.total,current:e.revenue.currentYear.total},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:[c.colors.highlight,c.colors.info]},series:[{name:"Año anterior",data:e.revenue.previousYear.values.map(r=>r.total)},{name:"Este año",data:e.revenue.currentYear.values.map(r=>r.total)}]},h={headers:{gapName:"Year",month:e.paidInterest.months.at(-1).outcome,avg:e.paidInterest.avg,current:(v=e.paidInterest)==null?void 0:v.year},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:[c.colors.highlight,c.colors.info]},series:[{name:"Ganancias intereses",data:e.paidInterest.months.map(r=>r.outcome)}]},u=P("cash-flow"),{openTransactionModal:x}=j();return(r,s)=>{const I=k;return n(),p(Y,{user:d.user},{default:t(()=>[a(_,{message:"Bienvenido a ICLoan",username:d.user.name,cards:g},null,8,["username"]),o("section",A,[o("article",G,[a(_,{message:"Rendimiento del mes",class:"order-2 mt-4 lg:mt-0 lg:order-1"},{actions:t(()=>[o("section",O,[o("button",{onClick:s[0]||(s[0]=l=>u.value="gains"),class:"bg-base-lvl-2 rounded-3xl text-body-1 px-4"}," Ganancias "),o("button",{onClick:s[1]||(s[1]=l=>u.value="cash-flow"),class:"bg-base-lvl-2 rounded-3xl text-body-1 px-4"}," Cash ")])]),content:t(()=>[u.value=="cash-flow"?(n(),p($,{key:0,title:"Flujo de efectivo",description:"Flujo de efectivo en el año",style:{height:"300px"},chart:f,headerInfo:f.headers},null,8,["headerInfo"])):(n(),p(F,{key:1,title:"Ganancias",description:"Ganancias por intereses en el año",chart:h,headerInfo:h.headers},null,8,["headerInfo"]))]),_:1}),a(T,{class:"mt-4"})]),o("article",S,[a(i(N),{class:"text-white bg-secondary h-36",icon:"fas fa-wallet",value:i(m)(e.bank.balance|0),title:"Balance cuenta prestamos"},{action:t(()=>[a(i(C),{class:"bg-secondary/60 rounded-md",onClick:s[2]||(s[2]=l=>i(x)({mode:"TRANSFER"}))},{default:t(()=>[b(" Agregar fondos ")]),_:1})]),_:1},8,["value"]),a(B,{title:"Pagos por periodo",endpoint:"/api/loan-payments?",method:"back","default-range":"7D","date-field":"payment_date",class:"rounded-md border",ranges:[{label:"1D",value:[1,1],tooltip:"Hoy"},{label:"7D",value:[7,0],tooltip:"7 Dias"},{label:"1M",value:[30,0],tooltip:"30 Dias"},{label:"3M",value:[90,0],tooltip:"3 Meses"}]},{beforeRange:t(()=>[a(i(D),{href:"/payment-center?tab=payments"},{default:t(()=>[b("Todos")]),_:1})]),content:t(({list:l})=>[o("article",V,[l.length?(n(!0),y(R,{key:0},L(l,w=>(n(),p(M,{payment:w},null,8,["payment"]))),256)):(n(),y("section",q,[a(I,{class:"text-8xl"}),z]))])]),_:1})])])]),_:1},8,["user"])}}});export{Le as default};
