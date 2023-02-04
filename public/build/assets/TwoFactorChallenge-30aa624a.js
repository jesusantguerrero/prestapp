import{r as c,P as k,d as u,f as V,b as a,u as e,w as s,F as x,o as l,X as P,a as m,t as C,c as p,Q as w}from"./app-fef21123.js";import{_ as I}from"./AuthenticationCardLogo-dfa58cf8.js";import{A as L,V as y,R as f,I as R}from"./atmosphere-ui-43ec926d.js";const U={class:"mb-4 text-sm text-body"},F={__name:"TwoFactorChallenge",setup(B){const n=c(!1),o=k({code:"",recovery_code:""}),d=c(null),i=c(null),v=async()=>{n.value^=!0,await w(),n.value?(d.value.focus(),o.code=""):(i.value.focus(),o.recovery_code="")},b=()=>{o.post(route("two-factor.login"))},g=u(()=>n.value?"Please confirm access to your account by entering one of your emergency recovery codes.":" Please confirm access to your account by entering the authentication code provided by your authenticator application.");return u(()=>n.value?"Use an authentication code":"Use a recovery code"),(_,r)=>(l(),V(x,null,[a(e(P),{title:"Two-factor Confirmation"}),a(e(R),null,{default:s(()=>[a(e(L),{"app-name":"PrestApp","btn-label":"Login","custom-link-label":"Recovery","btn-class":"mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary","link-class":"text-primary hover:text-primary",isLoading:e(o).processing,"onUpdate:isLoading":r[2]||(r[2]=t=>e(o).processing=t),mode:"register",errors:e(o).errors,onSubmit:b,onHomePressed:_.onHomePressed,onLinkPressed:r[3]||(r[3]=t=>v())},{brand:s(()=>[a(I)]),content:s(()=>[m("section",null,[m("div",U,C(e(g)),1),n.value?(l(),p(e(f),{key:1,label:"Recovery Code",field:"recovery_code",errors:e(o).errors},{default:s(()=>[a(e(y),{id:"recovery_code",ref_key:"recoveryCodeInput",ref:d,modelValue:e(o).recovery_code,"onUpdate:modelValue":r[1]||(r[1]=t=>e(o).recovery_code=t),type:"text",autocomplete:"one-time-code"},null,8,["modelValue"])]),_:1},8,["errors"])):(l(),p(e(f),{key:0,label:"Code"},{default:s(()=>[a(e(y),{id:"code",ref_key:"codeInput",ref:i,modelValue:e(o).code,"onUpdate:modelValue":r[0]||(r[0]=t=>e(o).code=t),type:"text",inputmode:"numeric",autofocus:"",autocomplete:"one-time-code"},null,8,["modelValue"])]),_:1}))])]),_:1},8,["isLoading","errors","onHomePressed"])]),_:1})],64))}};export{F as default};
