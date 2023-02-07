import{_ as u}from"./AppLayout.vue_vue_type_script_setup_true_lang-3f2d2e09.js";import"./AppLayout.vue_vue_type_style_index_0_lang-b5fae1fa.js";import{U as t,aa as _,ab as b,k as x,d as y,c as h,w as a,o as g,b as n,u as i,E as v,h as m,a as r,t as p,y as $}from"./app-5778ba55.js";import{f as k}from"./formatMoney-b7ef7683.js";import{I as w}from"./IconMarker-268209a0.js";import{g as C,a as P}from"./constants-ca1255a1.js";import{_ as A}from"./AtTable-9d037881.js";import{_ as B}from"./AppButton.vue_vue_type_script_setup_true_lang-bb509e3b.js";import{_ as D}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-782cd8ee.js";import{_ as E}from"./BudgetProgress.vue_vue_type_script_setup_true_lang-36dcadbf.js";import"./atmosphere-ui-cf0281b4.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-de8b7819.js";import"./PaymentGrid-aea5b371.js";/* empty css                                                                    */import"./mathHelper-b9912ba5.js";import"./exact-math.node-d4a7cd04.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-d05957ec.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-c8c2da4a.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-32b3b269.js";import"./usePaymentModal-7f0729f4.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-becaa604.js";import"./close-287af76b.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-a74f0c22.js";import"./clientInteractions-39478df6.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-4fc6099a.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-5f2b06a0.js";import"./AppSearchFilters-2dae0cfe.js";import"./customCell-8a32bb2a.js";import"./menus-2c7b33a0.js";const N=[{name:"address",label:"Dirección",class:"text-left",headerClass:"text-left",render(e){return e.owner.names+""+e.owner.lastnames,e.owner.names[0]+e.owner.lastnames[0],t("div",{class:"justify-center"},[t("div",{class:"flex items-center text-primary font-bold"},[t(w,{class:"text-primary font-bold"}),t("span",e.short_name)]),t("span",{class:"text-body-1 text-sm"},e.address)])}},{name:"owner",label:"Dueño",class:"text-left",headerClass:"text-left",minWidth:200,render(e){const o=e.owner.names+" "+e.owner.lastnames,l=e.owner.names[0]+e.owner.lastnames[0];return t("div",{class:"flex items-center space-x-2"},[t(_,{shape:"circle"},l),t("span",o)])}},{name:"balance",label:"Balance Pendiente",render(e){return k(e.balance)}},{name:"status",label:"Estado",render(e){return t(b,{type:P(e.status)},C(e.status))}},{name:"actions",label:"Acciones"}],V={class:"p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8"},I={class:"flex justify-between mb-1 font-bold"},S={class:"text-sm"},j={class:"text-secondary"},M={class:"text-primary"},T={class:"flex"},ye=x({__name:"Index",props:{properties:null},setup(e){const o=e,l=y(()=>Array.isArray(o.properties)?o.properties:o.properties.data);return(d,c)=>(g(),h(u,{title:"Propiedades"},{header:a(()=>[n(D,null,{actions:a(()=>[n(B,{variant:"secondary",onClick:c[0]||(c[0]=s=>i(v).visit(d.route("properties.create")))},{default:a(()=>[m(" Agregar Propiedad ")]),_:1})]),_:1})]),default:a(()=>[r("main",V,[n(A,{"table-data":i(l),cols:i(N),class:"bg-white rounded-md text-body-1"},{status:a(({scope:{row:s}})=>[r("div",null,[n(E,{goal:s.unit_count,current:s.available_units,class:"h-2.5 text-white rounded-md","progress-class":["bg-primary","bg-primary/5"],"show-labels":!1},{before:a(({progress:f})=>[r("header",I,[r("div",S,[r("span",j,p(s.available_units)+" disponible ",1),m(" de "+p(s.unit_count)+" unidades ",1)]),r("span",M,p(f)+"% ",1)])]),_:2},1032,["goal","current"])])]),actions:a(({scope:{row:s}})=>[r("div",T,[n(i($),{class:"relative inline-block px-5 py-2 overflow-hidden font-bold transition border rounded-md focus:outline-none hover:bg-opacity-80 min-w-max bg-primary/10 text-primary",href:`/properties/${s.id}`},{default:a(()=>[m(" Ver Propiedad ")]),_:2},1032,["href"])])]),_:1},8,["table-data","cols"])])]),_:1}))}});export{ye as default};
