import{o as f,f as h,a as d,O as r,a8 as B,k,d as M,B as S,c as V,w as l,b as n,u as t,E as p,h as E,y as L,a9 as N}from"./app-9b138141.js";import{_ as w}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as I}from"./AtTable-47f80732.js";import T from"./LoanSectionNav-894e86bf.js";import{_}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{_ as A}from"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import{u as O}from"./useServerSearch-3cc40805.js";import{g as U,a as j}from"./constants-0a903a05.js";import{I as D}from"./IconMarker-87d7c953.js";import{f as H}from"./index-901d5032.js";import{f as u}from"./formatMoney-b7ef7683.js";import"./atmosphere-ui-18006871.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./mathHelper-7298fc42.js";import"./exact-math.node-df6f4533.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./customCell-c06172d5.js";import"./menus-1fa29540.js";import"./AppSearchFilters-f097730f.js";const P={viewBox:"0 0 24 24",width:"1.2em",height:"1.2em"},R=d("path",{fill:"currentColor",d:"M19 4h-3.5l-1-1h-5l-1 1H5v2h14M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12Z"},null,-1),Z=[R];function q(e,o){return f(),h("svg",P,Z)}const z={name:"mdi-trash",render:q},F={viewBox:"0 0 24 24",width:"1.2em",height:"1.2em"},J=d("path",{fill:"currentColor",d:"M8.59 16.58L13.17 12L8.59 7.41L10 6l6 6l-6 6l-1.41-1.42Z"},null,-1),G=[J];function K(e,o){return f(),h("svg",F,G)}const Q={name:"mdi-chevron-right",render:K},W=[{name:"client",label:"Cliente",class:"text-left",headerClass:"text-left",render(e){var o;return r("div",{class:"px-4"},[r("div",{class:"flex items-start space-x-2 text-primary font-bold"},[r(D,{class:"text-primary font-bold mt-1"}),r("span",(o=e.client)==null?void 0:o.fullName)]),r("span",{class:"text-body-1 text-sm"},`${u(e.total)} (${u(e.amount)})`)])}},{name:"first_installment_date",class:"text-center",headerClass:"text-center",label:"Fecha de inicio",render(e){return H(e.first_installment_date)}},{name:"interest_rate",label:"Terminos",class:"text-center",headerClass:"text-center",render(e){return r("div",{class:"space-x-2"},[r("span",e.frequency),r("span",e.interest_rate+" %")])}},{name:"amount_due",label:"Por pagar",class:"text-right",headerClass:"text-right",render(e){return r("div",{class:"space-x-4  flex items-center justify-end"},[r(B,{type:U(e.payment_status)},j(e.payment_status)),r("div",u(e.amount_due))])}},{name:"actions",label:"Acciones",class:"text-center flex my-auto justify-end pr-4",headerClass:"text-center"}],X={class:"pt-16"},Y={class:"flex"},Ae=k({__name:"Index",props:{loans:null,serverSearchOptions:null},setup(e){const o=e,x=M(()=>Array.isArray(o.loans)?o.loans:o.loans.data),v=async i=>{var c;await N.confirm(`Estas seguro de eliminar el prestamo ${i.id} por ${i.amount} a ${(c=i.client)==null?void 0:c.fullName}?`,"Eliminar prestamo")&&p.delete(route("loans.destroy",i),{onSuccess(){p.reload()}})},{serverSearchOptions:y}=S(o),{state:m,updateSearch:$,executeSearch:g,reset:b}=O(y,i=>{$(`/loans?${i}`)},{manual:!0});return(i,s)=>{const c=Q,C=z;return f(),V(w,{title:"Prestamos"},{header:l(()=>[n(T,null,{actions:l(()=>[n(A,{modelValue:t(m).search,"onUpdate:modelValue":s[0]||(s[0]=a=>t(m).search=a),modelModifiers:{lazy:!0},filters:t(m).filters,"onUpdate:filters":s[1]||(s[1]=a=>t(m).filters=a),sorts:t(m).sorts,"onUpdate:sorts":s[2]||(s[2]=a=>t(m).sorts=a),class:"w-full","has-filters":!0,onClear:s[3]||(s[3]=a=>t(b)()),onBlur:t(g)},null,8,["modelValue","filters","sorts","onBlur"]),n(_,{variant:"inverse",onClick:s[4]||(s[4]=a=>t(p).visit("/loans/create"))},{default:l(()=>[E(" Nuevo prestamo ")]),_:1})]),_:1})]),default:l(()=>[d("main",X,[n(I,{"table-data":t(x),cols:t(W),class:"bg-white rounded-md text-body-1"},{actions:l(({scope:{row:a}})=>[d("div",Y,[n(t(L),{class:"relative inline-block px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max",href:`/loans/${a.id}`},{default:l(()=>[n(c)]),_:2},1032,["href"]),n(_,{variant:"neutral",class:"hover:text-error transition hover:border-red-400",onClick:ee=>v(a)},{default:l(()=>[n(C)]),_:2},1032,["onClick"])])]),_:1},8,["table-data","cols"])])]),_:1})}}});export{Ae as default};
