import{k as D,P as E,r as h,l as g,d as N,c as R,w as s,C as U,o as P,b as o,a as r,u as i,t as S,h as y,H as u,M as T}from"./app-fef21123.js";import{R as x,V as F}from"./atmosphere-ui-43ec926d.js";import{_ as V}from"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */import{_ as G}from"./BaseTable.vue_vue_type_style_index_0_lang-6dbbce48.js";import{_ as H}from"./AppLayout.vue_vue_type_script_setup_true_lang-dc441d96.js";import"./AppLayout.vue_vue_type_style_index_0_lang-cff2a039.js";import{_ as I}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-f16b6b1a.js";import{_ as C}from"./AppButton.vue_vue_type_script_setup_true_lang-78bec775.js";import"./customCell-775a8e84.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-7eed6d28.js";import"./close-ce456e3e.js";import"./AppSearchFilters-54a857cd.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-3ea39cd1.js";import"./PaymentGrid-926a1204.js";/* empty css                                                                    */import"./mathHelper-02ca0ff8.js";import"./exact-math.node-8398c915.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-f3e358be.js";import"./formatMoney-b7ef7683.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import"./usePaymentModal-a07a875e.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-396b1c58.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-5401ee99.js";import"./clientInteractions-23fb4c77.js";import"./menus-be25f72f.js";const j={class:"py-10 mx-auto sm:px-6 lg:px-8"},q={class:"rounded-md bg-base-lvl-3"},z={class:"flex space-x-4 w-full px-4"},A={class:"px-4 pb-10"},J={class:"items-center space-x-2 d-flex"},K={class:"w-full text-right space-x-4 pb-4"},ve=D({__name:"DepositRefund",props:{category:null},setup(v){const $=v,t=E({client:null,account:null,payments:[]}),d=h([]);g(()=>t.client,async()=>{d.value=await k(t.client.id,$.category),t.account=d.value.length?d.value[0]:null});function k(n,a){return u.get(`/categories/${a}/clients/${n}/balance?exclude_credits=true`).then(({data:l})=>l)}const _=h([]),b=N(()=>_.value.reduce((n,a)=>(console.log(a),a!=null&&a.transactionable&&n.push(...a.transactionable.payments.map(l=>({...l,rent_id:a.transactionable.invoiceable_id,client:t.client,balance:t.account.balance,payment:0}))),n),[])),w=(n,a)=>u.get("/api/transaction-lines",{params:{limit:10,page:1,filter:{account_id:a,payee_id:n},relationships:"transaction.transactionable.payments"}}).then(({data:l})=>l.data.map(e=>e.transaction));g(()=>t.account,async()=>{_.value=await w(t.client.id,t.account.id)});const B=()=>{},M=()=>{var e,m,f;const n=(e=b.value)==null?void 0:e.reduce((p,c)=>(c.amount&&p.push({id:c.id,rent_id:c.rent_id,amount:c.payment,original_amount:c.amount}),p),[]);if(!n.length){T({type:"error",message:"Seleccione al menos un pago",title:"Error de pago"});return}const a=n[0].rent_id,l={client_id:(m=t.client)==null?void 0:m.id,account_id:(f=t.account)==null?void 0:f.id,rent_id:a,payments:n,total:n.reduce((p,c)=>p+parseFloat(c.amount),0)};u.post(`/properties/${a}/transactions/refund`,l).then(({data:p})=>{console.log(p)})};return(n,a)=>{const l=U("ElCheckbox");return P(),R(H,{title:"Rembolso de depositos"},{header:s(()=>[o(I)]),default:s(()=>[r("main",j,[r("section",q,[r("header",z,[o(i(x),{label:"Cliente",class:"w-full"},{default:s(()=>[o(V,{modelValue:i(t).client,"onUpdate:modelValue":a[0]||(a[0]=e=>i(t).client=e),endpoint:"/api/clients",placeholder:"selecciona un cliente",label:"display_name","track-by":"id"},null,8,["modelValue"])]),_:1}),o(i(x),{label:"Categoria",class:"w-full"},{default:s(()=>[o(V,{modelValue:i(t).account,"onUpdate:modelValue":a[1]||(a[1]=e=>i(t).account=e),"track-by":n.id,options:d.value,"hide-selected":!1,"custom-label":e=>`${e.name} (Balance: $${Math.abs(e.balance)})`,placeholder:"selecciona una categoria"},null,8,["modelValue","track-by","options","custom-label"])]),_:1})]),r("article",A,[o(G,{cols:[{name:"item",label:""},{name:"id",label:"Pago #"},{name:"client.display_name",label:"Cliente"},{name:"method_name",label:"Metodo de pago"},{name:"balance",label:"Balance",render(e){return Math.abs(e.balance)}},{name:"payment",label:"Monto de reembolso"}],"table-data":i(b)},{item:s(({scope:{row:e}})=>[r("div",J,[o(l,{onChange:m=>B(m,e)},null,8,["onChange"]),r("span",null,S(e.name),1)])]),payment:s(({scope:{row:e}})=>[o(i(F),{class:"rounded-md shadow-none border-body-1/10",modelValue:e.payment,"onUpdate:modelValue":m=>e.payment=m,"number-format":!0},null,8,["modelValue","onUpdate:modelValue"])]),_:1},8,["cols","table-data"])]),r("footer",K,[o(C,null,{default:s(()=>[y(" Cancel")]),_:1}),o(C,{onClick:M},{default:s(()=>[y(" Guardar y Pagar")]),_:1})])])])]),_:1})}}});export{ve as default};
