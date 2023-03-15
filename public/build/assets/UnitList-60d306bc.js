import{_ as R}from"./trash-b41c2bba.js";import{_ as w}from"./file-8bfe57ae.js";import{_ as A}from"./chevron-right-5280f715.js";import{_ as E}from"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import{Q as _,l as I,d as T,B as D,z as F,r as O,c as h,w as n,o as v,b as a,a as u,u as e,h as J,E as d,y as L}from"./app-e7293397.js";import{a as Q,_ as q}from"./BaseTable.vue_vue_type_style_index_0_lang-efd97e4a.js";import{_ as f}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{_ as G}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-516e104c.js";import{_ as H}from"./ButtonGroup.vue_vue_type_script_setup_true_lang-6c7ef160.js";import{_ as K}from"./UnitTag.vue_vue_type_script_setup_true_lang-5b0bf4f3.js";import{f as W}from"./formatMoney-b7ef7683.js";import{_ as X}from"./UnitTitle.vue_vue_type_script_setup_true_lang-c25f773c.js";import{u as Y}from"./useServerSearch-037538e8.js";import{b as x}from"./constants-fbb1b618.js";import{_ as Z}from"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./atmosphere-ui-86b7f8ed.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./customCell-f0a6687f.js";import"./menus-f4817d1f.js";import"./index-9dc2d84c.js";import"./index-3624ec38.js";const tt=[{name:"property",label:"Propiedad/Unidad",class:"text-left",headerClass:"text-left",width:550,render(s){var i,p,c,l;return _("div",{class:"justify-center"},[_(X,{title:((i=s.property)==null?void 0:i.short_name)+" / "+s.name,ownerName:(p=s.owner)==null?void 0:p.display_name,tenantName:(l=(c=s.contract)==null?void 0:c.client)==null?void 0:l.display_name})])}},{name:"price",label:"Precio de Renta",align:"right",class:"text-right",render(s){return _("span",{class:"text-success font-bold"},W(s.price))}},{name:"actions",label:" "}],et={class:"p-5 mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8"},at={class:"flex space-x-4"},rt={class:"flex justify-end items-center"},ot={class:"flex"},Qt=I({__name:"UnitList",props:{units:null,serverSearchOptions:null},setup(s){const i=s,p=T(()=>Array.isArray(i.units)?{data:i.units}:i.units),{serverSearchOptions:c}=D(i),{executeSearch:l,updateSearch:C,changeSize:S,paginate:V,reset:B,state:m}=Y(c,r=>{C(`/units?${r}`)},{manual:!0}),y=F({status:x.find(r=>r.name===m.filters.status)??x[0]}),P=r=>{m.filters.status=r.name,l()},U={selectable:!0,searchBar:!0,pagination:!0},N=r=>{},g=O("units"),b={units:{label:"Unidades",link:"/units?filter[status]=RENTED"},properties:{label:"Propiedades",link:"/properties"}},j=r=>{d.get(b[r].link)};return(r,o)=>{const z=A,$=w,M=R;return v(),h(E,{title:"Propiedades"},{header:n(()=>[a(G)]),default:n(()=>[u("main",et,[u("section",at,[a(Q,{modelValue:e(m).search,"onUpdate:modelValue":o[0]||(o[0]=t=>e(m).search=t),modelModifiers:{lazy:!0},class:"w-full md:flex","has-filters":!0,onClear:o[1]||(o[1]=t=>e(B)()),onBlur:e(l)},null,8,["modelValue","onBlur"]),a(Z,{placeholder:"Filtrar",options:e(x),modelValue:y.status,"onUpdate:modelValue":[o[2]||(o[2]=t=>y.status=t),P],label:"label","track-by":"name"},null,8,["options","modelValue"]),a(H,{class:"w-full md:w-fit","onUpdate:modelValue":[j,o[3]||(o[3]=t=>g.value=t)],values:b,modelValue:g.value},null,8,["modelValue"]),a(f,{variant:"secondary",onClick:o[4]||(o[4]=t=>e(d).visit(r.route("properties.create")))},{default:n(()=>[J(" Agregar Propiedad ")]),_:1})]),a(q,{class:"bg-white rounded-md text-body-1 mt-4","table-data":e(p).data,cols:e(tt),pagination:e(m),total:s.units.total,onSearch:e(l),onPaginate:e(V),onSizeChange:e(S),config:U},{actions:n(({scope:{row:t}})=>[u("div",rt,[a(K,{status:t.status},null,8,["status"]),a(e(L),{class:"relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max",href:`/properties/${t.property_id}?unit=${t.id}`},{default:n(()=>[a(z)]),_:2},1032,["href"]),u("div",ot,[t.contract?(v(),h(f,{key:0,class:"hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400",variant:"neutral",onClick:k=>e(d).visit(`/rents/${t.contract.id}`)},{default:n(()=>[a($)]),_:2},1032,["onClick"])):(v(),h(f,{key:1,class:"hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400",variant:"neutral",onClick:k=>e(d).visit(`/rents/create?unit=${t.id}`)},{default:n(()=>[a($)]),_:2},1032,["onClick"]))]),a(f,{variant:"neutral",class:"hover:text-error transition items-center flex flex-col justify-center hover:border-red-400",onClick:k=>N(t)},{default:n(()=>[a(M)]),_:2},1032,["onClick"])])]),_:1},8,["table-data","cols","pagination","total","onSearch","onPaginate","onSizeChange"])])]),_:1})}}});export{Qt as default};