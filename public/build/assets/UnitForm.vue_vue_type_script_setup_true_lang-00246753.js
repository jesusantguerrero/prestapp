import{_ as r,a as c,b as f}from"./sharp-photo-size-select-small-cad54262.js";import{z as p,W as _}from"./atmosphere-ui-83e2a306.js";import{_ as t}from"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import{l as b,f as V,a as s,b as o,w as a,o as x,u}from"./app-494b6f90.js";const w={class:"grid grid-cols-4 gap-4"},h={class:"inline-blocks h-full flex items-center px-2"},g={class:"inline-blocks h-full flex items-center px-2"},B={class:"inline-blocks h-full flex items-center px-2"},C=b({__name:"UnitForm",props:{unit:null},setup(e){return(U,l)=>{const i=r,d=c,m=f;return x(),V("section",null,[s("section",w,[o(t,{class:"w-full",label:"Precio de Renta"},{default:a(()=>[o(u(p),{modelValue:e.unit.price,"onUpdate:modelValue":l[0]||(l[0]=n=>e.unit.price=n),class:"w-full",rounded:"","number-format":""},null,8,["modelValue"])]),_:1}),o(t,{class:"w-full",label:"Area",modelValue:e.unit.area,"onUpdate:modelValue":l[1]||(l[1]=n=>e.unit.area=n),rounded:""},{prefix:a(()=>[s("span",h,[o(i)])]),_:1},8,["modelValue"]),o(t,{class:"w-full",label:"Habitaciones",modelValue:e.unit.bedrooms,"onUpdate:modelValue":l[2]||(l[2]=n=>e.unit.bedrooms=n),rounded:""},{prefix:a(()=>[s("span",g,[o(d)])]),_:1},8,["modelValue"]),o(t,{class:"w-full",label:"Baños",modelValue:e.unit.bathrooms,"onUpdate:modelValue":l[3]||(l[3]=n=>e.unit.bathrooms=n),rounded:"",placeholder:"0"},{prefix:a(()=>[s("span",B,[o(m)])]),_:1},8,["modelValue"])]),o(t,{label:"Notas/Detalles"},{default:a(()=>[o(u(_),{modelValue:e.unit.description,"onUpdate:modelValue":l[4]||(l[4]=n=>e.unit.description=n),class:"w-full p-2 border focus:outline-none",placeholder:"Descripcion de la propiedad"},null,8,["modelValue"])]),_:1})])}}});export{C as _};
