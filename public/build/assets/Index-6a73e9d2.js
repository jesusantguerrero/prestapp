import{_ as O}from"./sharp-payment-a1c72b2d.js";import{_ as T,a as Y,b as A}from"./arrow-up-thick-a30720f5.js";import{l as C,c as N,w as a,o as R,a as s,b as e,u as t,t as l,h as v}from"./app-e7293397.js";import{F as h,t as g}from"./atmosphere-ui-86b7f8ed.js";import{_ as S}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{_ as D}from"./IncomeSummaryWidget.vue_vue_type_script_setup_true_lang-caa8d4ab.js";import{_ as y}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-f6e42422.js";import{_ as w}from"./SectionFooterCard.vue_vue_type_script_setup_true_lang-97aacd08.js";import{f as r}from"./formatMoney-b7ef7683.js";import{a as F,u as q}from"./user-outline-dcfa7b13.js";import{_ as E}from"./DashboardTemplate.vue_vue_type_script_setup_true_lang-99593b99.js";import{_ as P}from"./FastAccessOptions.vue_vue_type_script_setup_true_lang-32b05702.js";import"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./ButtonGroup.vue_vue_type_script_setup_true_lang-6c7ef160.js";const V={class:"w-full md:flex md:space-x-4"},G={class:"flex flex-col justify-between w-full md:w-9/12"},H={class:"grid-rows-1 py-4 space-y-4 md:grid md:grid-cols-2 md:divide-x-2"},U={class:"flex items-center text-xs text-success md:text-base",rounded:""},z={class:"font-bold"},J={class:"flex items-center text-xs text-error/70 md:text-base",rounded:""},K={class:"font-bold"},L={class:"font-bold"},Q=s("div",{class:"flex flex-col items-center justify-center w-full h-10 mt-4 rounded-md shadow-sm bg-base-lvl-3 md:mt-4"}," Distribución a propietarios pendientes ",-1),W={class:"flex flex-col mt-8 lg:space-x-4 lg:flex-row"},X={class:"order-1 space-y-2 lg:w-3/12 lg:order-2"},Ce=C({__name:"Index",props:{revenue:{type:Object,default(){return{previousYear:{values:[]},currentYear:{values:[]}}}},user:{type:Object,required:!0},accounts:{type:Object,default(){return{}}},stats:{type:Object,default(){return{}}},bank:{type:Number},dailyBox:{type:Object,required:!0},realState:{type:Object,required:!0}},setup(o){var m,d;const n=o;F("contact");const j={headers:{gapName:"Year",previous:(m=n.revenue.previousYear)==null?void 0:m.total,current:(d=n.revenue.currentYear)==null?void 0:d.total},options:{chart:{id:"vuechart-example"},stroke:{curve:"smooth"},dropShadow:{enabled:!0,top:3,left:0,blur:1,opacity:.5},colors:["#fa6b88","#80CDFE"]},series:[{name:"previous year",data:n.revenue.previousYear.values.map(c=>c.total)},{name:"current year",data:n.revenue.currentYear.values.map(c=>c.total)}]},{openTransactionModal:k}=q();return(c,u)=>{const $=T,B=Y,I=A,M=O;return R(),N(E,{user:o.user},{default:a(()=>{var p,f;return[s("section",V,[s("div",G,[e(y,{message:"Hola, ",username:o.user.name,class:"shadow-sm"},{content:a(()=>{var i,_;return[s("section",H,[e(w,{title:"Ganancias netas",value:t(r)(((i=o.accounts.assets)==null?void 0:i.total)+((_=o.accounts.liabilities)==null?void 0:_.total))},{footer:a(()=>{var x,b;return[s("p",U,[e($),s("span",z,l(t(r)((x=o.accounts.assets)==null?void 0:x.income))+" Recibido ",1)]),s("p",J,[e(B),s("span",K,l(t(r)((b=o.accounts.assets)==null?void 0:b.outcome))+" Gastado ",1)])]}),_:1},8,["value"]),e(w,{title:"Balance pendiente",value:t(r)(o.stats.outstanding),class:"md:pl-6"},{footer:a(()=>[e(t(h),{class:"flex items-center px-2 -ml-6 text-xs md:text-base text-error/70 hover:bg-error/10",rounded:""},{default:a(()=>[e(I,{class:"mr-2"}),s("span",L,l(t(r)(o.stats.overdue))+" En mora ",1)]),_:1}),e(t(h),{rounded:"",class:"flex items-center text-xs md:text-base text-primary hover:bg-primary/10"},{default:a(()=>[e(M,{class:"mr-2"}),v(" Recibir Pago ")]),_:1})]),_:1},8,["value"])])]}),_:1},8,["username"]),Q]),e(y,{message:"Accesos Rapidos",class:"hidden md:block w-full mt-4 md:mt-0 md:w-3/12"},{content:a(()=>[e(P)]),_:1})]),s("section",W,[e(D,{class:"order-2 mt-4 lg:w-9/12 lg:mt-0 lg:order-1",chart:j,style:{height:"310px"}}),s("article",X,[e(t(g),{class:"h-32 border-2 cursor-pointer text-primary bg-primary/10 border-primary/20",icon:"fas fa-wallet",value:t(r)(((p=n.dailyBox)==null?void 0:p.balance)|0),title:"Cuenta de Prestamos"},null,8,["value"]),e(t(g),{class:"h-32 border-2 cursor-pointer text-secondary bg-secondary/10 border-secondary/20",icon:"fas fa-wallet",value:t(r)(((f=n.realState)==null?void 0:f.balance)|0),title:"Cuenta Inmobiliaria"},null,8,["value"]),e(S,{variant:"secondary",class:"w-full",onClick:u[0]||(u[0]=i=>t(k)({mode:"TRANSFER"}))},{default:a(()=>[v(" Agregar fondos ")]),_:1})])])]}),_:1},8,["user"])}}});export{Ce as default};
