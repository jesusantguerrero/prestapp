import{_ as s}from"./BaseSelect.vue_vue_type_style_index_1_lang-7b13fe11.js";import{_}from"./FormSection.vue_vue_type_script_setup_true_lang-e638d4a6.js";import{u as V}from"./useReactiveForm-759cf632.js";import{_ as p}from"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import{l as y,B as b,d as x,f as k,b as a,w as r,o as i,u as o,c as w,g as U}from"./app-494b6f90.js";const $=y({__name:"RentFormProperty",props:{modelValue:null},emits:["update:modelValue"],setup(u,{emit:d}){const m=u,{modelValue:c}=b(m),{formData:e}=V({property_id:null,property:null,unit_id:null,unit:null,is_new_client:!1,client_id:null},c,d),f=x(()=>{var t;return(t=e.property)==null?void 0:t.units.filter(l=>l.status!=="RENTED")});return(t,l)=>(i(),k("section",null,[a(_,{"section-class":"flex flex-col md:space-x-4 md:flex-row"},{default:r(()=>[a(p,{class:"w-full",label:"Propiedad"},{default:r(()=>[a(s,{modelValue:o(e).property,"onUpdate:modelValue":l[0]||(l[0]=n=>o(e).property=n),endpoint:"/api/properties",placeholder:"Selecciona una propiedad",label:"name","track-by":"id"},null,8,["modelValue"])]),_:1}),o(e).property?(i(),w(p,{key:0,class:"w-full",label:"Unidad"},{default:r(()=>[a(s,{modelValue:o(e).unit,"onUpdate:modelValue":l[1]||(l[1]=n=>o(e).unit=n),options:o(f),placeholder:"Unidad #1",label:"name","track-by":"id"},null,8,["modelValue","options"])]),_:1})):U("",!0)]),_:1})]))}});export{$ as _};
