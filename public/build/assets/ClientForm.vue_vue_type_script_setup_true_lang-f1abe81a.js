import{W as _}from"./atmosphere-ui-86b7f8ed.js";import{_ as n}from"./AppFormField.vue_vue_type_style_index_0_lang-d6998fd8.js";import{_ as $}from"./SectionNav.vue_vue_type_script_setup_true_lang-e0dfad64.js";import{_ as U}from"./BaseSelect.vue_vue_type_style_index_1_lang-cbd59430.js";import{z as E,E as y,l as I,P as N,r as D,d as k,m as B,f as p,b as o,a as u,t as L,u as a,w as f,g as P,F as j,o as V,i as F}from"./app-e7293397.js";import{d as g}from"./constants-22f9a1fe.js";const S=E({isGeneratingDistribution:!1});class G{create(b,i="lender"){const m={...b,[`is_${i}`]:!0};return new Promise((l,c)=>{y.post("/clients",m,{onSuccess(r){l(r)},onError(r){c(r)}})})}generateOwnerDistribution(b,i){S.isGeneratingDistribution=!0;const m=`/clients/${b}/owner-distributions`;i?y.put(`${m}/${i}`):y.post(m)}}const O=new G,W={class:"pb-4 bg-white sm:p-6 sm:pb-4"},z={class:"font-bold text-lg w-full text-center"},A={key:0,class:"px-2 md:px-0"},J={class:"flex flex-col md:flex-row md:space-x-2"},R={class:"flex flex-col md:flex-row md:space-x-2"},H={class:"flex flex-col md:flex-row md:space-x-2"},K={key:1,class:"px-2 md:px-0"},M={class:"flex space-x-2"},Q={class:"flex flex-col md:flex-row md:space-x-2"},X={key:2},Y={class:"flex space-x-2"},Z={class:"flex space-x-2"},se=I({__name:"ClientForm",props:{formData:null,type:{default:"lender"},disabled:{type:Boolean}},emits:["success","error","update:isLoading"],setup(s,{expose:b,emit:i}){const m=s,l=N({names:"",lastnames:"",email:"",cellphone:"",address_details:"",dniType:g[0],dni_type:"DNI",dni:"",work_name:"",work_email:"",work_cellphone:"",work_address_details:"",bank_name:"",bank_account_number:"",owner_distribution_date:""}),c=[{value:"general",label:"Datos Generales"},{value:"work",label:"Datos de trabajo"},{value:"accounting",label:"Cuenta y Pago"}],r=D("general"),T=k(()=>{var t;return(t=c.find(e=>e.value==r.value))==null?void 0:t.label}),q=k(()=>g.find(t=>t.name==l.dni_type).label),v=[{value:"lender",label:"Cliente de Prestamos"},{value:"owner",label:"Propierario"},{value:"tenant",label:"Inquilino"}],x=D(m.type),w=k({set(t){x.value=t.value},get(){return v.filter(t=>x.value==t.value)}}),C=()=>{i("update:isLoading",!0),O.create({...l,[`is_${x.value}`]:!0}).then(()=>{i("success")}).catch(t=>{i("update:isLoading",!1),i("error",t)}).finally(()=>{i("update:isLoading",!1)})};return b({reset:l.reset,onSubmit:C}),B(()=>m.formData,t=>{Object.keys(l.data()).forEach(e=>{e=="date"?l[e]=new Date(t[e]):t&&(l[e]=t[e])})},{deep:!0,immediate:!0}),(t,e)=>(V(),p(j,null,[o($,{class:"bg-primary/5 w-full",modelValue:r.value,"onUpdate:modelValue":e[0]||(e[0]=d=>r.value=d),"selected-class":"bg-base-lvl-3 text-primary font-bold",sections:c},null,8,["modelValue"]),u("main",W,[u("h1",z,L(a(T)),1),r.value=="general"?(V(),p("article",A,[u("section",J,[o(n,{label:"Nombres",modelValue:a(l).names,"onUpdate:modelValue":e[1]||(e[1]=d=>a(l).names=d),required:"",disabled:s.disabled},null,8,["modelValue","disabled"]),o(n,{label:"Apellidos",modelValue:a(l).lastnames,"onUpdate:modelValue":e[2]||(e[2]=d=>a(l).lastnames=d),rounded:"",required:"",disabled:s.disabled},null,8,["modelValue","disabled"])]),o(n,{label:"Dirección",required:""},{default:f(()=>[o(a(_),{modelValue:a(l).address_details,"onUpdate:modelValue":e[3]||(e[3]=d=>a(l).address_details=d),class:"border px-4 py-2 bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60",disabled:s.disabled},null,8,["modelValue","disabled"])]),_:1}),o(n,{label:"Tipo de cliente ",required:"",disabled:s.disabled},{default:f(()=>[o(U,{modelValue:a(w),"onUpdate:modelValue":e[4]||(e[4]=d=>F(w)?w.value=d:null),options:v,"track-by":"value",label:"label",placeholder:"Tipo de cliente",disabled:s.disabled},null,8,["modelValue","disabled"])]),_:1},8,["disabled"]),u("section",R,[o(n,{label:"Tipo Documento",required:""},{default:f(()=>[o(U,{options:a(g),"track-by":"name",modelValue:a(l).dniType,"onUpdate:modelValue":e[5]||(e[5]=d=>a(l).dniType=d),disabled:s.disabled},null,8,["options","modelValue","disabled"])]),_:1}),o(n,{label:a(q),modelValue:a(l).dni,"onUpdate:modelValue":e[6]||(e[6]=d=>a(l).dni=d),rounded:"",required:""},null,8,["label","modelValue"])]),u("section",H,[o(n,{label:"Email",modelValue:a(l).email,"onUpdate:modelValue":e[7]||(e[7]=d=>a(l).email=d),rounded:"",type:"email",disabled:s.disabled},null,8,["modelValue","disabled"]),o(n,{required:"",label:"Telefono",modelValue:a(l).cellphone,"onUpdate:modelValue":e[8]||(e[8]=d=>a(l).cellphone=d),type:"tel",rounded:"",disabled:s.disabled},null,8,["modelValue","disabled"])])])):r.value=="work"?(V(),p("article",K,[u("section",M,[o(n,{label:"Lugar de trabajo",modelValue:a(l).work_name,"onUpdate:modelValue":e[9]||(e[9]=d=>a(l).work_name=d),disabled:s.disabled},null,8,["modelValue","disabled"])]),o(n,{label:"Dirección de trabajo"},{default:f(()=>[o(a(_),{modelValue:a(l).work_address_details,"onUpdate:modelValue":e[10]||(e[10]=d=>a(l).work_address_details=d),class:"border",disabled:s.disabled},null,8,["modelValue","disabled"])]),_:1}),u("section",Q,[o(n,{label:"Email",modelValue:a(l).work_email,"onUpdate:modelValue":e[11]||(e[11]=d=>a(l).work_email=d),rounded:"",type:"email",disabled:s.disabled},null,8,["modelValue","disabled"]),o(n,{label:"Telefono",modelValue:a(l).work_cellphone,"onUpdate:modelValue":e[12]||(e[12]=d=>a(l).work_cellphone=d),type:"tel",rounded:"",disabled:s.disabled},null,8,["modelValue","disabled"])])])):r.value=="accounting"?(V(),p("article",X,[u("section",Y,[o(n,{label:"Banco",modelValue:a(l).bank_name,"onUpdate:modelValue":e[13]||(e[13]=d=>a(l).bank_name=d),disabled:s.disabled},null,8,["modelValue","disabled"]),o(n,{label:"No. de cuenta",modelValue:a(l).bank_account_number,"onUpdate:modelValue":e[14]||(e[14]=d=>a(l).bank_account_number=d),disabled:s.disabled},null,8,["modelValue","disabled"])]),u("section",Z,[o(n,{label:"Dia de generacion de factura",modelValue:a(l).owner_distribution_date,"onUpdate:modelValue":e[15]||(e[15]=d=>a(l).owner_distribution_date=d),disabled:s.disabled},null,8,["modelValue","disabled"])])])):P("",!0)])],64))}});export{S as I,se as _,O as c};
