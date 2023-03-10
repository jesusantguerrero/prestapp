import{_ as u}from"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import{l as b,d as r,o as n,f as l,a as d,t as i,u as c,g as o,k as h,c as m,w as f,h as y}from"./app-494b6f90.js";const v={class:"flex items-center justify-between w-full px-5 py-2 border rounded-lg bg-base-lvl-3 border-base-lvl-3 no-print"},B={class:"flex items-center text-sm md:text-xl font-semibold leading-tight text-gray-500"},w={class:"mr-2 font-bold text-primary capitalize"},C={key:0},T={key:1,class:"px-2 py-1 ml-2 text-xs font-bold text-green-600 capitalize bg-green-100 rounded-3xl"},E={class:"button-container"},S={key:0,class:"flex"},V=b({__name:"AppSectionHeader",props:{title:{type:String,default:""},extractTitle:{type:String,default:""},name:{type:String,required:!0},resource:{type:Object,default(){return{}}},isEditing:{type:Boolean,default:!1},showEdit:{type:Boolean,default:!1},hideAction:{type:Boolean,default:!1}},setup(t){const e=t,x=r(()=>e.resource&&e.extractTitle?e.resource[e.extractTitle]:e.title?e.title:`Create a new ${e.name}`),p=r(()=>e.resource&&e.resource.id?`Update ${e.name}`:`Save ${e.name}`),k=r(()=>`Edit ${e.name}`),$=r(()=>`Create ${e.name}`);return(a,s)=>(n(),l("header",v,[d("h2",B,[d("span",w,i(t.name),1),t.resource&&t.resource.id?(n(),l("span",C,i(c(x)),1)):o("",!0),t.isEditing?(n(),l("span",T,"editing")):o("",!0)]),d("div",null,[a.$slots.actions||!t.hideAction?h(a.$slots,"actions",{key:0},()=>[d("div",E,[!t.resource||t.resource.id?(n(),l("div",S,[t.showEdit?o("",!0):(n(),m(u,{key:0,onClick:s[0]||(s[0]=g=>a.$emit("saved"))},{default:f(()=>[y(i(c(p)),1)]),_:1})),t.showEdit?(n(),m(u,{key:1,onClick:s[1]||(s[1]=g=>a.$emit("edit"))},{default:f(()=>[y(i(c(k)),1)]),_:1})):o("",!0)])):(n(),m(u,{key:1,onClick:s[2]||(s[2]=g=>a.$emit("create"))},{default:f(()=>[y(i(c($)),1)]),_:1}))])]):o("",!0)])]))}});export{V as _};
