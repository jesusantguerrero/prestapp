import{o,f as r,a as t,k as d,c as p,w as _,t as n,j as h,b as i,h as u,u as l,F as f}from"./app-9b138141.js";import{_ as x}from"./LoanTemplate.vue_vue_type_script_setup_true_lang-fdc58bdc.js";import{f as b}from"./index-901d5032.js";import{f as g}from"./formatMoney-b7ef7683.js";import"./sharp-payment-a643f5bb.js";import"./atmosphere-ui-18006871.js";import"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";import"./AppSectionHeader-a328e89a.js";import"./LoanSectionNav-894e86bf.js";import"./menus-1fa29540.js";import"./AgreementFormModal.vue_vue_type_script_setup_true_lang-e92fa048.js";const L={viewBox:"0 0 24 24",width:"1.2em",height:"1.2em"},v=t("path",{fill:"currentColor",d:"m3 22l1.5-1.5L6 22l1.5-1.5L9 22l1.5-1.5L12 22l1.5-1.5L15 22l1.5-1.5L18 22l1.5-1.5L21 22V2l-1.5 1.5L18 2l-1.5 1.5L15 2l-1.5 1.5L12 2l-1.5 1.5L9 2L7.5 3.5L6 2L4.5 3.5L3 2"},null,-1),y=[v];function w(e,a){return o(),r("svg",L,y)}const $={name:"mdi-receipt",render:w},B={viewBox:"0 0 24 24",width:"1.2em",height:"1.2em"},V=t("path",{d:"M5 3h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zm2 4v2h10V7H7zm0 4v2h10v-2H7zm0 4v2h7v-2H7z",fill:"currentColor"},null,-1),k=[V];function z(e,a){return o(),r("svg",B,k)}const C={name:"mdi-document",render:z},H={class:"mt-12 px-4 overflow-hidden bg-white rounded-md shadow-md"},M={class:"mb-4"},D=t("h4",{class:"my-2 mb-0 font-bold text-secondary"},"Pagos de prestamo",-1),N={class:"text-body-1"},P={class:"text-sm flex text-body justify-between py-4 shadow-sm px-4 rounded-md border mb-2"},T={class:"flex space-x-3 items-center"},j={class:"font-bold text-primary"},E={class:"font-bold text-xl text-green-500 flex space-x-3 items-center"},F=["href"],I=t("p",null,"Recibo",-1),vt=d({__name:"Payments",props:{loans:null,currentTab:null,stats:null},setup(e){return(a,R)=>{const m=C,c=$;return o(),p(x,{loans:e.loans,"current-tab":e.currentTab,stats:e.stats},{default:_(()=>[t("section",H,[t("header",M,[D,t("small",N,"Este prestamo ha recibido "+n(e.loans.payment_documents.length)+" pagos",1)]),(o(!0),r(f,null,h(e.loans.payment_documents,s=>(o(),r("article",P,[t("section",T,[i(m,{class:"text-xl"}),t("div",null,[t("h5",null,n(s.concept),1),t("small",null,[u(" Pagado en "),t("span",j,n(l(b)(s.payment_date)),1)])])]),t("section",null,[t("p",E,[t("span",null,n(l(g)(s.amount)),1),t("a",{href:`/loans/${e.loans.id}/payments/${s.id}/print`,target:"_blank",rel:"noopener noreferrer",class:"text-secondary px-3 py-1 rounded-md border border-base-lvl-1 flex text-sm bg-base-lvl-2"},[i(c),I],8,F)])])]))),256))])]),_:1},8,["loans","current-tab","stats"])}}});export{vt as default};
