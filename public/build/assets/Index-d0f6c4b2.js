import{_ as A}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import{a as N}from"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as B}from"./ClientsTable.vue_vue_type_script_setup_true_lang-0dbe5a51.js";import{k as O,d as u,B as P,c as n,w as r,u as t,o as s,b as c,h as p,E as d,g as T,t as V,a as _}from"./app-9b138141.js";import b from"./LoanSectionNav-894e86bf.js";import{_ as x}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-d8996414.js";import{_ as m}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{u as z}from"./useServerSearch-3cc40805.js";import"./atmosphere-ui-18006871.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";import"./BaseTable.vue_vue_type_style_index_0_lang-8bddfc90.js";import"./customCell-c06172d5.js";import"./constants-ca1255a1.js";import"./IconMarker-87d7c953.js";import"./menus-1fa29540.js";import"./index-901d5032.js";const D={class:"mt-16 bg-white rounded-md"},Ct=O({__name:"Index",props:{clients:null,type:null,serverSearchOptions:{default:()=>({filters:{},dates:{},sorts:"",limit:10,relationships:"",search:"",page:1})}},setup(o){const i=o,v=u(()=>Array.isArray(i.clients)?i.clients:i.clients.data),y=u(()=>!Array.isArray(i.clients)&&i.clients.total),{toggleModal:f}=N("contact"),g=u(()=>({owner:"Dueños de propiedades",tenant:"Inquilinos",lender:"Clientes de prestamos"})[i.type]??"Clientes"),{serverSearchOptions:k}=P(i),{executeSearch:C,updateSearch:S,changeSize:$,paginate:w,state:h}=z(k,a=>{S(`/contacts/${i.type}?${a}`)},{manual:!0});return(a,e)=>(s(),n(A,{title:t(g)},{header:r(()=>[o.type=="lender"?(s(),n(b,{key:0},{actions:r(()=>[c(m,{variant:"inverse-secondary",onClick:e[0]||(e[0]=l=>t(d).visit("/loans/create"))},{default:r(()=>[p(" Nuevo prestamo ")]),_:1}),c(m,{variant:"secondary",onClick:e[1]||(e[1]=l=>t(f)({data:{type:o.type},isOpen:!0}))},{default:r(()=>[p(" Nuevo cliente ")]),_:1})]),_:1})):(s(),n(x,{key:1},{actions:r(()=>[o.type=="owner"?(s(),n(m,{key:0,variant:"inverse",onClick:e[2]||(e[2]=l=>t(d).visit(a.route("properties.create")))},{default:r(()=>[p(" Agregar Propiedad ")]),_:1})):T("",!0),o.type=="owner"?(s(),n(m,{key:1,variant:"inverse",onClick:e[3]||(e[3]=l=>t(d).visit(a.route("owners.draw")))},{default:r(()=>[p(" Pagar distribucion ")]),_:1})):(s(),n(m,{key:2,variant:"inverse-secondary",onClick:e[4]||(e[4]=l=>t(d).visit(a.route("properties.create")))},{default:r(()=>[p(" Agregar Contrato ")]),_:1})),c(m,{variant:"secondary",onClick:e[5]||(e[5]=l=>t(f)({data:{type:o.type},isOpen:!0}))},{default:r(()=>[p(" Agregar "+V(o.type),1)]),_:1})]),_:1}))]),default:r(()=>[_("main",D,[c(B,{clients:t(v),pagination:t(h),total:t(y),onSearch:t(C),onPaginate:t(w),onSizeChange:t($)},null,8,["clients","pagination","total","onSearch","onPaginate","onSizeChange"])])]),_:1},8,["title"]))}});export{Ct as default};
