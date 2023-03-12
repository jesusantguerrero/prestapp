import{l as m,f as o,a as t,k as i,n as l,o as n,h as u,t as c,b,w as f,u as d,E as x,g as v,F as y,j as h,c as g}from"./app-494b6f90.js";import{F as k,t as w}from"./atmosphere-ui-83e2a306.js";const L={class:"font-bold text-body-1"},B={class:"text-primary"},C={key:0,class:"space-x-2"},$=t("i",{class:"fa fa-home"},null,-1),N=m({__name:"WelcomeWidget",props:{message:null,username:null,cards:null,actionLabel:null,actionLink:null,sectionClass:{default:"flex flex-col md:flex-row py-4 space-y-2 md:space-y-0 md:space-x-4 divide-x-2 rounded-md divide-base-lvl-2 bg-base-lvl-3"},borderless:{type:Boolean},rounded:{type:Boolean,default:!0}},setup(e){return(a,r)=>(n(),o("article",{class:l(["px-5 pt-3 transition divide-y divide-base bg-base-lvl-3",[!e.borderless&&"border-base border",e.rounded&&"rounded-lg "]])},[t("section",{class:l(["items-center justify-between flex",!a.$slots.title&&"pb-2"])},[i(a.$slots,"title",{},()=>[t("h1",L,[u(c(e.message)+" ",1),t("span",B,c(e.username),1)])]),t("div",null,[i(a.$slots,"actions",{},()=>[e.actionLabel&&e.actionLink?(n(),o("div",C,[b(d(k),{class:"text-sm text-primary px-0",rounded:"",onClick:r[0]||(r[0]=s=>e.actionLink&&d(x).visit(e.actionLink))},{default:f(()=>[$,u(" "+c(e.actionLabel),1)]),_:1})])):v("",!0)])])],2),i(a.$slots,"content",{},()=>[t("section",{class:l(e.sectionClass)},[(n(!0),o(y,null,h(e.cards,s=>(n(),g(d(w),{class:l(["w-full h-24 shadow-none",[s.accent?"bg-primary-shade-2 text-white":"bg-white text-primary"]]),icon:`fas ${s.icon}`,value:s.value,title:s.label},null,8,["class","icon","value","title"]))),256))],2)])],2))}});export{N as _};
