import{d as j,e as U,v as M,u as a,o as l,f as i,i as L,P as h,r as b,b as t,w as e,a as r,g,h as n,F as x,j as C,t as y,n as $}from"./app-fef21123.js";import{_ as z}from"./ActionMessage-387b050a.js";import{_ as E}from"./ActionSection-b2687e63.js";import{_ as R}from"./ConfirmationModal-22510af8.js";import{_ as Y}from"./DangerButton-d214b29f.js";import{_ as w}from"./DialogModal-08571405.js";import{_ as q}from"./FormSection-a174fcfe.js";import{_ as G}from"./InputError-dba5ca0f.js";import{_ as T}from"./InputLabel-c7c4144f.js";import{_ as S}from"./PrimaryButton-7d539c21.js";import{_ as A}from"./SecondaryButton-8571d888.js";import{S as H}from"./SectionBorder-15dada99.js";import{_ as J}from"./TextInput-f5d19bba.js";import"./SectionTitle-ad13cfda.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";const K=["value"],I={__name:"Checkbox",props:{checked:{type:[Array,Boolean],default:!1},value:{type:String,default:null}},emits:["update:checked"],setup(u,{emit:P}){const c=u,d=j({get(){return c.checked},set(v){P("update:checked",v)}});return(v,p)=>U((l(),i("input",{"onUpdate:modelValue":p[0]||(p[0]=m=>L(d)?d.value=m:null),type:"checkbox",value:u.value,class:"rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"},null,8,K)),[[M,a(d)]])}},O={class:"col-span-6 sm:col-span-4"},Q={key:0,class:"col-span-6"},W={class:"mt-2 grid grid-cols-1 md:grid-cols-2 gap-4"},X={class:"flex items-center"},Z={class:"ml-2 text-sm text-gray-600"},ee={key:0},se={class:"mt-10 sm:mt-0"},te={class:"space-y-6"},oe={class:"flex items-center"},ne={key:0,class:"text-sm text-gray-400"},ae=["onClick"],le=["onClick"],ie=r("div",null," Please copy your new API token. For your security, it won't be shown again. ",-1),re={key:0,class:"mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500"},ce={class:"grid grid-cols-1 md:grid-cols-2 gap-4"},ue={class:"flex items-center"},de={class:"ml-2 text-sm text-gray-600"},Te={__name:"ApiTokenManager",props:{tokens:Array,availablePermissions:Array,defaultPermissions:Array},setup(u){const c=h({name:"",permissions:u.defaultPermissions}),d=h({permissions:[]}),v=h(),p=b(!1),m=b(null),_=b(null),V=()=>{c.post(route("api-tokens.store"),{preserveScroll:!0,onSuccess:()=>{p.value=!0,c.reset()}})},D=f=>{d.permissions=f.abilities,m.value=f},F=()=>{d.put(route("api-tokens.update",m.value),{preserveScroll:!0,preserveState:!0,onSuccess:()=>m.value=null})},B=f=>{_.value=f},N=()=>{v.delete(route("api-tokens.destroy",_.value),{preserveScroll:!0,preserveState:!0,onSuccess:()=>_.value=null})};return(f,o)=>(l(),i("div",null,[t(q,{onSubmitted:V},{title:e(()=>[n(" Create API Token ")]),description:e(()=>[n(" API tokens allow third-party services to authenticate with our application on your behalf. ")]),form:e(()=>[r("div",O,[t(T,{for:"name",value:"Name"}),t(J,{id:"name",modelValue:a(c).name,"onUpdate:modelValue":o[0]||(o[0]=s=>a(c).name=s),type:"text",class:"mt-1 block w-full",autofocus:""},null,8,["modelValue"]),t(G,{message:a(c).errors.name,class:"mt-2"},null,8,["message"])]),u.availablePermissions.length>0?(l(),i("div",Q,[t(T,{for:"permissions",value:"Permissions"}),r("div",W,[(l(!0),i(x,null,C(u.availablePermissions,s=>(l(),i("div",{key:s},[r("label",X,[t(I,{checked:a(c).permissions,"onUpdate:checked":o[1]||(o[1]=k=>a(c).permissions=k),value:s},null,8,["checked","value"]),r("span",Z,y(s),1)])]))),128))])])):g("",!0)]),actions:e(()=>[t(z,{on:a(c).recentlySuccessful,class:"mr-3"},{default:e(()=>[n(" Created. ")]),_:1},8,["on"]),t(S,{class:$({"opacity-25":a(c).processing}),disabled:a(c).processing},{default:e(()=>[n(" Create ")]),_:1},8,["class","disabled"])]),_:1}),u.tokens.length>0?(l(),i("div",ee,[t(H),r("div",se,[t(E,null,{title:e(()=>[n(" Manage API Tokens ")]),description:e(()=>[n(" You may delete any of your existing tokens if they are no longer needed. ")]),content:e(()=>[r("div",te,[(l(!0),i(x,null,C(u.tokens,s=>(l(),i("div",{key:s.id,class:"flex items-center justify-between"},[r("div",null,y(s.name),1),r("div",oe,[s.last_used_ago?(l(),i("div",ne," Last used "+y(s.last_used_ago),1)):g("",!0),u.availablePermissions.length>0?(l(),i("button",{key:1,class:"cursor-pointer ml-6 text-sm text-gray-400 underline",onClick:k=>D(s)}," Permissions ",8,ae)):g("",!0),r("button",{class:"cursor-pointer ml-6 text-sm text-red-500",onClick:k=>B(s)}," Delete ",8,le)])]))),128))])]),_:1})])])):g("",!0),t(w,{show:p.value,onClose:o[3]||(o[3]=s=>p.value=!1)},{title:e(()=>[n(" API Token ")]),content:e(()=>[ie,f.$page.props.jetstream.flash.token?(l(),i("div",re,y(f.$page.props.jetstream.flash.token),1)):g("",!0)]),footer:e(()=>[t(A,{onClick:o[2]||(o[2]=s=>p.value=!1)},{default:e(()=>[n(" Close ")]),_:1})]),_:1},8,["show"]),t(w,{show:m.value!=null,onClose:o[6]||(o[6]=s=>m.value=null)},{title:e(()=>[n(" API Token Permissions ")]),content:e(()=>[r("div",ce,[(l(!0),i(x,null,C(u.availablePermissions,s=>(l(),i("div",{key:s},[r("label",ue,[t(I,{checked:a(d).permissions,"onUpdate:checked":o[4]||(o[4]=k=>a(d).permissions=k),value:s},null,8,["checked","value"]),r("span",de,y(s),1)])]))),128))])]),footer:e(()=>[t(A,{onClick:o[5]||(o[5]=s=>m.value=null)},{default:e(()=>[n(" Cancel ")]),_:1}),t(S,{class:$(["ml-3",{"opacity-25":a(d).processing}]),disabled:a(d).processing,onClick:F},{default:e(()=>[n(" Save ")]),_:1},8,["class","disabled"])]),_:1},8,["show"]),t(R,{show:_.value!=null,onClose:o[8]||(o[8]=s=>_.value=null)},{title:e(()=>[n(" Delete API Token ")]),content:e(()=>[n(" Are you sure you would like to delete this API token? ")]),footer:e(()=>[t(A,{onClick:o[7]||(o[7]=s=>_.value=null)},{default:e(()=>[n(" Cancel ")]),_:1}),t(Y,{class:$(["ml-3",{"opacity-25":a(v).processing}]),disabled:a(v).processing,onClick:N},{default:e(()=>[n(" Delete ")]),_:1},8,["class","disabled"])]),_:1},8,["show"])]))}};export{Te as default};