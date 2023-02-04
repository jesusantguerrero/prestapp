import{k as p,z as f,p as g,H as m,c as y,w as n,o as x,a as o,b as l,h as V,u as s,ai as h,E as b,M as w}from"./app-fef21123.js";import{_ as v}from"./AppLayout.vue_vue_type_script_setup_true_lang-dc441d96.js";import"./AppLayout.vue_vue_type_style_index_0_lang-cff2a039.js";import{_ as D}from"./AppButton.vue_vue_type_script_setup_true_lang-78bec775.js";import{R as r,V as u,F as C}from"./atmosphere-ui-43ec926d.js";import{_ as k}from"./AccountSelect.vue_vue_type_script_setup_true_lang-f3e358be.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-3ea39cd1.js";import"./PaymentGrid-926a1204.js";/* empty css                                                                    */import"./mathHelper-02ca0ff8.js";import"./exact-math.node-8398c915.js";import"./constants-0a903a05.js";import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import"./usePaymentModal-a07a875e.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-396b1c58.js";import"./close-ce456e3e.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-5401ee99.js";import"./clientInteractions-23fb4c77.js";import"./formatMoney-b7ef7683.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-7eed6d28.js";import"./AppSearchFilters-54a857cd.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */const N={class:"flex items-center justify-between px-5 border-4 border-white rounded-md"},U=o("div",{class:"flex items-center space-x-2"},null,-1),$={class:"flex overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min"},B={class:"w-full h-auto px-5 py-10 mx-auto mt-12 space-y-5 bg-white divide-y divide-gray-200 rounded-md sm:px-6 lg:px-8"},E={class:"w-full"},I={class:"md:max-w-sm"},L=o("h2",{class:"my-4 font-bold"}," Preferencias de prestamo",-1),P=o("h2",{class:"my-4 font-bold"}," Detalle de moras",-1),S={class:"w-full"},j=o("h2",{class:"my-4 font-bold"}," Notas de ticket de pago",-1),ie=p({__name:"Loan",props:{settingData:{type:Object,default:()=>({loan_default_source_account:null,loan_grace_days:7,loan_apply_late_fees:!1,loan_payment_notes:""})},taxes:{type:Array,default:()=>[]}},setup(_){const i=_,a=f({...i.settingData});g(async()=>{console.log(i.settingData);const d=i.settingData.loan_default_source_account;d&&(a.loan_default_source_account=await m.get(`/loan-accounts/${d}`).then(({data:e})=>e))});const c=()=>{var d;m({url:"/api/settings",method:"POST",data:{...a,loan_default_source_account:(d=a.loan_default_source_account)==null?void 0:d.id}}).then(()=>{b.reload({preserveScroll:!0}),w({title:"Configuracion de prestamo guardada",message:"La configuracion de prestamo ha sido guardada correctamente",type:"success"})})};return(d,e)=>(x(),y(v,{title:"Configuracion / Prestamos"},{header:n(()=>[o("div",N,[U,o("div",$,[l(D,{variant:"inverse",onClick:e[0]||(e[0]=t=>c()),class:"w-32"},{default:n(()=>[V(" Guardar ")]),_:1})])])]),default:n(()=>[o("main",B,[o("article",E,[o("div",I,[L,l(s(r),{class:"",label:"Cuenta de origen"},{default:n(()=>[l(k,{modelValue:a.loan_default_source_account,"onUpdate:modelValue":e[1]||(e[1]=t=>a.loan_default_source_account=t)},null,8,["modelValue"])]),_:1}),l(s(r),{class:"mt-5",label:"Aplicar moras por defecto"},{default:n(()=>[l(s(h),{modelValue:a.loan_apply_late_fees,"onUpdate:modelValue":e[2]||(e[2]=t=>a.loan_apply_late_fees=t)},null,8,["modelValue"])]),_:1})])]),o("article",null,[P,l(s(r),{class:"md:max-w-sm",label:"Dias de gracia",rounded:""},{default:n(()=>[l(s(u),{modelValue:a.loan_grate_days,"onUpdate:modelValue":e[3]||(e[3]=t=>a.loan_grate_days=t),placeholder:"0",rounded:""},null,8,["modelValue"])]),_:1}),l(s(r),{class:"md:max-w-sm",label:"Interes de mora",rounded:""},{default:n(()=>[l(s(u),{modelValue:a.loan_late_fee,"onUpdate:modelValue":e[4]||(e[4]=t=>a.loan_late_fee=t),placeholder:"Interes de mora",rounded:""},null,8,["modelValue"])]),_:1})]),o("article",S,[j,l(s(C),{modelValue:a.loan_payment_notes,"onUpdate:modelValue":e[5]||(e[5]=t=>a.loan_payment_notes=t),class:"border rounded-md"},null,8,["modelValue"])])])]),_:1}))}});export{ie as default};
