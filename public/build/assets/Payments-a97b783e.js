import{_ as c,a as p}from"./document-dbea43a6.js";import{_ as d}from"./LoanTemplate.vue_vue_type_script_setup_true_lang-edc2daae.js";import{f as _}from"./index-8305cd09.js";import{k as u,c as f,w as x,o as r,a as t,t as s,f as n,j as b,b as a,h,u as m,F as y}from"./app-bdc0a90d.js";import{f as g}from"./formatMoney-b7ef7683.js";import"./sharp-payment-c2380fd2.js";import"./atmosphere-ui-92e9addd.js";import"./AppLayout.vue_vue_type_script_setup_true_lang-2eda9632.js";import"./AppLayout.vue_vue_type_style_index_0_lang-933d19bf.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-fcb67ff4.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-667d0b29.js";import"./PaymentGrid-0bd36832.js";/* empty css                                                                    */import"./mathHelper-db6605d5.js";import"./exact-math.node-0390b5bc.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-6ae29dfc.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-f40618a6.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-b383de19.js";import"./usePaymentModal-b60af107.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-b2380174.js";import"./close-0030bbab.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-723236b8.js";import"./clientInteractions-f0f2fe2b.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-18ff5984.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-543932f6.js";import"./AppSearchFilters-f0af399e.js";import"./AppSectionHeader-02c12398.js";import"./LoanSectionNav-3940fab1.js";import"./menus-2c7b33a0.js";import"./AgreementFormModal.vue_vue_type_script_setup_true_lang-6820e33d.js";const w={class:"mt-12 px-4 overflow-hidden bg-white rounded-md shadow-md"},k={class:"mb-4"},v=t("h4",{class:"my-2 mb-0 font-bold text-secondary"},"Pagos de prestamo",-1),B={class:"text-body-1"},D={class:"text-sm flex text-body justify-between py-4 shadow-sm px-4 rounded-md border mb-2"},M={class:"flex space-x-3 items-center"},N={class:"font-bold text-primary"},P={class:"font-bold text-xl text-green-500 flex space-x-3 items-center"},T=["href"],V=t("p",null,"Recibo",-1),ut=u({__name:"Payments",props:{loans:null,currentTab:null,stats:null},setup(o){return($,j)=>{const i=c,l=p;return r(),f(d,{loans:o.loans,"current-tab":o.currentTab,stats:o.stats},{default:x(()=>[t("section",w,[t("header",k,[v,t("small",B,"Este prestamo ha recibido "+s(o.loans.payment_documents.length)+" pagos",1)]),(r(!0),n(y,null,b(o.loans.payment_documents,e=>(r(),n("article",D,[t("section",M,[a(i,{class:"text-xl"}),t("div",null,[t("h5",null,s(e.concept),1),t("small",null,[h(" Pagado en "),t("span",N,s(m(_)(e.payment_date)),1)])])]),t("section",null,[t("p",P,[t("span",null,s(m(g)(e.amount)),1),t("a",{href:`/loans/${o.loans.id}/payments/${e.id}/print`,target:"_blank",rel:"noopener noreferrer",class:"text-secondary px-3 py-1 rounded-md border border-base-lvl-1 flex text-sm bg-base-lvl-2"},[a(l),V],8,T)])])]))),256))])]),_:1},8,["loans","current-tab","stats"])}}});export{ut as default};
