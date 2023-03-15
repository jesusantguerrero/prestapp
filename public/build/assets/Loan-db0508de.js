import{l as p,z as f,q as g,a3 as m,c as y,w as n,o as x,a as o,b as l,h as V,u as s,an as h,E as b,M as w}from"./app-e7293397.js";import{_ as v}from"./AppLayout.vue_vue_type_style_index_0_lang-96d4f03f.js";import{_ as D}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{k as r,z as u,W as k}from"./atmosphere-ui-86b7f8ed.js";import{_ as C}from"./AccountSelect.vue_vue_type_script_setup_true_lang-cb870c82.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-dd64f8b8.js";import"./close-f1b40d6b.js";import"./PaymentGrid-6607ced0.js";import"./mathHelper-95aa3a2b.js";import"./exact-math.node-9a256fc0.js";import"./constants-0a903a05.js";import"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-53b90f8d.js";import"./InvoiceFormModal.vue_vue_type_script_setup_true_lang-88bfc502.js";import"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import"./user-outline-dcfa7b13.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-d0093e27.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-f1abe81a.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import"./constants-22f9a1fe.js";import"./usePaymentModal-ad844d5c.js";import"./FastAccessOptions.vue_vue_type_script_setup_true_lang-1fc13d45.js";import"./formatMoney-b7ef7683.js";const N={class:"flex items-center justify-between px-5 border-4 border-white rounded-md"},U=o("div",{class:"flex items-center space-x-2"},null,-1),$={class:"flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min"},z={class:"w-full h-auto px-5 py-10 mx-auto mt-12 space-y-5 bg-white divide-y divide-gray-200 rounded-md sm:px-6 lg:px-8"},B={class:"w-full"},E={class:"md:max-w-sm"},I=o("h2",{class:"my-4 font-bold"}," Preferencias de prestamo",-1),L=o("h2",{class:"my-4 font-bold"}," Detalle de moras",-1),P={class:"w-full"},S=o("h2",{class:"my-4 font-bold"}," Notas de ticket de pago",-1),ie=p({__name:"Loan",props:{settingData:{type:Object,default:()=>({loan_default_source_account:null,loan_grace_days:7,loan_apply_late_fees:!1,loan_payment_notes:""})},taxes:{type:Array,default:()=>[]}},setup(_){const i=_,a=f({...i.settingData});g(async()=>{console.log(i.settingData);const d=i.settingData.loan_default_source_account;d&&(a.loan_default_source_account=await m.get(`/loan-accounts/${d}`).then(({data:e})=>e))});const c=()=>{var d;m({url:"/api/settings",method:"POST",data:{...a,loan_default_source_account:(d=a.loan_default_source_account)==null?void 0:d.id}}).then(()=>{b.reload({preserveScroll:!0}),w({title:"Configuracion de prestamo guardada",message:"La configuracion de prestamo ha sido guardada correctamente",type:"success"})})};return(d,e)=>(x(),y(v,{title:"Configuracion / Prestamos"},{header:n(()=>[o("div",N,[U,o("div",$,[l(D,{variant:"inverse",onClick:e[0]||(e[0]=t=>c()),class:"w-32"},{default:n(()=>[V(" Guardar ")]),_:1})])])]),default:n(()=>[o("main",z,[o("article",B,[o("div",E,[I,l(s(r),{class:"",label:"Cuenta de origen"},{default:n(()=>[l(C,{modelValue:a.loan_default_source_account,"onUpdate:modelValue":e[1]||(e[1]=t=>a.loan_default_source_account=t)},null,8,["modelValue"])]),_:1}),l(s(r),{class:"mt-5",label:"Aplicar moras por defecto"},{default:n(()=>[l(s(h),{modelValue:a.loan_apply_late_fees,"onUpdate:modelValue":e[2]||(e[2]=t=>a.loan_apply_late_fees=t)},null,8,["modelValue"])]),_:1})])]),o("article",null,[L,l(s(r),{class:"md:max-w-sm",label:"Dias de gracia",rounded:""},{default:n(()=>[l(s(u),{modelValue:a.loan_grate_days,"onUpdate:modelValue":e[3]||(e[3]=t=>a.loan_grate_days=t),placeholder:"0",rounded:""},null,8,["modelValue"])]),_:1}),l(s(r),{class:"md:max-w-sm",label:"Interes de mora",rounded:""},{default:n(()=>[l(s(u),{modelValue:a.loan_late_fee,"onUpdate:modelValue":e[4]||(e[4]=t=>a.loan_late_fee=t),placeholder:"Interes de mora",rounded:""},null,8,["modelValue"])]),_:1})]),o("article",P,[S,l(s(k),{modelValue:a.loan_payment_notes,"onUpdate:modelValue":e[5]||(e[5]=t=>a.loan_payment_notes=t),class:"border rounded-md"},null,8,["modelValue"])])])]),_:1}))}});export{ie as default};