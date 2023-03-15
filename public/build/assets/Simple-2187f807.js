import{p as g}from"./index-1a0c9adf.js";import{_ as S}from"./InvoiceTotals.vue_vue_type_style_index_0_lang-9e8793e7.js";import V from"./InvoiceGrid-960550dc.js";import{_ as N}from"./ClientCard.vue_vue_type_script_setup_true_lang-721ae044.js";import{_ as C}from"./BusinessCard.vue_vue_type_script_setup_true_lang-2db3943c.js";import{f as D}from"./index-9dc2d84c.js";import{r as M,t as h}from"./index-81f33ca2.js";import{d as k}from"./index-4b317f4f.js";import{l as q,z as E,d as j,m as B,B as O,f as m,a as t,g as v,b,t as c,u as e,h as y,W as U,Y as T,o as _}from"./app-e7293397.js";import{_ as Y}from"./_plugin-vue_export-helper-c27b6911.js";import"./index-3624ec38.js";import"./trash-b41c2bba.js";import"./exact-math.node-9a256fc0.js";import"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import"./atmosphere-ui-86b7f8ed.js";import"./formatMoney-b7ef7683.js";import"./IconTrash-b225eb02.js";import"./AtTable.vue_vue_type_script_setup_true_lang-805493b4.js";import"./customCell-f0a6687f.js";import"./index-c251e33c.js";function F(o,a){var l=o.getFullYear()-a.getFullYear()||o.getMonth()-a.getMonth()||o.getDate()-a.getDate()||o.getHours()-a.getHours()||o.getMinutes()-a.getMinutes()||o.getSeconds()-a.getSeconds()||o.getMilliseconds()-a.getMilliseconds();return l<0?-1:l>0?1:l}function $(o,a){M(2,arguments);var l=h(o),u=h(a),r=F(l,u),f=Math.abs(k(l,u));l.setDate(l.getDate()-r*f);var i=Number(F(l,u)===-r),n=r*(f-i);return n===0?0:n}const d=o=>(U("data-v-60bf0f21"),o=o(),T(),o),A={class:"w-full py-2 rounded-md section"},G={class:"section-body"},H={class:"pt-8 invoice__header"},P={class:"flex w-full invoice-header-details"},z={class:"flex justify-between px-4 w-full space-y-4 invoice-details"},W={class:"flex items-center"},J={key:0,class:"rounded-md"},K=["src"],Q={class:"w-full text-right"},X={class:"px-5 text-primary text-2xl font-bold"},Z={class:"text-md"},L=d(()=>t("span",{class:"font-bold"},"Concepto:",-1)),R=d(()=>t("span",{class:"font-bold"},"Fecha",-1)),tt=d(()=>t("span",{class:"font-bold"},"Fecha Limite",-1)),et={class:"flex px-4 mt-4 space-x-4"},st={class:"w-8/12 text-left"},ot={class:"flex justify-end px-4 mt-10 text-gray-600"},lt={key:0,class:"flex text-center invoice-footer-details mt-14"},at={class:"w-full text-gray-600"},it=d(()=>t("p",{class:"font-bold text-gray-600"},"Gracias por su preferencia!",-1)),nt={key:0,class:"mt-5 font-bold text-gray-600"},ct={key:1},rt={class:"w-full text-right justify-center flex flex-col items-end"},dt=d(()=>t("div",{class:"font-serif invoice__signature w-96 border-b-2 mb-2 mx-auto"},null,-1)),ut={class:"text-center w-full justify-center"},mt={class:"font-bold"},_t=d(()=>t("div",null,"Firma",-1)),ft=q({__name:"Simple",props:{imageUrl:{default:"/logo.png"},type:{default:"INVOICE"},user:null,businessData:null,products:null,clients:null,invoiceData:null},setup(o){const a=o,l=E({totalValues:{},totals:{subtotalField:"subtotal",totalField:"amount",discountField:"discountTotal",subtotalFormula(s){return s.quantity*s.price},totalFormula(s){return s.quantity*s.price},discountFormula(s){return s.quantity*s.price}},invoice:{},selectedPayment:null,isPaymentDialogVisible:!1,modals:{email:{value:!1}},tableData:[],client:null,imageUrl:"",dueDays:j(()=>$(l.invoice.due_date,l.invoice.date))}),u=s=>{s&&(s.date=h(g(s.date)||new Date),s.due_date=h(g(s.due_date)||new Date),l.invoice=s,l.client=s.client,l.tableData=s.lines.sort((x,p)=>x.index>p.index?1:-1)||[])};B(()=>a.invoiceData,s=>{s&&u(s)},{immediate:!0,deep:!0});const{tableData:r,client:f,invoice:i,totals:n,totalValues:I,dueDays:w}=O(l);return(s,x)=>{var p;return _(),m("section",A,[t("div",G,[t("div",H,[t("div",P,[t("div",z,[t("section",W,[o.imageUrl?(_(),m("div",J,[t("img",{src:o.imageUrl,class:"w-96"},null,8,K)])):v("",!0),b(C,{business:o.businessData,class:"w-full text-left"},null,8,["business"])]),t("div",Q,[t("h4",X," Factura "+c(e(i).series)+"-"+c(e(i).number),1),t("h5",Z,[L,y(" "+c(e(i).concept),1)]),t("div",null,[t("p",null,[R,y(" "+c(e(D)(e(i).date)),1)]),t("p",null,[tt,y(" "+c(e(D)(e(i).due_date)),1)])])])])]),t("div",et,[t("div",st,[b(N,{label:"Cliente",client:e(f)},null,8,["client"])])])]),b(V,{tableData:e(r),"is-editing":!1,"hidden-cols":["quantity","discount"],class:"mt-10 main-grid w-full"},null,8,["tableData"]),t("div",ot,[b(S,{tableData:e(r),"subtotal-field":e(n).subtotalField,"discount-field":e(n).discountField,payments:e(i).payments,"total-values":e(I),"total-field":e(n).totalField,subtotalFormula:e(n).subtotalFormula,discountFormula:e(n).discountFormula,totalFormula:e(n).totalFormula},null,8,["tableData","subtotal-field","discount-field","payments","total-values","total-field","subtotalFormula","discountFormula","totalFormula"])]),e(i).id?(_(),m("div",lt,[t("div",at,[it,e(i).type=="INVOICE"?(_(),m("div",nt," Terminos y condiciones ")):v("",!0),e(i).type=="INVOICE"?(_(),m("div",ct," El pago debe ser dentro de "+c(e(w))+" dias ",1)):v("",!0)]),t("section",rt,[dt,t("article",ut,[t("div",mt,c((p=o.user)==null?void 0:p.name),1),_t])])])):v("",!0)])])}}});const Bt=Y(ft,[["__scopeId","data-v-60bf0f21"]]);export{Bt as default};