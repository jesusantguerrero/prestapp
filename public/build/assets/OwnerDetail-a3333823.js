import{_ as d}from"./ClientTemplate.vue_vue_type_script_setup_true_lang-eaecb7fe.js";import{_ as u}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-ccb04f01.js";import{_ as f}from"./UnitTitle.vue_vue_type_script_setup_true_lang-ad071be3.js";import{C as y}from"./ClientProfile-45452668.js";import{_}from"./ClientForm.vue_vue_type_script_setup_true_lang-7ced5882.js";import{l as b,c as n,w as i,C as w,o as t,k as h,a as k,b as l,f as r,j as a,u as v,F as s}from"./app-494b6f90.js";import"./edit-c2d315cc.js";import"./atmosphere-ui-83e2a306.js";import"./AppLayout.vue_vue_type_style_index_0_lang-3aae0800.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-2fd658ef.js";import"./close-40de6817.js";import"./PaymentGrid-a2bc0bdb.js";import"./mathHelper-d6bc48cd.js";import"./exact-math.node-f7579f9b.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-93902e28.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-7b13fe11.js";import"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-ca2f799a.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-d4cb9813.js";import"./user-outline-c87acd59.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-593db1f8.js";import"./usePaymentModal-f96f741a.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-ff3d4599.js";import"./AppSectionHeader.vue_vue_type_script_setup_true_lang-5e6dc640.js";import"./EmptyAddTool-1f60e564.js";import"./PropertySectionNav.vue_vue_type_script_setup_true_lang-d424aeb5.js";import"./menus-f4817d1f.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-3cbc58a3.js";import"./LoanSectionNav.vue_vue_type_script_setup_true_lang-185d9b4d.js";import"./index-9dc2d84c.js";import"./index-3624ec38.js";import"./constants-a62dae4c.js";import"./chevron-right-182a2a80.js";const x={key:0,class:"px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"},C={key:1,class:"shadow-md rounded-md overflow-hidden"},T={class:"space-y-4 flex flex-col"},pt=b({__name:"OwnerDetail",props:{clients:null,currentTab:{default:"summary"},outstanding:null,deposits:null,daysLate:null,type:null,leases:null},setup(e){const c=e;return(m,g)=>{const p=w("EmptyAddTool");return t(),n(d,{clients:e.clients,type:e.type,"current-tab":e.currentTab,contract:m.contract,tabs:{"":"Detalles",transactions:"Transacciones"}},{options:i(()=>[h(m.$slots,"options",{},()=>[k("section",T,[l(p,null,{default:i(()=>[(t(!0),r(s,null,a(e.leases,o=>(t(),n(v(y),{name:o.client_name,type:"owner",id:o.client_id},null,8,["name","id"]))),256))]),_:1}),(t(!0),r(s,null,a(e.leases,o=>(t(),n(f,{"tenant-name":" ",class:"mt-4 hover:bg-white cursor-pointer px-4 py-2 bg-white rounded-md flex-col",title:o.address,"owner-name":o.client_name},null,8,["title","owner-name"]))),256))])])]),default:i(()=>[e.currentTab=="transactions"?(t(),r("article",x,[(t(!0),r(s,null,a(c.clients.invoices,o=>(t(),n(u,{invoice:o},null,8,["invoice"]))),256))])):(t(),r("article",C,[l(_,{"form-data":e.clients,disabled:!0,type:"owner"},null,8,["form-data"])]))]),_:3},8,["clients","type","current-tab","contract"])}}});export{pt as default};
