import{_ as $}from"./close-ce456e3e.js";import{k,o as b,c as g,w as u,s as N,b as t,u as l,P as h,r as T,d as f,l as B,a as s,t as j,f as _,g as q,h as x,E as F}from"./app-fef21123.js";import{V as S,R as E,F as y,a as P,N as I}from"./atmosphere-ui-43ec926d.js";import{_ as A}from"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import{_ as O}from"./AppButton.vue_vue_type_script_setup_true_lang-78bec775.js";import{_ as R}from"./SectionNav.vue_vue_type_script_setup_true_lang-5401ee99.js";import{c as G}from"./clientInteractions-23fb4c77.js";const n=k({__name:"AppFormField",props:{label:null,modelValue:null},setup(r){return(c,i)=>(b(),g(l(E),{label:r.label,class:"w-full text-secondary font-bold"},{default:u(()=>[N(c.$slots,"default",{},()=>[t(l(S),{"model-value":r.modelValue,"onUpdate:modelValue":i[0]||(i[0]=p=>c.$emit("update:modelValue")),rounded:"",class:"bg-neutral/20 shadow-none border-neutral hover:border-secondary/60"},null,8,["model-value"])])]),_:3},8,["label"]))}}),w=[{name:"DNI",label:"Cedula"},{name:"PASSPORT",label:"Pasaporte"}],M={class:"border-b bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"},W=s("h4",{class:"font-bold text-xl"},"Crear Contacto",-1),J={class:"pb-4 bg-white sm:p-6 sm:pb-4"},L={class:"font-bold text-lg w-full text-center"},z={key:0},H={class:"flex space-x-2"},K={class:"flex space-x-2"},Q={class:"flex space-x-2"},X={key:1},Y={class:"flex space-x-2"},Z={class:"flex space-x-2"},ee={key:2},le={class:"flex space-x-2"},ae={class:"flex space-x-2"},oe={class:"px-6 py-4 space-x-3 text-gray-600 text-right bg-neutral"},ie=k({__name:"ClientFormModal",props:{show:{type:Boolean},maxWidth:{type:Number},closeable:{type:Boolean},formData:{type:[Object,null]},type:{type:String,default(){return"lender"}}},emits:["close","saved","update:show"],setup(r,{emit:c}){const i=r,p=()=>{a.reset(),c("update:show",!1)},a=h({names:"",lastnames:"",email:"",cellphone:"",address_details:"",dni_type:"DNI",dni:"",work_name:"",work_email:"",work_cellphone:"",work_address_details:"",bank_name:"",bank_account_number:"",owner_distribution_date:""}),V=[{value:"general",label:"Datos Generales"},{value:"work",label:"Datos de trabajo"},{value:"accounting",label:"Cuenta y Pago"}],m=T("general"),v=f(()=>{var d;return(d=V.find(e=>e.value==m.value))==null?void 0:d.label}),U=f(()=>w.find(d=>d.name==a.dni_type).label),C=()=>{G.create({...a,[`is_${i.type}`]:!0}).then(()=>{p(),F.reload()}).catch(d=>{console.log(d)})};return B(()=>i.formData,d=>{Object.keys(a.data()).forEach(e=>{e=="date"?a[e]=new Date(d[e]):d&&(a[e]=d[e])})},{deep:!0,immediate:!0}),(d,e)=>{const D=$;return b(),g(A,{show:r.show,"max-width":r.maxWidth,closeable:r.closeable,onClose:e[18]||(e[18]=o=>p())},{default:u(()=>[s("header",M,[W,s("button",{class:"hover:text-danger",onClick:e[0]||(e[0]=o=>p())},[t(D)])]),t(R,{class:"bg-primary/10 w-full py-1",modelValue:m.value,"onUpdate:modelValue":e[1]||(e[1]=o=>m.value=o),"selected-class":"bg-base-lvl-3 text-primary font-bold",sections:V},null,8,["modelValue"]),s("main",J,[s("h1",L,j(l(v)),1),m.value=="general"?(b(),_("article",z,[s("section",H,[t(n,{label:"Nombres",modelValue:l(a).names,"onUpdate:modelValue":e[2]||(e[2]=o=>l(a).names=o),required:""},null,8,["modelValue"]),t(n,{label:"Apellidos",modelValue:l(a).lastnames,"onUpdate:modelValue":e[3]||(e[3]=o=>l(a).lastnames=o),rounded:"",required:""},null,8,["modelValue"])]),t(n,{label:"Dirección",required:""},{default:u(()=>[t(l(y),{modelValue:l(a).address_details,"onUpdate:modelValue":e[4]||(e[4]=o=>l(a).address_details=o),class:"border"},null,8,["modelValue"])]),_:1}),s("section",K,[t(n,{label:"Tipo Documento",required:""},{default:u(()=>[t(l(P),{options:l(w),modelValue:l(a).dni_type,"onUpdate:modelValue":e[5]||(e[5]=o=>l(a).dni_type=o)},null,8,["options","modelValue"])]),_:1}),t(n,{label:l(U),modelValue:l(a).dni,"onUpdate:modelValue":e[6]||(e[6]=o=>l(a).dni=o),rounded:"",required:""},null,8,["label","modelValue"])]),s("section",Q,[t(n,{label:"Email",modelValue:l(a).email,"onUpdate:modelValue":e[7]||(e[7]=o=>l(a).email=o),rounded:"",type:"email"},null,8,["modelValue"]),t(n,{required:"",label:"Telefono",modelValue:l(a).cellphone,"onUpdate:modelValue":e[8]||(e[8]=o=>l(a).cellphone=o),type:"tel",rounded:""},null,8,["modelValue"])])])):m.value=="work"?(b(),_("article",X,[s("section",Y,[t(n,{label:"Lugar de trabajo",modelValue:l(a).work_name,"onUpdate:modelValue":e[9]||(e[9]=o=>l(a).work_name=o)},null,8,["modelValue"])]),t(n,{label:"Dirección de trabajo"},{default:u(()=>[t(l(y),{modelValue:l(a).work_address_details,"onUpdate:modelValue":e[10]||(e[10]=o=>l(a).work_address_details=o),class:"border"},null,8,["modelValue"])]),_:1}),s("section",Z,[t(n,{label:"Email",modelValue:l(a).work_email,"onUpdate:modelValue":e[11]||(e[11]=o=>l(a).work_email=o),rounded:"",type:"email"},null,8,["modelValue"]),t(n,{label:"Telefono",modelValue:l(a).work_cellphone,"onUpdate:modelValue":e[12]||(e[12]=o=>l(a).work_cellphone=o),type:"tel",rounded:""},null,8,["modelValue"])])])):m.value=="accounting"?(b(),_("article",ee,[s("section",le,[t(n,{label:"Banco",modelValue:l(a).bank_name,"onUpdate:modelValue":e[13]||(e[13]=o=>l(a).bank_name=o)},null,8,["modelValue"]),t(n,{label:"No. de cuenta",modelValue:l(a).bank_account_number,"onUpdate:modelValue":e[14]||(e[14]=o=>l(a).bank_account_number=o)},null,8,["modelValue"])]),s("section",ae,[t(n,{label:"Dia de generacion de factura",modelValue:l(a).owner_distribution_date,"onUpdate:modelValue":e[15]||(e[15]=o=>l(a).owner_distribution_date=o)},null,8,["modelValue"])])])):q("",!0)]),s("footer",oe,[t(l(I),{onClick:e[16]||(e[16]=o=>p()),class:"text-gray"},{default:u(()=>[x(" Cancelar ")]),_:1}),t(O,{variant:"secondary",onClick:e[17]||(e[17]=o=>C())},{default:u(()=>[x(" Guardar ")]),_:1})])]),_:1},8,["show","max-width","closeable"])}}});export{ie as _};