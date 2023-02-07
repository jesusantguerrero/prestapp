import{_ as A}from"./AppLayout.vue_vue_type_script_setup_true_lang-5b8fe57e.js";import{a as N}from"./AppLayout.vue_vue_type_style_index_0_lang-1b19aa61.js";import{_ as B}from"./ClientsTable.vue_vue_type_script_setup_true_lang-f2d49ad7.js";import{k as O,d as u,B as P,c as n,w as r,u as t,o as s,b as c,h as p,E as d,g as T,t as V,a as _}from"./app-de9d56f0.js";import b from"./LoanSectionNav-be6b3382.js";import{_ as x}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-19b23e2c.js";import{_ as m}from"./AppButton.vue_vue_type_script_setup_true_lang-81087974.js";import{u as z}from"./useServerSearch-20d48c29.js";import"./atmosphere-ui-55a1c253.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-e6300ed2.js";import"./PaymentGrid-aa03ae3b.js";/* empty css                                                                    */import"./mathHelper-c551ea9f.js";import"./exact-math.node-4d03343f.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-a83a84cb.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-7415b2b5.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-26f85fd9.js";import"./usePaymentModal-6968ac37.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-ca470652.js";import"./close-69e3a4b1.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-8a48d595.js";import"./clientInteractions-a56251a7.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-de720a79.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-46ceeab7.js";import"./AppSearchFilters-d4c59cd5.js";import"./BaseTable.vue_vue_type_style_index_0_lang-b5975dc2.js";import"./customCell-936c7b84.js";import"./constants-ca1255a1.js";import"./IconMarker-92fd0c89.js";import"./ClientCard.vue_vue_type_script_setup_true_lang-d710f240.js";import"./menus-942163d8.js";import"./index-8305cd09.js";const D={class:"mt-16 bg-white rounded-md"},St=O({__name:"Index",props:{clients:null,type:null,serverSearchOptions:{default:()=>({filters:{},dates:{},sorts:"",limit:10,relationships:"",search:"",page:1})}},setup(o){const i=o,v=u(()=>Array.isArray(i.clients)?i.clients:i.clients.data),y=u(()=>!Array.isArray(i.clients)&&i.clients.total),{toggleModal:f}=N("contact"),g=u(()=>({owner:"Dueños de propiedades",tenant:"Inquilinos",lender:"Clientes de prestamos"})[i.type]??"Clientes"),{serverSearchOptions:k}=P(i),{executeSearch:C,updateSearch:S,changeSize:$,paginate:w,state:h}=z(k,a=>{S(`/contacts/${i.type}?${a}`)},{manual:!0});return(a,e)=>(s(),n(A,{title:t(g)},{header:r(()=>[o.type=="lender"?(s(),n(b,{key:0},{actions:r(()=>[c(m,{variant:"inverse-secondary",onClick:e[0]||(e[0]=l=>t(d).visit("/loans/create"))},{default:r(()=>[p(" Nuevo prestamo ")]),_:1}),c(m,{variant:"secondary",onClick:e[1]||(e[1]=l=>t(f)({data:{type:o.type},isOpen:!0}))},{default:r(()=>[p(" Nuevo cliente ")]),_:1})]),_:1})):(s(),n(x,{key:1},{actions:r(()=>[o.type=="owner"?(s(),n(m,{key:0,variant:"inverse",onClick:e[2]||(e[2]=l=>t(d).visit(a.route("properties.create")))},{default:r(()=>[p(" Agregar Propiedad ")]),_:1})):T("",!0),o.type=="owner"?(s(),n(m,{key:1,variant:"inverse",onClick:e[3]||(e[3]=l=>t(d).visit(a.route("owners.draw")))},{default:r(()=>[p(" Pagar distribucion ")]),_:1})):(s(),n(m,{key:2,variant:"inverse-secondary",onClick:e[4]||(e[4]=l=>t(d).visit(a.route("properties.create")))},{default:r(()=>[p(" Agregar Contrato ")]),_:1})),c(m,{variant:"secondary",onClick:e[5]||(e[5]=l=>t(f)({data:{type:o.type},isOpen:!0}))},{default:r(()=>[p(" Agregar "+V(o.type),1)]),_:1})]),_:1}))]),default:r(()=>[_("main",D,[c(B,{clients:t(v),pagination:t(h),total:t(y),onSearch:t(C),onPaginate:t(w),onSizeChange:t($)},null,8,["clients","pagination","total","onSearch","onPaginate","onSizeChange"])])]),_:1},8,["title"]))}});export{St as default};