import{k as i,z as t}from"./atmosphere-ui-86b7f8ed.js";import{_ as w}from"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import{_ as b}from"./FormSection.vue_vue_type_script_setup_true_lang-bb2801db.js";import{u as k}from"./useReactiveForm-9c94d9c1.js";import{l as y,B as U,d as x,f as r,b as o,w as s,u as e,g as _,o as d,a as m,c as u}from"./app-e7293397.js";const B={class:"w-full mt-8"},g=m("header",{class:"flex justify-between"},[m("label",{for:""},"Inquilino")],-1),v={key:0,class:"grid grid-cols-2 gap-2"},F=y({__name:"RentFormPersonal",props:{modelValue:null},emits:["update:modelValue"],setup(c,{emit:p}){const f=c,{modelValue:V}=U(f),{formData:l}=k({client:null,client_id:null},V,p);return x(()=>l.is_new_client?"Lista de clientes":"Nuevo cliente"),(C,n)=>(d(),r("section",null,[o(b,{"section-class":"w-full -px-10"},{default:s(()=>[m("div",B,[g,e(l).is_new_client?(d(),u(e(t),{key:1,modelValue:e(l).client_name,"onUpdate:modelValue":n[1]||(n[1]=a=>e(l).client_name=a),rounded:""},null,8,["modelValue"])):(d(),u(w,{key:0,modelValue:e(l).client,"onUpdate:modelValue":n[0]||(n[0]=a=>e(l).client=a),endpoint:"/api/clients?filter[is_tenant]=1",placeholder:"Selecciona un inquilino",label:"display_name","track-by":"id"},null,8,["modelValue"]))]),e(l).is_new_client?(d(),u(e(i),{key:0,label:"Apellidos",class:"w-full"},{default:s(()=>[o(e(t),{modelValue:e(l).client_last_name,"onUpdate:modelValue":n[2]||(n[2]=a=>e(l).client_last_name=a),rounded:""},null,8,["modelValue"])]),_:1})):_("",!0)]),_:1}),e(l).is_new_client?(d(),r("div",v,[o(e(i),{label:"Telefono",class:"w-full"},{default:s(()=>[o(e(t),{modelValue:e(l).client_phone_number,"onUpdate:modelValue":n[3]||(n[3]=a=>e(l).client_phone_number=a),rounded:""},null,8,["modelValue"])]),_:1}),o(e(i),{label:"Email",class:"w-full"},{default:s(()=>[o(e(t),{modelValue:e(l).client_email,"onUpdate:modelValue":n[4]||(n[4]=a=>e(l).client_email=a),rounded:""},null,8,["modelValue"])]),_:1}),o(e(i),{label:"Cedula",class:"w-full"},{default:s(()=>[o(e(t),{modelValue:e(l).client_dni,"onUpdate:modelValue":n[5]||(n[5]=a=>e(l).client_dni=a),rounded:""},null,8,["modelValue"])]),_:1})])):_("",!0)]))}});export{F as _};
