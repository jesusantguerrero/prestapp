import{r as m,c as p,w as a,o as t,b as f,a as i,f as o,j as g,u as b,y as _,t as h,g as v,F as y,E as x}from"./app-9b138141.js";import{_ as k}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as w}from"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./atmosphere-ui-18006871.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";const C={class:"w-full h-auto py-12 mx-auto sm:px-6 lg:px-8"},N={class:"text-left section_container"},P=["href"],V=i("i",{class:"fa fa-chevron-right"},null,-1),B=[V],E={key:1,class:"mb-4"},oe={__name:"Index",props:{tabName:{type:String,default:"business"}},setup(c){const u=c,d=n=>{x.replace(`/settings/tab/${n}`)},s=m("business"),r=m({business:{label:"Organización",sections:[["Datos de Compañia","/settings/business"],["Facturas","/settings/invoice"],["Prestamos","/settings/loan"],["Propiedades","/settings/invoice"],"",["Region","/settings/region"]]},payments:{label:"Pagos",sections:[["Modal de pago","/settings/payment"]]},notifications:{label:"Notifications",sections:[["Notificationes en app",{name:"business"}],["Correo Electronico",{name:"business"}],"",["Propiedades",{name:"business"}],["Prestamos",{name:"region"}]]},account:{label:"Cuenta y Seguridad",sections:[["User info","/user/profile"],["Plan","/billing/upgrade"],["Billing","/billing"]]}});return s.value=u.tabName,(n,l)=>(t(),p(k,{title:"Configuracion"},{header:a(()=>[f(w,{sections:r.value,modelValue:s.value,"onUpdate:modelValue":[l[0]||(l[0]=e=>s.value=e),d]},null,8,["sections","modelValue"])]),default:a(()=>[i("div",C,[i("div",N,[(t(!0),o(y,null,g(r.value[s.value].sections,e=>(t(),o("div",{key:e,class:""},[e&&e.length?(t(),p(b(_),{key:0,href:e[1],class:"flex justify-between w-full px-2 py-2 font-bold text-gray-400 transition transform bg-white border hover:text-blue-400 hover:shadow-md hover:border-blue-400"},{default:a(()=>[i("div",null,h(e[0]),1),e[1]?(t(),o("div",{key:0,href:e[1]},B,8,P)):v("",!0)]),_:2},1032,["href"])):(t(),o("div",E))]))),128))])])]),_:1}))}};export{oe as default};
