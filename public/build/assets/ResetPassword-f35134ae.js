import{P as u,f,b as r,u as e,w as n,F as b,o as w,X as g,y as P,h as V}from"./app-fef21123.js";import{A as k,R as t,V as c,n as i,I as y}from"./atmosphere-ui-43ec926d.js";const h={__name:"ResetPassword",props:{email:String,token:String},setup(m){const l=m,o=u({token:l.token,email:l.email,password:"",password_confirmation:""}),p=()=>{o.post(route("password.update"),{onFinish:()=>o.reset("password","password_confirmation")})};return(d,s)=>(w(),f(b,null,[r(e(g),{title:"Reset Password"}),r(e(y),null,{default:n(()=>[r(e(k),{"app-name":"PrestApp","btn-label":"Send email","btn-class":"mb-2 font-bold border-2 rounded-md border-primary bg-gradient-to-br from-purple-400 to-primary hover:bg-primary","link-class":"text-primary hover:text-primary",isLoading:e(o).processing,"onUpdate:isLoading":s[3]||(s[3]=a=>e(o).processing=a),mode:"register",errors:e(o).errors,onSubmit:p,onHomePressed:d.onHomePressed,onLinkPressed:d.onLinkPressed},{brand:n(()=>[r(e(P),{href:"/",class:"w-full font-light font-brand"},{default:n(()=>[V(" PrestaApp ")]),_:1})]),content:n(()=>[r(e(t),{label:"Email"},{default:n(()=>[r(e(c),{modelValue:e(o).email,"onUpdate:modelValue":s[0]||(s[0]=a=>e(o).email=a),required:""},null,8,["modelValue"])]),_:1}),r(e(t),{label:"Password"},{default:n(()=>[r(e(i),{class:"bg-white",modelValue:e(o).password,"onUpdate:modelValue":s[1]||(s[1]=a=>e(o).password=a),required:""},null,8,["modelValue"])]),_:1}),r(e(t),{label:"Confirm Password"},{default:n(()=>[r(e(i),{class:"bg-white",modelValue:e(o).password_confirmation,"onUpdate:modelValue":s[2]||(s[2]=a=>e(o).password_confirmation=a),required:""},null,8,["modelValue"])]),_:1})]),_:1},8,["isLoading","errors","onHomePressed","onLinkPressed"])]),_:1})],64))}};export{h as default};