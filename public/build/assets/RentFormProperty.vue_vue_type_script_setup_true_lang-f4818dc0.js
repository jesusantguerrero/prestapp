import{R as s}from"./atmosphere-ui-43ec926d.js";import{_ as p}from"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */import{_}from"./FormSection.vue_vue_type_script_setup_true_lang-5aa9f2b8.js";import{u as V}from"./useReactiveForm-01cd3938.js";import{k as y,B as b,d as k,f as R,b as t,w as r,o as u,u as e,c as w,g as U}from"./app-fef21123.js";const D=y({__name:"RentFormProperty",props:{modelValue:null},emits:["update:modelValue"],setup(i,{emit:d}){const m=i,{modelValue:c}=b(m),{formData:l}=V({property_id:null,property:null,unit_id:null,unit:null,is_new_client:!1,client_id:null},c,d),f=k(()=>{var a;return(a=l.property)==null?void 0:a.units.filter(o=>o.status!=="RENTED")});return(a,o)=>(u(),R("section",null,[t(_,null,{default:r(()=>[t(e(s),{class:"w-full",label:"Propiedad"},{default:r(()=>[t(p,{modelValue:e(l).property,"onUpdate:modelValue":o[0]||(o[0]=n=>e(l).property=n),endpoint:"/api/properties",placeholder:"Selecciona una propiedad",label:"name","track-by":"id"},null,8,["modelValue"])]),_:1}),e(l).property?(u(),w(e(s),{key:0,class:"w-full",label:"Unidad"},{default:r(()=>[t(p,{modelValue:e(l).unit,"onUpdate:modelValue":o[1]||(o[1]=n=>e(l).unit=n),options:e(f),placeholder:"Unidad #1",label:"name","track-by":"id"},null,8,["modelValue","options"])]),_:1})):U("",!0)]),_:1})]))}});export{D as _};
