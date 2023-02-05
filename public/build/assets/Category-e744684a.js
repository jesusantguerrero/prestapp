import{_ as F}from"./printer-4c524caf.js";import{k as M,d as w,z as C,r as U,B as k,c as A,w as m,u as o,E as H,C as L,o as i,a as t,b as l,h as S,t as a,n as V,f as u,j as T,g as $,F as D,af as R,V as G,W as J}from"./app-9b138141.js";import{f as d}from"./formatMoney-b7ef7683.js";import{_ as W}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as g}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{_ as q}from"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import{_ as K}from"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import{u as Q}from"./useServerSearch-3cc40805.js";import{_ as X}from"./_plugin-vue_export-helper-c27b6911.js";import"./atmosphere-ui-18006871.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";import"./index-901d5032.js";const Y=n=>({customPrint:(b=n)=>{const f=document.getElementById(b),y=f==null?void 0:f.cloneNode(!0);let r=document.getElementById("print");r||(r=document.createElement("div"),r.id="print",document.body.appendChild(r)),r.innerHTML="",r.appendChild(y),window.print()}}),j=n=>(G("data-v-480c6356"),n=n(),J(),n),Z={class:"flex items-center justify-end py-1 mx-5 rounded-md"},ee={class:"flex space-x-2 font-bold text-gray-500 rounded-t-lg max-w-min"},te={class:"w-full rounded-md bg-white mt-16 shadow-md printable py-10 mx-auto mb-32 sm:px-6 lg:px-8 print:shadow-none print:w-screen print:absolute print:mt-0",id:"report"},se={class:"text-center text-gray-500"},oe={class:"text-3xl font-bold capitalize"},ae=j(()=>t("h5",{class:"font-bold"},"Neatforms",-1)),ne=j(()=>t("p",null,"From date to date",-1)),re={class:"flex items-center justify-end space-x-2 print:hidden"},le={key:0,class:"w-full px-5 py-2 mt-5 font-bold bg-gray-200"},ie={key:1,class:"divide-y"},de={class:"px-5 py-2 font-semibold bg-gray-100"},pe={class:"w-full px-5"},ce={class:"flex justify-between py-2"},me={class:"font-semibold text-blue-500"},ue={class:"space-x-4"},_e={class:"font-bold text-success"},fe={class:"font-bold text-error"},ye={class:"font-bold"},ve={class:"flex space-x-4"},xe={class:"font-bold text-success"},be={class:"font-bold text-error"},he={class:"font-bold"},ge={class:"flex justify-between py-5 text-xl capitalize"},we={class:"font-bold"},Se={class:"font-bold"},Ce=M({__name:"Category",props:{categoryType:{type:String,default:"income"},categories:{type:Array},accounts:{type:Array},ledger:{type:Object},serverSearchOptions:{type:Object,default(){return{}}}},setup(n){const _=n,b=w(()=>`${R(_.categoryType)} - Statement`),f=C({isSummary:!0,isTransferModalOpen:!1,mainCategories:w(()=>_.categories),categoriesTotal:w(()=>_.categories.reduce((c,s)=>c+parseFloat(s.total||0),0)),transferAccount:null}),y=U(null),r=c=>{const s=c.category;return s&&(!y.value||y.value.id!==s.id)?(y.value=s,!0):!1},{isSummary:v,mainCategories:E,categoriesTotal:P}=k(f),{serverSearchOptions:B}=k(_),{executeSearch:z,state:p}=Q(B,c=>{H.get(`${window.location.pathname}?${c}`,{},{preserveScroll:!0,preserveState:!0})},{manual:!0}),h=C({property:null,account:null}),{customPrint:I}=Y("report");return(c,s)=>{const N=L("ElDatePicker"),O=F;return i(),A(W,{title:o(b)},{header:m(()=>[t("div",Z,[t("div",ee,[l(N,{"model-value":[o(p).dates.startDate,o(p).dates.endDate],type:"daterange","unlink-panels":"","range-separator":"To","start-placeholder":"Start date","end-placeholder":"End date",size:"large","onUpdate:modelValue":s[0]||(s[0]=e=>{o(p).dates.startDate=e[0],o(p).dates.endDate=e[1]})},null,8,["model-value"]),l(q,{endpoint:"/api/properties",modelValue:h.property,"onUpdate:modelValue":[s[1]||(s[1]=e=>h.property=e),s[2]||(s[2]=e=>o(p).filters.property=e.id)],label:"name","track-by":"id",class:"md:w-[200px]",placeholder:"Propiedad o Dueño"},null,8,["modelValue"]),l(K,{endpoint:"/api/accounts",modelValue:h.account,"onUpdate:modelValue":s[3]||(s[3]=e=>h.account=e),"onUpdate:modeValue":s[4]||(s[4]=e=>o(p).filters.account=e.id),class:"md:w-[200px]",multiple:""},null,8,["modelValue"]),l(g,{onClick:s[5]||(s[5]=e=>o(z)())},{default:m(()=>[S(" Generar Reporte ")]),_:1}),l(g,{variant:"neutral",onClick:s[6]||(s[6]=e=>o(I)())},{default:m(()=>[l(O)]),_:1})])])]),default:m(()=>[t("div",te,[t("header",se,[t("h4",oe,a(o(b)),1),ae,ne]),t("div",re,[t("section",null,a(n.ledger.assets[0].total)+" =",1),t("section",null,[l(g,{variant:"secondary",onClick:s[7]||(s[7]=e=>v.value=!0)},{default:m(()=>[S(" Summary ")]),_:1}),l(g,{variant:"secondary",onClick:s[8]||(s[8]=e=>v.value=!1)},{default:m(()=>[S(" Details ")]),_:1})])]),t("div",{class:V(["mt-10 items",{"divide-y":o(v)}])},[(i(!0),u(D,null,T(o(E),e=>(i(),u("div",{key:e.id,class:"py-2"},[r(e)?(i(),u("div",le,a(e.category.alias??e.category.name)+" "+a(e.category.total),1)):$("",!0),o(v)?$("",!0):(i(),u("div",ie,[t("div",de,a(e.alias??e.name),1),(i(!0),u(D,null,T(e.accounts,x=>(i(),u("div",pe,[t("div",ce,[t("span",me,a(x.alias??x.name),1),t("div",ue,[t("span",_e,a(o(d)(x.income)),1),t("span",fe,a(o(d)(x.outcome)),1),t("span",null,a(o(d)(x.balance)),1)])])]))),256))])),t("div",{class:V(["flex justify-between px-5 py-2",{"border-t":!o(v)}])},[t("span",ye,a(e.alias??e.name),1),t("div",ve,[t("span",xe,a(o(d)(e.income)),1),t("span",be,a(o(d)(e.outcome)),1),t("span",he,a(o(d)(e.total)),1)])],2)]))),128)),t("div",ge,[t("span",we," Total "+a(n.categoryType)+"s ",1),t("span",Se,a(o(d)(o(P))),1)])],2)])]),_:1},8,["title"])}}});const tt=X(Ce,[["__scopeId","data-v-480c6356"]]);export{tt as default};
