import{A as p,I as g}from"./atmosphere-ui-55a1c253.js";import{P as u,f as b,b as s,u as r,w as n,F as c,o as f,X as L,y as _,h as w,E as a}from"./app-de9d56f0.js";const h={__name:"Login",props:{canResetPassword:Boolean,status:String},setup(x){const e=u({email:"",password:"",remember:!1}),i=()=>{a.visit("/")},m=()=>{a.visit("register")},d=t=>{e.transform(o=>({...o,...t,remember:e.remember?"on":""})).post(route("login"),{onFinish:()=>e.reset("password")})};return(t,o)=>(f(),b(c,null,[s(r(L),{title:"Log in"}),s(r(g),null,{default:n(()=>[s(r(p),{"app-name":"Loger","btn-class":"mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary","link-class":"text-primary hover:text-primary",isLoading:r(e).processing,"onUpdate:isLoading":o[0]||(o[0]=l=>r(e).processing=l),errors:r(e).errors,onSubmit:d,onHomePressed:i,onLinkPressed:m},{brand:n(()=>[s(r(_),{to:{name:"landing"},class:"w-full font-light font-brand"},{default:n(()=>[w(" ICLoan ")]),_:1})]),_:1},8,["isLoading","errors"])]),_:1})],64))}};export{h as default};