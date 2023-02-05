import{N as c}from"./atmosphere-ui-18006871.js";import{z as v,d as x,l as N,r as V,c as _,w as o,o as f,b as i,a as $,t as k,u as a,E as n,h as l,g as C}from"./app-9b138141.js";import{_ as g}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{_ as I}from"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import{_ as b}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as w}from"./InvoiceTable.vue_vue_type_script_setup_true_lang-42a3587e.js";import S from"./AccountingSectionNav-8ff36470.js";import{_ as B}from"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-69eeb123.js";import"./formatMoney-b7ef7683.js";import{_ as T}from"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";import"./constants-3d7b0ca2.js";import"./index-901d5032.js";import"./menus-1fa29540.js";const E={class:"py-10 mx-auto sm:px-6 lg:px-8"},F={__name:"Index",props:{invoices:{type:Array},type:{type:String}},setup(m){const y=m,p=v({sectionName:x(()=>`${y.type.toLowerCase()}s`)}),s=v({client_id:null});N(()=>s,()=>{const d=Object.entries(s).reduce((e,[t,u])=>(e[t]=u==null?void 0:u.id,e),{});n.get(location.pathname,{filters:d},{preserveState:!0})},{deep:!0});const r=V(!1);return(d,e)=>(f(),_(b,{title:"Transaccciones"},{header:o(()=>[i(S,null,{actions:o(()=>[$("p",null,"Total: "+k(m.invoices.data.length),1),i(a(c),{onClick:e[0]||(e[0]=t=>a(n).visit(`/${p.sectionName}/create`)),variant:"inverse"},{default:o(()=>[l("Imprimir")]),_:1}),i(a(c),{onClick:e[1]||(e[1]=t=>a(n).visit(`/${p.sectionName}/create`)),variant:"inverse"},{default:o(()=>[l("Filtros")]),_:1}),i(I,{modelValue:s.client_id,"onUpdate:modelValue":e[2]||(e[2]=t=>s.client_id=t),"track-by":"id",endpoint:"/api/clients",placeholder:"selecciona un cliente",label:"display_name"},null,8,["modelValue"]),i(g,{onClick:e[3]||(e[3]=t=>a(n).visit(`/${p.sectionName}/create`)),variant:"inverse"},{default:o(()=>[l("Ingreso")]),_:1}),i(g,{onClick:e[4]||(e[4]=t=>r.value=!r.value),variant:"inverse"},{default:o(()=>[l(" Egreso ")]),_:1})]),_:1})]),default:o(()=>[$("div",E,[i(a(w),{"invoice-data":m.invoices.data,class:"mt-10 bg-base-lvl-3"},null,8,["invoice-data"])]),r.value?(f(),_(B,{key:0,title:"Registar gasto de propiedad",modelValue:r.value,"onUpdate:modelValue":e[5]||(e[5]=t=>r.value=t),onSaved:e[6]||(e[6]=t=>d.$router.reload())},null,8,["modelValue"])):C("",!0)]),_:1}))}},ue=T(F,[["__scopeId","data-v-4453857a"]]);export{ue as default};
