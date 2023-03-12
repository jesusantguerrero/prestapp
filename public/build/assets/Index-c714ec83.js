import{_ as j}from"./chevron-right-182a2a80.js";import{_ as C}from"./sharp-payment-0e03bd0a.js";import{_ as $,a as I,b as O}from"./arrow-up-thick-4c2313e0.js";import{F as u,t as p}from"./atmosphere-ui-83e2a306.js";import{_ as k}from"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import{_ as B}from"./IncomeSummaryWidget.vue_vue_type_script_setup_true_lang-f39335a4.js";import{_}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-801aae9c.js";import{_ as f}from"./SectionFooterCard.vue_vue_type_script_setup_true_lang-f3bf8d42.js";import{f as a}from"./formatMoney-b7ef7683.js";import{u as D}from"./user-outline-c87acd59.js";import{_ as M}from"./DashboardTemplate.vue_vue_type_script_setup_true_lang-7c8a26e3.js";import{_ as R}from"./FastAccessOptions.vue_vue_type_script_setup_true_lang-ff3d4599.js";import{l as Y,c as A,w as r,o as N,a as s,b as e,u as t,t as c,h as l,y as S}from"./app-494b6f90.js";import"./AppLayout.vue_vue_type_style_index_0_lang-3aae0800.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-2fd658ef.js";import"./close-40de6817.js";import"./PaymentGrid-a2bc0bdb.js";import"./mathHelper-d6bc48cd.js";import"./exact-math.node-f7579f9b.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-93902e28.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-7b13fe11.js";import"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-ca2f799a.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-d4cb9813.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-593db1f8.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-7ced5882.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-3cbc58a3.js";import"./usePaymentModal-f96f741a.js";import"./ButtonGroup.vue_vue_type_script_setup_true_lang-f813575d.js";const T={class:"w-full md:flex md:space-x-4"},F={class:"flex flex-col justify-between w-full md:w-9/12"},q={class:"grid-rows-1 py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2"},E={class:"flex items-center text-xs text-success md:text-base",rounded:""},P={class:"font-bold"},V={class:"flex items-center text-xs text-error/70 md:text-base",rounded:""},G={class:"font-bold"},H={class:"font-bold"},U={class:"flex flex-col mt-8 lg:space-x-4 lg:flex-row"},z={class:"order-1 space-y-2 lg:w-3/12 lg:order-2"},Oe=Y({__name:"Index",props:{revenue:{type:Object,default(){return{previousYear:{values:[]},currentYear:{values:[]}}}},user:{type:Object,required:!0},accounts:{type:Object,default(){return{}}},stats:{type:Object,default(){return{}}},paidCommissions:{type:Object,required:!0},dailyBox:{type:Object,required:!0},realState:{type:Object,required:!0},pendingDraws:{type:Number}},setup(o){const n=o,x={headers:{gapName:"Year",previous:n.revenue.previousYear.total,current:n.revenue.currentYear.total},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:["#fa6b88","#80CDFE"]},series:[{name:"previous year",data:n.revenue.previousYear.values.map(i=>i.total)},{name:"current year",data:n.revenue.currentYear.values.map(i=>i.total)}]},{openTransactionModal:b}=D();return(i,m)=>{const v=$,h=I,g=O,y=C,w=j;return N(),A(M,{user:o.user},{default:r(()=>{var d;return[s("section",T,[s("div",F,[e(_,{message:"Hola, ",username:o.user.name,class:"shadow-sm"},{content:r(()=>[s("section",q,[e(f,{title:"Ganancias netas",value:t(a)(o.paidCommissions.balance)},{footer:r(()=>[s("p",E,[e(v),s("span",P,c(t(a)(o.accounts.assets.income))+" Recibido ",1)]),s("p",V,[e(h),s("span",G,c(t(a)(o.accounts.assets.outcome))+" Gastado ",1)])]),_:1},8,["value"]),e(f,{title:"Balance pendiente",value:t(a)(o.stats.outstanding),class:"md:pl-6"},{footer:r(()=>[e(t(u),{class:"flex items-center px-2 -ml-6 text-xs md:text-base text-error/70 hover:bg-error/10",rounded:""},{default:r(()=>[e(g,{class:"mr-2"}),s("span",H,c(t(a)(o.stats.overdue))+" En mora ",1)]),_:1}),e(t(u),{rounded:"",class:"flex items-center text-xs md:text-base text-primary hover:bg-primary/10"},{default:r(()=>[e(y,{class:"mr-2"}),l(" Recibir Pago ")]),_:1})]),_:1},8,["value"])])]),_:1},8,["username"]),e(t(S),{href:"/properties/management-tools",class:"flex items-center hover:text-primary hover:font-bold transition-all justify-center w-full h-10 mt-4 rounded-md shadow-sm bg-base-lvl-3 md:mt-4"},{default:r(()=>[l(" Distribución a propietarios pendientes "),e(w),s("span",null,c(o.pendingDraws),1)]),_:1})]),e(_,{message:"Accesos Rapidos",class:"hidden md:block w-full mt-4 md:mt-0 md:w-3/12"},{content:r(()=>[e(R)]),_:1})]),s("section",U,[e(B,{class:"order-2 mt-4 lg:w-9/12 lg:mt-0 lg:order-1",chart:x,style:{height:"310px"}}),s("article",z,[e(t(p),{class:"h-32 border-2 cursor-pointer text-primary bg-primary/10 border-primary/20",icon:"fas fa-wallet",value:t(a)(((d=n.dailyBox)==null?void 0:d.balance)|0),title:"Cuenta de Prestamos"},null,8,["value"]),e(t(p),{class:"h-32 border-2 cursor-pointer text-secondary bg-secondary/10 border-secondary/20",icon:"fas fa-wallet",value:t(a)(n.realState.balance|0),title:"Cuenta Inmobiliaria"},null,8,["value"]),e(k,{variant:"secondary",class:"w-full",onClick:m[0]||(m[0]=J=>t(b)({mode:"TRANSFER"}))},{default:r(()=>[l(" Agregar fondos ")]),_:1})])])]}),_:1},8,["user"])}}});export{Oe as default};
