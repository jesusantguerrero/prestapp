import{_ as c}from"./ClientTemplate.vue_vue_type_script_setup_true_lang-5a9f26af.js";import{c as p}from"./clientInteractions-6c951eed.js";import{k as l,c as i,w as n,C as u,o as e,f as o,F as d,j as f,u as y,g as a}from"./app-b9464164.js";import"./AppLayout.vue_vue_type_script_setup_true_lang-0afa9cc5.js";import"./atmosphere-ui-8913659c.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0d2503f0.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-89aa5f09.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-a1e2938d.js";import"./PaymentGrid-88a06cd9.js";/* empty css                                                                    */import"./mathHelper-44355b55.js";import"./exact-math.node-e9867222.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-d3f16147.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-89ccd5ed.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-74361783.js";import"./usePaymentModal-23c75da0.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-3f2645eb.js";import"./close-45140fa9.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-d4d45920.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-b284cd8c.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-b788b25f.js";import"./AppSearchFilters-93f60e1e.js";import"./AppSectionHeader-611c7f77.js";import"./PropertySectionNav.vue_vue_type_script_setup_true_lang-e2241f15.js";import"./menus-2c7b33a0.js";import"./EmptyAddTool-4fec7d30.js";const b={key:0,class:"px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"},C=["onClick"],Z=l({__name:"Show",props:{clients:null,currentTab:{default:"summary"},outstanding:null,deposits:null,daysLate:null,type:null},setup(t){const m=t;return(k,h)=>{const s=u("InvoiceCard");return e(),i(c,{clients:t.clients,type:t.type,"current-tab":t.currentTab},{default:n(()=>[t.currentTab=="transactions"?(e(),o("article",b,[(e(!0),o(d,null,f(m.clients.invoices,r=>(e(),i(s,{invoice:r},{"header-actions":n(()=>[r.status!=="paid"?(e(),o("button",{key:0,class:"mr-2",onClick:_=>y(p).generateOwnerDistribution(t.clients.id,r.id)}," Re-generar ",8,C)):a("",!0)]),_:2},1032,["invoice"]))),256))])):a("",!0)]),_:1},8,["clients","type","current-tab"])}}});export{Z as default};
