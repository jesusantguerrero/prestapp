import{P as l,r as c,f as d,b as o,u as e,w as r,F as u,o as p,X as f,a,n as _,h as w,m as b}from"./app-fef21123.js";import{A as h}from"./AuthenticationCard-75df3d85.js";import{_ as g}from"./AuthenticationCardLogo-dfa58cf8.js";import{_ as x}from"./InputError-dba5ca0f.js";import{_ as v}from"./InputLabel-c7c4144f.js";import{_ as y}from"./PrimaryButton-7d539c21.js";import{_ as V}from"./TextInput-f5d19bba.js";import"./_plugin-vue_export-helper-c27b6911.js";const C=a("div",{class:"mb-4 text-sm text-gray-600"}," This is a secure area of the application. Please confirm your password before continuing. ",-1),$=["onSubmit"],k={class:"flex justify-end mt-4"},z={__name:"ConfirmPassword",setup(P){const s=l({password:""}),t=c(null),n=()=>{s.post(route("password.confirm"),{onFinish:()=>{s.reset(),t.value.focus()}})};return(A,i)=>(p(),d(u,null,[o(e(f),{title:"Secure Area"}),o(h,null,{logo:r(()=>[o(g)]),default:r(()=>[C,a("form",{onSubmit:b(n,["prevent"])},[a("div",null,[o(v,{for:"password",value:"Password"}),o(V,{id:"password",ref_key:"passwordInput",ref:t,modelValue:e(s).password,"onUpdate:modelValue":i[0]||(i[0]=m=>e(s).password=m),type:"password",class:"mt-1 block w-full",required:"",autocomplete:"current-password",autofocus:""},null,8,["modelValue"]),o(x,{class:"mt-2",message:e(s).errors.password},null,8,["message"])]),a("div",k,[o(y,{class:_(["ml-4",{"opacity-25":e(s).processing}]),disabled:e(s).processing},{default:r(()=>[w(" Confirm ")]),_:1},8,["class","disabled"])])],40,$)]),_:1})],64))}};export{z as default};
