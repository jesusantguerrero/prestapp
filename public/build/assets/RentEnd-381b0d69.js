import{l as T,P as k,c as l,w as n,o as r,a as t,b as a,t as u,u as o,f as c,j as g,F as x,g as p,h as i,a0 as C,aa as F}from"./app-e7293397.js";import{k as f,F as d}from"./atmosphere-ui-86b7f8ed.js";import{_ as B}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{_ as y}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-26337e5b.js";import{_ as R}from"./ClientTemplate.vue_vue_type_script_setup_true_lang-782a164e.js";import"./index-9dc2d84c.js";import"./formatMoney-b7ef7683.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./index-3624ec38.js";import"./constants-7016751c.js";import"./usePaymentModal-ad844d5c.js";import"./edit-3340e0a9.js";import"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./AppSectionHeader.vue_vue_type_script_setup_true_lang-4f8015fe.js";import"./EmptyAddTool-be692e14.js";import"./PropertySectionNav.vue_vue_type_script_setup_true_lang-516e104c.js";import"./menus-f4817d1f.js";import"./LoanSectionNav.vue_vue_type_script_setup_true_lang-6869f5ef.js";const w={class:"w-full px-4 py-2 space-y-4 rounded-md shadow-md bg-base-lvl-3"},N=t("header",null,[t("h4",null,"Terminar Contrato")],-1),V={class:"grid grid-cols-3"},P=t("h4",{class:"mb-4 text-lg font-bold"},"Facturas Pendientes",-1),D={class:"space-y-2"},E={key:0,class:"text-body-1"},S={class:"text-end"},I=t("h4",{class:"mb-4 text-lg font-bold"},"Retornar Depositos",-1),$={key:0,class:"text-body-1"},j={class:"text-end"},z={class:"flex justify-between"},A={class:"flex space-x-2"},kt=T({__name:"RentEnd",props:{clients:null,currentTab:{default:"summary"},rent:null,property:null,pendingInvoices:null,depositsToReturn:null,contract:null},setup(e){const _=e,m=k({move_out_at:new Date,move_out_notice:""}),v=()=>{F.alert("¿Seguro que desea terminar este contrato?","Terminar Contrato",{confirmButtonText:"Si, Terminar Contraro",cancelButtonText:"Cancelar",showCancelButton:!0,callback:h=>{h==="confirm"&&m.put(route("tenant.end-rent-action",{client:_.clients,rent:_.rent}))}})};return(h,b)=>(r(),l(R,{clients:e.clients,contract:e.contract,"hide-statistics":""},{default:n(()=>[t("main",w,[N,t("section",V,[a(o(f),{label:"Propiedad"},{default:n(()=>[t("p",null,u(e.property.name),1)]),_:1}),a(o(f),{label:"Tipo de Propiedad"},{default:n(()=>[t("p",null,u(e.property.property_type),1)]),_:1}),a(o(f),{label:"Fecha de inicio"},{default:n(()=>[t("p",null,u(e.rent.date),1)]),_:1})]),t("section",null,[P,t("section",D,[(r(!0),c(x,null,g(e.pendingInvoices,s=>(r(),l(y,{invoice:s},null,8,["invoice"]))),256)),e.pendingInvoices.length?p("",!0):(r(),c("span",E," No hay facturas pendientes "))]),t("p",S,[a(o(d),{class:"font-bold text-success"},{default:n(()=>[i(" Agregar Factura ")]),_:1})])]),t("section",null,[I,t("section",null,[(r(!0),c(x,null,g(e.depositsToReturn,s=>(r(),l(y,{invoice:s},null,8,["invoice"]))),256)),e.depositsToReturn.length?p("",!0):(r(),c("span",$," No hay depositos pendientes "))]),t("p",j,[e.depositsToReturn.length?(r(),l(o(d),{key:0,class:"font-bold text-success"},{default:n(()=>[i("Retornar deposito")]),_:1})):p("",!0)]),a(o(d),null,{default:n(()=>[i("Agregar Nota")]),_:1})]),t("footer",z,[a(o(d),{class:"font-bold transition border text-body-1 hover:text-error hover:border-error",rounded:""},{default:n(()=>[i(" Cancelar ")]),_:1}),t("section",A,[a(o(C),{modelValue:o(m).move_out_at,"onUpdate:modelValue":b[0]||(b[0]=s=>o(m).move_out_at=s),size:"large"},null,8,["modelValue"]),a(B,{variant:"error",onClick:v},{default:n(()=>[i(" Finalizar Contrato ")]),_:1})])])])]),_:1},8,["clients","contract"]))}});export{kt as default};