import{_ as a}from"./ClientTemplate.vue_vue_type_script_setup_true_lang-782a164e.js";import{c as s}from"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import{_ as p}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-26337e5b.js";import{l as c,c as o,w as i,o as r,f as m,F as l,j as u,a as d,u as f,g as b}from"./app-e7293397.js";import"./edit-3340e0a9.js";import"./atmosphere-ui-86b7f8ed.js";import"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./AppSectionHeader.vue_vue_type_script_setup_true_lang-4f8015fe.js";import"./EmptyAddTool-be692e14.js";import"./PropertySectionNav.vue_vue_type_script_setup_true_lang-516e104c.js";import"./menus-f4817d1f.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./LoanSectionNav.vue_vue_type_script_setup_true_lang-6869f5ef.js";import"./constants-22f9a1fe.js";import"./index-9dc2d84c.js";import"./index-3624ec38.js";import"./constants-7016751c.js";const y={key:0,class:"px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"},_=["onClick"],it=c({__name:"Show",props:{clients:null,currentTab:{default:"summary"},outstanding:null,deposits:null,daysLate:null,type:null},setup(t){const n=t;return(h,g)=>(r(),o(a,{clients:t.clients,type:t.type,"current-tab":t.currentTab},{default:i(()=>[t.currentTab=="transactions"?(r(),m("article",y,[(r(!0),m(l,null,u(n.clients.invoices,e=>(r(),o(p,{invoice:e},{"header-actions":i(()=>[d("button",{class:"mr-2",onClick:k=>f(s).generateOwnerDistribution(t.clients.id,e.id)}," Re-generar ",8,_)]),_:2},1032,["invoice"]))),256))])):b("",!0)]),_:1},8,["clients","type","current-tab"]))}});export{it as default};
