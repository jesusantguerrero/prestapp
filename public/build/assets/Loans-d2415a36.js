import{N as w,i as u,b as x}from"./atmosphere-ui-cf0281b4.js";import{_ as N}from"./IncomeSummaryWidget.vue_vue_type_script_setup_true_lang-18d15742.js";import{_ as I}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-e2c482ec.js";import{_ as b}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-391640e9.js";import{_ as P}from"./NextPaymentsWidget.vue_vue_type_script_setup_true_lang-4bf490a1.js";import{f as s}from"./formatMoney-b7ef7683.js";import{_ as A}from"./DashboardTemplate.vue_vue_type_script_setup_true_lang-9e55daee.js";import{k as E,c as d,w as r,o as i,b as a,a as c,u as o,h as v,f as g,j as y,F as h,t as $}from"./app-5778ba55.js";import"./index-8305cd09.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./constants-a27b9dfb.js";import"./usePaymentModal-7f0729f4.js";import"./InstallmentTable.vue_vue_type_script_setup_true_lang-7bce7c85.js";import"./sharp-payment-87c77348.js";import"./AppButton.vue_vue_type_script_setup_true_lang-bb509e3b.js";import"./BaseTable.vue_vue_type_style_index_0_lang-46fdd41f.js";import"./customCell-8a32bb2a.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-5f2b06a0.js";import"./close-287af76b.js";import"./AppSearchFilters-2dae0cfe.js";import"./AppLayout.vue_vue_type_script_setup_true_lang-3f2d2e09.js";import"./AppLayout.vue_vue_type_style_index_0_lang-b5fae1fa.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-de8b7819.js";import"./PaymentGrid-aea5b371.js";/* empty css                                                                    */import"./mathHelper-b9912ba5.js";import"./exact-math.node-d4a7cd04.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-d05957ec.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-c8c2da4a.js";/* empty css                                                   */import"./Modal.vue_vue_type_script_setup_true_lang-32b3b269.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-becaa604.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-a74f0c22.js";import"./clientInteractions-39478df6.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-4fc6099a.js";const j={class:"flex flex-col mt-8 lg:space-x-4 lg:flex-row"},k={class:"lg:w-8/12"},B={class:"order-1 space-y-5 lg:w-5/12 lg:order-2"},D={class:"px-4 py-1 space-y-2"},ye=E({__name:"Loans",props:{revenue:{type:Object,default(){return{previousYear:{values:[]},currentYear:{values:[]}}}},user:{type:Object,required:!0},activeLoanClients:{type:Number},loanCapital:{type:Number},loanExpectedInterest:{type:Number},loanPaidInterest:{type:Number},bank:{type:Number},dailyBox:{type:Number},cashOnHand:{type:Number},nextInvoices:{type:Array},debtors:{type:Array}},setup(n){const e=n,C=[{label:"Clientes con Prestamos",value:e.activeLoanClients,icon:"fa-users"},{label:"Capital Prestado",icon:"fa-money",value:s(e.loanCapital)},{label:"Interes Esperado",icon:"fa-wallet",value:s(e.loanExpectedInterest)},{label:"Total Interes pagado",value:s(e.loanPaidInterest),accent:!0}],_=[{name:"caja",title:"Caja Chica"},{name:"pagos",title:"Pagos"},{name:"ganancias",title:"Ingresos"},{name:"debtors",title:"Deudores"}],p={headers:{gapName:"Year",previous:e.revenue.previousYear.total,current:e.revenue.currentYear.total},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:["#fa6b88","#80CDFE"]},series:[{name:"previous year",data:e.revenue.previousYear.values.map(t=>t.total)},{name:"current year",data:e.revenue.currentYear.values.map(t=>t.total)}]};return(t,f)=>(i(),d(A,{user:n.user},{default:r(()=>[a(I,{message:"Bienvenido a ICLoan",username:n.user.name,cards:C},null,8,["username"]),c("section",j,[c("article",k,[a(N,{class:"order-2 mt-4 lg:mt-0 lg:order-1",style:{height:"300px"},chart:p,headerInfo:p.headers},null,8,["headerInfo"]),a(P)]),c("article",B,[a(o(u),{class:"text-white bg-blue-400 h-36",icon:"fas fa-wallet",value:o(s)(e.cashOnHand.balance|0),title:"Cartera de Prestamos"},{action:r(()=>[a(o(w),{class:"bg-blue-500 rounded-md",onClick:f[0]||(f[0]=l=>t.isTransferModalOpen=!0)},{default:r(()=>[v(" Add Transaction ")]),_:1})]),_:1},8,["value"]),a(o(x),{class:"h-full rounded-md",slides:_},{caja:r(()=>[a(o(u),{class:"w-full h-full text-gray-400 rounded-t-none rounded-b-none",icon:"fas fa-wallet",value:o(s)(e.dailyBox.balance),title:"Caja Chica"},null,8,["value"])]),ganancias:r(()=>[a(o(u),{class:"w-full h-full text-blue-400 rounded-t-none rounded-b-none",icon:"fas fa-dollar-sign",value:"5,000",title:"Interes Ganado"})]),debtors:r(()=>[(i(!0),g(h,null,y(n.debtors,l=>(i(),d(b,{invoice:l,actions:{payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},onAction:m=>t.handleActions(m,l)},null,8,["invoice","onAction"]))),256)),v(" "+$(n.debtors),1)]),pagos:r(()=>[c("div",D,[(i(!0),g(h,null,y(n.nextInvoices,l=>(i(),d(b,{invoice:l,actions:{payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},onAction:m=>t.handleActions(m,l)},null,8,["invoice","onAction"]))),256))])]),_:1})])])]),_:1},8,["user"]))}});export{ye as default};
