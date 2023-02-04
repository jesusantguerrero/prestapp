import{_ as w}from"./AppLayout.vue_vue_type_script_setup_true_lang-dc441d96.js";import"./AppLayout.vue_vue_type_style_index_0_lang-cff2a039.js";import{N as C,i as d,b as N}from"./atmosphere-ui-43ec926d.js";import{_ as I}from"./IncomeSummaryWidget.vue_vue_type_script_setup_true_lang-3fd70125.js";import{_ as P}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-d46ec886.js";import{_ as b}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-c3a05c67.js";import A from"./LoanSectionNav-f1a482ff.js";import{_ as $}from"./AppButton.vue_vue_type_script_setup_true_lang-78bec775.js";import{f as s}from"./formatMoney-b7ef7683.js";import{c as p,w as a,o as i,b as t,h as f,a as m,u as n,f as g,j as y,F as h,t as E}from"./app-fef21123.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-3ea39cd1.js";import"./PaymentGrid-926a1204.js";/* empty css                                                                    */import"./mathHelper-02ca0ff8.js";import"./exact-math.node-8398c915.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-f3e358be.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import"./usePaymentModal-a07a875e.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-396b1c58.js";import"./close-ce456e3e.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-5401ee99.js";import"./clientInteractions-23fb4c77.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-7eed6d28.js";import"./AppSearchFilters-54a857cd.js";import"./index-901d5032.js";import"./constants-ba74557c.js";import"./menus-be25f72f.js";const D={class:"mx-auto text-gray-500 md:mt-16 sm:px-6 lg:px-8"},j={class:"flex flex-col mt-8 lg:space-x-4 lg:flex-row"},k={class:"order-1 space-y-5 lg:w-5/12 lg:order-2"},B={class:"px-4 py-1 space-y-2"},pe={__name:"Overview",props:{revenue:{type:Object,default(){return{previousYear:{values:[]},currentYear:{values:[]}}}},user:{type:Object,required:!0},activeLoanClients:{type:Number},loanCapital:{type:Number},loanExpectedInterest:{type:Number},loanPaidInterest:{type:Number},bank:{type:Number},dailyBox:{type:Number},cashOnHand:{type:Number},nextInvoices:{type:Array},debtors:{type:Array}},setup(l){const e=l,_=[{label:"Clientes con Prestamos",value:e.activeLoanClients,icon:"fa-users"},{label:"Capital Prestado",icon:"fa-money",value:s(e.loanCapital)},{label:"Interes Esperado",icon:"fa-wallet",value:s(e.loanExpectedInterest)},{label:"Total Interes pagado",value:s(e.loanPaidInterest),accent:!0}],x=[{name:"caja",title:"Caja Chica"},{name:"pagos",title:"Pagos"},{name:"ganancias",title:"Ingresos"},{name:"debtors",title:"Deudores"}],v={headers:{gapName:"Year",previous:e.revenue.previousYear.total,current:e.revenue.currentYear.total},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:["#fa6b88","#80CDFE"]},series:[{name:"previous year",data:e.revenue.previousYear.values.map(r=>r.total)},{name:"current year",data:e.revenue.currentYear.values.map(r=>r.total)}]};return(r,c)=>(i(),p(w,{title:"Dashboard"},{header:a(()=>[t(A,null,{actions:a(()=>[t($,{variant:"inverse",onClick:c[0]||(c[0]=o=>r.router.visit("/loans/create"))},{default:a(()=>[f(" Nuevo prestamo ")]),_:1})]),_:1})]),default:a(()=>[m("main",D,[t(P,{message:"Bienvenido a PrestApp",username:l.user.name,cards:_},null,8,["username"]),m("section",j,[t(I,{class:"order-2 mt-4 lg:w-8/12 lg:mt-0 lg:order-1",chart:v,headerInfo:v.headers},null,8,["headerInfo"]),m("article",k,[t(n(d),{class:"text-white bg-blue-400 h-36",icon:"fas fa-wallet",value:n(s)(e.cashOnHand.balance|0),title:"Cartera de Prestamos"},{action:a(()=>[t(n(C),{class:"bg-blue-500 rounded-md",onClick:c[1]||(c[1]=o=>r.isTransferModalOpen=!0)},{default:a(()=>[f(" Add Transaction ")]),_:1})]),_:1},8,["value"]),t(n(N),{class:"h-full rounded-md",slides:x},{caja:a(()=>[t(n(d),{class:"w-full h-full text-gray-400 rounded-t-none rounded-b-none",icon:"fas fa-wallet",value:n(s)(e.dailyBox.balance),title:"Caja Chica"},null,8,["value"])]),ganancias:a(()=>[t(n(d),{class:"w-full h-full text-blue-400 rounded-t-none rounded-b-none",icon:"fas fa-dollar-sign",value:"5,000",title:"Interes Ganado"})]),debtors:a(()=>[(i(!0),g(h,null,y(l.debtors,o=>(i(),p(b,{invoice:o,actions:{payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},onAction:u=>r.handleActions(u,o)},null,8,["invoice","onAction"]))),256)),f(" "+E(l.debtors),1)]),pagos:a(()=>[m("div",B,[(i(!0),g(h,null,y(l.nextInvoices,o=>(i(),p(b,{invoice:o,actions:{payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},onAction:u=>r.handleActions(u,o)},null,8,["invoice","onAction"]))),256))])]),_:1})])])])]),_:1}))}};export{pe as default};