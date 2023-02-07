import{N as w,V as m,R as d,y as k,F as h}from"./atmosphere-ui-55a1c253.js";import{k as v,P as U,l as $,c as N,w as s,o as u,b as l,a as p,u as e,h as c,f as y,j as C,F}from"./app-de9d56f0.js";import{_ as B}from"./AppLayout.vue_vue_type_script_setup_true_lang-5b8fe57e.js";import"./AppLayout.vue_vue_type_style_index_0_lang-1b19aa61.js";import{_ as T}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-19b23e2c.js";import{_ as j}from"./AppButton.vue_vue_type_script_setup_true_lang-81087974.js";import{p as P}from"./constants-ca1255a1.js";import{_ as R}from"./BaseSelect.vue_vue_type_script_setup_true_lang-7415b2b5.js";/* empty css                                                   */import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-e6300ed2.js";import"./PaymentGrid-aa03ae3b.js";/* empty css                                                                    */import"./mathHelper-c551ea9f.js";import"./exact-math.node-4d03343f.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-a83a84cb.js";import"./formatMoney-b7ef7683.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-26f85fd9.js";import"./usePaymentModal-6968ac37.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-ca470652.js";import"./close-69e3a4b1.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-8a48d595.js";import"./clientInteractions-a56251a7.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-de720a79.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-46ceeab7.js";import"./AppSearchFilters-d4c59cd5.js";import"./menus-942163d8.js";const S={class:"flex justify-end space-x-2"},A={class:"mx-auto mt-8 text-gray-500 sm:px-6 lg:px-8"},D={class:"w-full px-5 py-4 space-y-4 text-gray-600 bg-white rounded-md shadow-md"},E={class:"flex space-x-5"},L={class:"md:flex md:space-y-0 md:space-x-2 space-y-4"},G={class:"space-x-4 grid grid-cols-[196px,1fr]"},I=p("i",{class:"mr-2 fa fa-plus-circle"},null,-1),Ve=v({__name:"PropertyForm",props:["properties","clients"],setup(V){const n=V,r=U({name:"",description:"",owner:null,owner_id:"",address:"",property_type:"",price:0,units:[{price:0,description:""}]});$(()=>n.properties,a=>{a&&b(a,r)},{immediate:!0,deep:!0});function b(a,t){Object.keys(t).forEach(o=>{t[o]=a[o]||t[o],o=="owner_id"&&(t.owner=n.clients.find(i=>i.id==t.owner_id))})}const x=()=>{var i;const a=(i=n.properties)==null?void 0:i.id,t=a?"put":"post",o=a?`/${n.properties.id}`:"";r.transform(f=>{var _;return{...f,owner_id:(_=f.owner)==null?void 0:_.id}})[t](`/properties${o}`,{onsuccess(){route.visit(route("properties"))}})},g=()=>{const a=r.units.length+1;r.units.push({index:a,description:"",price:0})};return(a,t)=>(u(),N(B,{title:"Agregar propiedad"},{header:s(()=>[l(T,null,{actions:s(()=>[p("section",S,[l(e(w),{class:"font-bold text-red-400 bg-gray-100 rounded-md",variant:"secondary",onClick:t[0]||(t[0]=o=>a.goToList())},{default:s(()=>[c(" Cancelar ")]),_:1}),l(j,{variant:"inverse",onClick:x},{default:s(()=>[c(" Guardar propiedad ")]),_:1})])]),_:1})]),default:s(()=>[p("main",A,[p("div",D,[p("div",E,[l(e(d),{class:"w-full",label:"Dirección"},{default:s(()=>[l(e(m),{modelValue:e(r).address,"onUpdate:modelValue":t[1]||(t[1]=o=>e(r).address=o),class:"w-full border-b focus:outline-none",placeholder:"Dirección",rounded:""},null,8,["modelValue"])]),_:1}),l(e(d),{class:"w-4/12",label:"Dueño de propiedad"},{default:s(()=>[l(R,{modelValue:e(r).owner,"onUpdate:modelValue":t[2]||(t[2]=o=>e(r).owner=o),endpoint:"/api/clients?filter[is_owner]=1",placeholder:"Selecciona un dueño",label:"display_name","track-by":"id"},null,8,["modelValue"])]),_:1})]),p("div",L,[l(e(d),{class:"w-full",label:"Nombre de propiedad"},{default:s(()=>[l(e(m),{modelValue:e(r).name,"onUpdate:modelValue":t[3]||(t[3]=o=>e(r).name=o),class:"w-full",placeholder:"Nombre de propiedad",rounded:""},null,8,["modelValue"])]),_:1}),l(e(d),{class:"w-full",label:"Tipo de propiedad"},{default:s(()=>[l(e(k),{modelValue:e(r).property_type,"onUpdate:modelValue":t[4]||(t[4]=o=>e(r).property_type=o),selected:e(r).type,"onUpdate:selected":t[5]||(t[5]=o=>e(r).type=o),options:e(P),placeholder:"Selecciona un tipo",label:"label","key-track":"value"},null,8,["modelValue","selected","options"])]),_:1})]),(u(!0),y(F,null,C(e(r).units,o=>(u(),y("section",G,[l(e(d),{class:"w-full",label:"Precio de Renta"},{default:s(()=>[l(e(m),{modelValue:o.price,"onUpdate:modelValue":i=>o.price=i,class:"w-full",rounded:"","number-format":""},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024),l(e(d),{label:"Descripción"},{default:s(()=>[l(e(h),{modelValue:o.description,"onUpdate:modelValue":i=>o.description=i,class:"w-full p-2 border focus:outline-none",placeholder:"Descripcion de la propiedad"},null,8,["modelValue","onUpdate:modelValue"])]),_:2},1024)]))),256)),l(e(w),{class:"text-gray-400",onClick:t[6]||(t[6]=o=>g())},{default:s(()=>[I,c(" Add unit ")]),_:1})])])]),_:1}))}});export{Ve as default};