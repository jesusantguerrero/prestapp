import{_ as d}from"./AppLayout.vue_vue_type_style_index_0_lang-3aae0800.js";import{_ as m}from"./ButtonGroup.vue_vue_type_script_setup_true_lang-f813575d.js";import{l as c,J as p,r as u,c as _,w as b,o as f,a,t as h,b as k,k as g,E as x}from"./app-494b6f90.js";const v={class:"p-5 pt-0 mx-auto text-gray-500 sm:px-6 lg:px-8"},w={class:"flex justify-between mt-4 md:mt-0 mb-4"},y={class:"hidden md:inline-block"},D=c({__name:"DashboardTemplate",props:{user:{type:Object,required:!0}},setup(l){const n=p().props,s=u(n.section),t={general:{label:"General",link:"/dashboard"},loans:{label:"Prestamos",link:"/dashboard/loan"},realState:{label:"Inmobiliaria",link:"/dashboard/property"}},r=e=>{x.get(t[e].link)};return(e,o)=>(f(),_(d,{title:"Dashboard"},{default:b(()=>[a("main",v,[a("div",w,[a("h4",y,"Bienvenido, "+h(l.user.name),1),k(m,{class:"w-full md:w-fit","onUpdate:modelValue":[r,o[0]||(o[0]=i=>s.value=i)],values:t,modelValue:s.value},null,8,["modelValue"])]),g(e.$slots,"default")])]),_:3}))}});export{D as _};
