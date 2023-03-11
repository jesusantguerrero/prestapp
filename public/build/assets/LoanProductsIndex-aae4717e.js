import{Q as f,ac as _,l as x,d as h,r as b,c as v,w as t,o as y,b as a,h as l,a as m,u as s,y as w,t as c}from"./app-494b6f90.js";import{_ as C}from"./AppLayout.vue_vue_type_style_index_0_lang-3aae0800.js";import{_ as g}from"./AtTable.vue_vue_type_script_setup_true_lang-3e9914f8.js";import{_ as $}from"./LoanSectionNav.vue_vue_type_script_setup_true_lang-185d9b4d.js";import{_ as N}from"./LoanProductModal.vue_vue_type_script_setup_true_lang-1c702660.js";import{_ as p}from"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import{f as d}from"./formatMoney-b7ef7683.js";import"./atmosphere-ui-83e2a306.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-2fd658ef.js";import"./close-40de6817.js";import"./PaymentGrid-a2bc0bdb.js";import"./mathHelper-d6bc48cd.js";import"./exact-math.node-f7579f9b.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-93902e28.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-7b13fe11.js";import"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-ca2f799a.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-d4cb9813.js";import"./user-outline-c87acd59.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-593db1f8.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-7ced5882.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-3cbc58a3.js";import"./usePaymentModal-f96f741a.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-ff3d4599.js";import"./customCell-9e9ed16f.js";import"./menus-f4817d1f.js";import"./FormSection.vue_vue_type_script_setup_true_lang-e638d4a6.js";import"./TaxTypeSelector-109e1660.js";const P=[{name:"name",class:"text-center",headerClass:"text-center",label:"Nombre"},{name:"client",label:"Terminos",class:"text-center",headerClass:"text-center",render(o){return o.frequency}},{name:"interest_rates",label:"Tasas de Interés",class:"text-center",headerClass:"text-center",render(o){return o.interest_rates.map(r=>r.trim()).join("%, ")+"%"}},{name:"status",label:"Estado",class:"text-center",headerClass:"text-center",render(o){return f(_,{type:"success"},"Activo")}},{name:"actions",label:"Acciones",class:"text-right",headerClass:"text-center"}],T={class:"pt-16"},k={class:"flex space-x-4 mx-auto"},A={class:"font-bold"},B={class:"font-bold text-green-500"},ct=x({__name:"LoanProductsIndex",props:{loanProducts:null},setup(o){const r=o,u=h(()=>Array.isArray(r.loanProducts)?r.loanProducts:r.loanProducts.data),n=b(!1);return(D,i)=>(y(),v(C,{title:"Tipo de prestamos"},{header:t(()=>[a($,null,{actions:t(()=>[a(p,{variant:"inverse",onClick:i[0]||(i[0]=e=>n.value=!n.value)},{default:t(()=>[l(" Nuevo tipo ")]),_:1})]),_:1})]),default:t(()=>[m("main",T,[a(g,{"table-data":s(u),cols:s(P),class:"bg-white rounded-md text-body-1"},{actions:t(({scope:{row:e}})=>[m("div",k,[a(s(w),{class:"relative inline-block px-5 py-2 overflow-hidden font-bold text-white transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary",href:`/loans/${e.id}`},{default:t(()=>[l(" Edit")]),_:2},1032,["href"]),a(p,null,{default:t(()=>[l(" Delete ")]),_:1})])]),amount:t(({scope:{row:e}})=>[m("div",A,[l(c(s(d)(e.amount))+" ",1),m("p",B,c(s(d)(e.total)),1)])]),_:1},8,["table-data","cols"]),a(N,{show:n.value,"onUpdate:show":i[1]||(i[1]=e=>n.value=e)},null,8,["show"])])]),_:1}))}});export{ct as default};