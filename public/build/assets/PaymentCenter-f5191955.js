import{z as C,l as $,k,P as v,c as w,w as r,C as E,o as F,b as a,a as d,u as s,t as P,h,E as B,M,H as N}from"./app-9b138141.js";import{R as y,V as S}from"./atmosphere-ui-18006871.js";import{_ as x}from"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import{_ as U}from"./BaseTable.vue_vue_type_style_index_0_lang-8bddfc90.js";import{_ as D}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import R from"./LoanSectionNav-894e86bf.js";import{_ as V}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{f as g}from"./formatMoney-b7ef7683.js";import"./customCell-c06172d5.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./close-f797118c.js";import"./AppSearchFilters-f097730f.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./menus-1fa29540.js";const j=o=>o.reduce((n,t)=>(n[(t==null?void 0:t.name)??t]=null,n),{}),z=(o,n)=>Object.entries(o).reduce((t,[p,i])=>(i&&(t[p]=i[n]),t),{}),G=(o,n,t="id")=>{const p=C(j(o));return $(()=>p,i=>{const l=z(i,t);console.log(l,i),n.get(location.pathname,{filters:l},{preserveState:!0})},{deep:!0}),p},H={class:"py-10 mx-auto sm:px-6 lg:px-8"},I={class:"rounded-md bg-base-lvl-3"},J={class:"flex space-x-4 w-full px-4"},O={class:"px-4 pb-10"},T={class:"items-center space-x-2 d-flex"},q={class:"w-full text-right space-x-4 pb-4"},$e=k({__name:"PaymentCenter",props:{invoices:null,loans:null,clients:null},setup(o){const n=v({client:null,account:null,payments:[]}),t=G(["client","loan"],B),p=()=>{},i=()=>{var e,u,f;const l=(e=payments.value)==null?void 0:e.reduce((_,c)=>(c.amount&&_.push({id:c.id,rent_id:c.rent_id,amount:c.payment,original_amount:c.amount}),_),[]);if(!l.length){M({type:"error",message:"Seleccione al menos un pago",title:"Error de pago"});return}const m=l[0].rent_id,b={client_id:(u=n.client)==null?void 0:u.id,account_id:(f=n.account)==null?void 0:f.id,rent_id:m,payments:l,total:l.reduce((_,c)=>_+parseFloat(c.amount),0)};N.post(`/properties/${m}/transactions/refund`,b).then(({data:_})=>{console.log(_)})};return(l,m)=>{const b=E("ElCheckbox");return F(),w(D,{title:"Centro de pago de prestamos"},{header:r(()=>[a(R)]),default:r(()=>[d("main",H,[d("section",I,[d("header",J,[a(s(y),{label:"Cliente",class:"w-full"},{default:r(()=>[a(x,{modelValue:s(t).client,"onUpdate:modelValue":m[0]||(m[0]=e=>s(t).client=e),options:o.clients,placeholder:"selecciona un cliente",label:"display_name","track-by":"id"},null,8,["modelValue","options"])]),_:1}),a(s(y),{label:"Categoria",class:"w-full"},{default:r(()=>[a(x,{modelValue:s(t).loan,"onUpdate:modelValue":m[1]||(m[1]=e=>s(t).loan=e),"track-by":l.id,options:o.loans,"hide-selected":!1,"custom-label":e=>`Prestamo ${e.id} (${e.amount}) (debt: $${e.debt})`,placeholder:"selecciona una categoria"},null,8,["modelValue","track-by","options","custom-label"])]),_:1})]),d("article",O,[a(U,{cols:[{name:"item",label:""},{name:"loan_id",label:"Prestamo #"},{name:"client.display_name",label:"Cliente"},{name:"amount",label:"Monto del pagare",render(e){return s(g)(e.amount)}},{name:"amount_paid",label:"Balance",render(e){return s(g)(e.amount_paid-e.amount)}},{name:"payment",label:"Monto de reembolso"}],"table-data":o.invoices.data},{item:r(({scope:{row:e}})=>[d("div",T,[a(b,{onChange:u=>p(u,e)},null,8,["onChange"]),d("span",null,P(e.name),1)])]),payment:r(({scope:{row:e}})=>[a(s(S),{class:"rounded-md shadow-none border-body-1/10",modelValue:e.payment,"onUpdate:modelValue":u=>e.payment=u,"number-format":!0},null,8,["modelValue","onUpdate:modelValue"])]),_:1},8,["cols","table-data"])]),d("footer",q,[a(V,null,{default:r(()=>[h(" Cancel")]),_:1}),a(V,{onClick:i},{default:r(()=>[h(" Guardar y Pagar")]),_:1})])])])]),_:1})}}});export{$e as default};
