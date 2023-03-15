import{_ as d}from"./ClientTemplate.vue_vue_type_script_setup_true_lang-782a164e.js";import{_ as u}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-26337e5b.js";import{_ as f}from"./UnitTitle.vue_vue_type_script_setup_true_lang-c25f773c.js";import{C as y}from"./ClientProfile-1c1716f3.js";import{_}from"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import{l as b,c as n,w as i,C as w,o as t,k as h,a as k,b as l,f as r,j as a,u as v,F as s}from"./app-e7293397.js";import"./edit-3340e0a9.js";import"./atmosphere-ui-86b7f8ed.js";import"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./AppSectionHeader.vue_vue_type_script_setup_true_lang-4f8015fe.js";import"./EmptyAddTool-be692e14.js";import"./PropertySectionNav.vue_vue_type_script_setup_true_lang-516e104c.js";import"./menus-f4817d1f.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./LoanSectionNav.vue_vue_type_script_setup_true_lang-6869f5ef.js";import"./index-9dc2d84c.js";import"./index-3624ec38.js";import"./constants-7016751c.js";import"./chevron-right-5280f715.js";import"./constants-22f9a1fe.js";const x={key:0,class:"px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"},C={key:1,class:"shadow-md rounded-md overflow-hidden"},T={class:"space-y-4 flex flex-col"},dt=b({__name:"OwnerDetail",props:{clients:null,currentTab:{default:"summary"},outstanding:null,deposits:null,daysLate:null,type:null,leases:null},setup(e){const c=e;return(m,g)=>{const p=w("EmptyAddTool");return t(),n(d,{clients:e.clients,type:e.type,"current-tab":e.currentTab,contract:m.contract,tabs:{"":"Detalles",transactions:"Transacciones"}},{options:i(()=>[h(m.$slots,"options",{},()=>[k("section",T,[l(p,null,{default:i(()=>[(t(!0),r(s,null,a(e.leases,o=>(t(),n(v(y),{name:o.client_name,type:"owner",id:o.client_id},null,8,["name","id"]))),256))]),_:1}),(t(!0),r(s,null,a(e.leases,o=>(t(),n(f,{"tenant-name":" ",class:"mt-4 hover:bg-white cursor-pointer px-4 py-2 bg-white rounded-md flex-col",title:o.address,"owner-name":o.client_name},null,8,["title","owner-name"]))),256))])])]),default:i(()=>[e.currentTab=="transactions"?(t(),r("article",x,[(t(!0),r(s,null,a(c.clients.invoices,o=>(t(),n(u,{invoice:o},null,8,["invoice"]))),256))])):(t(),r("article",C,[l(_,{"form-data":e.clients,disabled:!0,type:"owner"},null,8,["form-data"])]))]),_:3},8,["clients","type","current-tab","contract"])}}});export{dt as default};