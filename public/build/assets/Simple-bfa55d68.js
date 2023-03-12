import{p as g}from"./index-1a0c9adf.js";import{_ as S}from"./InvoiceTotals.vue_vue_type_style_index_0_lang-fb964559.js";import M from"./InvoiceGrid-4cf1b47f.js";import{_ as V}from"./ClientCard.vue_vue_type_script_setup_true_lang-6e29d606.js";import{_ as N}from"./BusinessCard.vue_vue_type_script_setup_true_lang-7e0ded8d.js";import{f as C}from"./index-c251e33c.js";import{r as k,t as p}from"./index-81f33ca2.js";import{d as q}from"./index-4b317f4f.js";import{l as j,z as B,d as T,m as U,B as P,f as y,a as t,g as D,b as f,t as n,u as o,h,W as Y,Y as $,o as b}from"./app-494b6f90.js";import{_ as A}from"./_plugin-vue_export-helper-c27b6911.js";import"./index-3624ec38.js";import"./index-9dc2d84c.js";import"./formatMoney-b7ef7683.js";import"./exact-math.node-f7579f9b.js";import"./atmosphere-ui-83e2a306.js";import"./IconTrash-bc17a3dd.js";import"./AtTable.vue_vue_type_script_setup_true_lang-3e9914f8.js";import"./customCell-9e9ed16f.js";function F(s,a){var l=s.getFullYear()-a.getFullYear()||s.getMonth()-a.getMonth()||s.getDate()-a.getDate()||s.getHours()-a.getHours()||s.getMinutes()-a.getMinutes()||s.getSeconds()-a.getSeconds()||s.getMilliseconds()-a.getMilliseconds();return l<0?-1:l>0?1:l}function E(s,a){k(2,arguments);var l=p(s),r=p(a),d=F(l,r),m=Math.abs(q(l,r));l.setDate(l.getDate()-d*m);var v=Number(F(l,r)===-d),i=d*(m-v);return i===0?0:i}const c=s=>(Y("data-v-ac8e6ee7"),s=s(),$(),s),H={class:"w-full py-2 rounded-md section"},O={class:"section-body"},z={class:"pt-8 invoice__header"},G={class:"flex w-full invoice-header-details"},W={class:"flex justify-between px-4 w-full space-y-4 invoice-details"},J={class:"flex items-center"},K={key:0,class:"rounded-md"},Q=["src"],X={class:"w-full text-right"},Z={class:"px-5 text-primary text-2xl font-bold"},L={class:"text-md"},R=c(()=>t("span",{class:"font-bold"},"Concepto:",-1)),tt=c(()=>t("span",{class:"font-bold"},"Fecha",-1)),et=c(()=>t("span",{class:"font-bold"},"Fecha Limite",-1)),st={class:"flex px-4 mt-4 space-x-4"},ot={class:"w-8/12 text-left"},lt={class:"flex justify-end px-4 mt-10 text-gray-600"},at={key:0,class:"flex text-center invoice-footer-details mt-14"},it={class:"w-full text-gray-600"},nt=c(()=>t("p",{class:"font-bold text-gray-600"},"Thanks For your business!",-1)),ct=c(()=>t("div",{class:"mt-5 font-bold text-gray-600"},"Terms and conditions",-1)),rt={class:"w-full text-right justify-center flex flex-col items-end"},ut=c(()=>t("div",{class:"font-serif invoice__signature w-96 border-b-2 mb-2 mx-auto"},null,-1)),dt={class:"text-center w-full justify-center"},mt={class:"font-bold"},_t=c(()=>t("div",null,"Firma",-1)),ft=j({__name:"Simple",props:{imageUrl:{default:"/logo.png"},type:{default:"INVOICE"},user:null,businessData:null,products:null,clients:null,invoiceData:null},setup(s){const a=s,l=B({totalValues:{},totals:{subtotalField:"subtotal",totalField:"amount",discountField:"discountTotal",subtotalFormula(e){return e.quantity*e.price},totalFormula(e){return e.quantity*e.price},discountFormula(e){return e.quantity*e.price}},invoice:{},selectedPayment:null,isPaymentDialogVisible:!1,modals:{email:{value:!1}},tableData:[],client:null,imageUrl:"",dueDays:T(()=>E(l.invoice.due_date,l.invoice.date))}),r=e=>C(e,"MMM dd, yyyy"),d=e=>{e&&(e.date=p(g(e.date)||new Date),e.due_date=p(g(e.due_date)||new Date),l.invoice=e,l.client=e.client,l.tableData=e.lines.sort((x,_)=>x.index>_.index?1:-1)||[])};U(()=>a.invoiceData,e=>{e&&d(e)},{immediate:!0});const{tableData:m,client:v,invoice:i,totals:u,totalValues:w,dueDays:I}=P(l);return(e,x)=>{var _;return b(),y("section",H,[t("div",O,[t("div",z,[t("div",G,[t("div",W,[t("section",J,[s.imageUrl?(b(),y("div",K,[t("img",{src:s.imageUrl,class:"w-96"},null,8,Q)])):D("",!0),f(N,{business:s.businessData,class:"w-full text-left"},null,8,["business"])]),t("div",X,[t("h4",Z," Factura "+n(o(i).series)+"-"+n(o(i).number),1),t("h5",L,[R,h(" "+n(o(i).concept),1)]),t("div",null,[t("p",null,[tt,h(" "+n(r(o(i).date)),1)]),t("p",null,[et,h(" "+n(r(o(i).due_date)),1)])])])])]),t("div",st,[t("div",ot,[f(V,{label:"Cliente",client:o(v)},null,8,["client"])])])]),f(M,{tableData:o(m),"is-editing":!1,"hidden-cols":["quantity","discount"],class:"mt-10 main-grid w-full"},null,8,["tableData"]),t("div",lt,[f(S,{tableData:o(m),"subtotal-field":o(u).subtotalField,"discount-field":o(u).discountField,payments:o(i).payments,"total-values":o(w),"total-field":o(u).totalField,subtotalFormula:o(u).subtotalFormula,discountFormula:o(u).discountFormula,totalFormula:o(u).totalFormula},null,8,["tableData","subtotal-field","discount-field","payments","total-values","total-field","subtotalFormula","discountFormula","totalFormula"])]),o(i).id?(b(),y("div",at,[t("div",it,[nt,ct,t("div",null,"Payment is due within "+n(o(I))+" days",1)]),t("section",rt,[ut,t("article",dt,[t("div",mt,n((_=s.user)==null?void 0:_.name),1),_t])])])):D("",!0)])])}}});const jt=A(ft,[["__scopeId","data-v-ac8e6ee7"]]);export{jt as default};
