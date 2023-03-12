import{l as r,r as p,c as _,w as n,o as c,a as o,b as a,h as b,u as t,a3 as f,M as x}from"./app-e7293397.js";import{_ as V}from"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import{_ as y}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{_ as i}from"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./atmosphere-ui-86b7f8ed.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./formatMoney-b7ef7683.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";const w={class:"flex items-center justify-end py-1 px-5"},g={class:"flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min"},v={class:"h-auto py-12 mx-auto max-w-7xl sm:px-6 lg:px-8"},U={class:"w-full px-5 py-10 space-y-5 bg-white divide-y divide-gray-200"},h={class:"pb-2 w-6/12"},C={class:"md:w-full"},N=o("h2",{class:"my-4 font-bold text-primary"},"Registro Legal",-1),D={class:"flex space-x-4"},B={class:"w-full flex space-x-4"},P={class:"md:w-full"},$=o("h2",{class:"my-4 font-bold text-primary"},"Detalles de direccion",-1),k={class:"flex space-x-4"},E={class:"flex space-x-4"},R={class:"flex mt-2 space-x-4"},j={class:"md:w-full"},z=o("h2",{class:"my-4 font-bold text-primary"},"Detalles de contacto",-1),ue=r({__name:"Business",props:{settingData:{type:Object,default(){return{}}}},setup(d){const u=d,e=p({});e.value={...e.value,...u.settingData};const m=()=>{f({url:"/api/settings",method:"POST",data:e.value}).then(()=>{x({title:"Business Data Updated",message:"Business Data Updated",type:"success"})})};return(L,s)=>(c(),_(V,{title:"Configuracion / Empresa"},{header:n(()=>[o("div",w,[o("div",g,[a(y,{variant:"secondary",onClick:s[0]||(s[0]=l=>m())},{default:n(()=>[b(" Save ")]),_:1})])])]),default:n(()=>[o("main",v,[o("div",U,[o("section",h,[o("article",C,[N,a(i,{label:"Nombre de Empresa",modelValue:t(e).business_name,"onUpdate:modelValue":s[1]||(s[1]=l=>t(e).business_name=l)},null,8,["modelValue"]),o("section",D,[a(i,{class:"w-4/12",label:"Nombre registro legal",modelValue:t(e).business_tax_id_label,"onUpdate:modelValue":s[2]||(s[2]=l=>t(e).business_tax_id_label=l),placeholder:"RNC"},null,8,["modelValue"]),a(i,{class:"w-8/12",label:"# Registro Legal",modelValue:t(e).business_tax_id_number,"onUpdate:modelValue":s[3]||(s[3]=l=>t(e).business_tax_id_number=l)},null,8,["modelValue"])])])]),o("section",B,[o("article",P,[$,o("section",k,[a(i,{class:"w-8/12",label:"Calle",modelValue:t(e).business_street,"onUpdate:modelValue":s[4]||(s[4]=l=>t(e).business_street=l)},null,8,["modelValue"]),a(i,{class:"w-4/12",label:"#",modelValue:t(e).business_apt_unit,"onUpdate:modelValue":s[5]||(s[5]=l=>t(e).business_apt_unit=l)},null,8,["modelValue"])]),o("section",E,[a(i,{class:"w-8/12",label:"Ciudad",modelValue:t(e).business_city,"onUpdate:modelValue":s[6]||(s[6]=l=>t(e).business_city=l)},null,8,["modelValue"]),a(i,{class:"w-4/12",label:"Codigo Zip",modelValue:t(e).business_zip_code,"onUpdate:modelValue":s[7]||(s[7]=l=>t(e).business_zip_code=l)},null,8,["modelValue"])]),o("section",R,[a(i,{class:"w-8/12",label:"Pais",modelValue:t(e).business_country,"onUpdate:modelValue":s[8]||(s[8]=l=>t(e).business_country=l)},null,8,["modelValue"]),a(i,{class:"w-4/12",label:"Provincia",modelValue:t(e).business_state,"onUpdate:modelValue":s[9]||(s[9]=l=>t(e).business_state=l)},null,8,["modelValue"])])]),o("article",j,[z,o("section",null,[a(i,{label:"Phone Number",type:"tel",modelValue:t(e).business_phone,"onUpdate:modelValue":s[10]||(s[10]=l=>t(e).business_phone=l)},null,8,["modelValue"])])])])])])]),_:1}))}});export{ue as default};
