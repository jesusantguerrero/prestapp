import{k as m,d as f,o as l,f as r,s as i,n as b,g as v,D as y,u as C,F as S,j as h,a as k,c as g,w as x,h as V,t as B,E as $}from"./app-fef21123.js";const N=m({__name:"SectionNavTab",props:{keepActiveState:{type:Boolean,default:!0},icon:{type:String},isSelected:{type:Boolean,default:!1},tabClass:{type:String,default:""},selectedClass:{type:String,default:"border-b-2 border-primary text-primary"}},setup(t){const c=t,u=f(()=>[c.keepActiveState&&"focus:bg-base-lvl-1"]);return(s,d)=>(l(),r("button",y({type:"button",class:["inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition border-b-2 border-transparent hover:bg-base-lvl-2 hover:text-body/80 focus:outline-none",[...C(u),t.isSelected?t.selectedClass:"text-body"]]},s.$attrs),[i(s.$slots,"icon",{},()=>[t.icon?(l(),r("i",{key:0,class:b(["fa mr-2",t.icon])},null,2)):v("",!0)]),i(s.$slots,"default")],16))}}),w={class:"flex justify-between w-full pr-8"},j={class:"flex items-center justify-end py-1 ml-auto space-x-2"},E=m({__name:"SectionNav",props:{sections:null,modelValue:null,selectedClass:null},emits:["update:modelValue"],setup(t,{emit:c}){const u=t,s=f(()=>{var e;return(e=document==null?void 0:document.location)==null?void 0:e.pathname}),d=(e,n)=>{const a=e.url||e.value||n,o=u.modelValue||s.value;return a==o},p=(e,n)=>{e.url?$.visit(e.url):c("update:modelValue",e.value||n)};return(e,n)=>(l(),r("div",w,[(l(!0),r(S,null,h(t.sections,(a,o)=>(l(),g(N,{onClick:A=>p(a,o),"is-selected":d(a,o),key:a.url,"selected-class":t.selectedClass},{default:x(()=>[V(B(a.label),1)]),_:2},1032,["onClick","is-selected","selected-class"]))),128)),k("div",j,[i(e.$slots,"actions")])]))}});export{E as _};