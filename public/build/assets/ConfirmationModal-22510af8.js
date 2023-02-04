import{_ as c}from"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import{o as i,c as n,w as r,a as s,s as o}from"./app-fef21123.js";const d={class:"bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4"},m={class:"sm:flex sm:items-start"},h=s("div",{class:"mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10"},[s("svg",{class:"h-6 w-6 text-red-600",stroke:"currentColor",fill:"none",viewBox:"0 0 24 24"},[s("path",{"stroke-linecap":"round","stroke-linejoin":"round","stroke-width":"2",d:"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"})])],-1),f={class:"mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"},x={class:"text-lg"},_={class:"mt-2"},u={class:"flex flex-row justify-end px-6 py-4 bg-gray-100 text-right"},v={__name:"ConfirmationModal",props:{show:{type:Boolean,default:!1},maxWidth:{type:String,default:"2xl"},closeable:{type:Boolean,default:!0}},emits:["close"],setup(t,{emit:l}){const a=()=>{l("close")};return(e,p)=>(i(),n(c,{show:t.show,"max-width":t.maxWidth,closeable:t.closeable,onClose:a},{default:r(()=>[s("div",d,[s("div",m,[h,s("div",f,[s("h3",x,[o(e.$slots,"title")]),s("div",_,[o(e.$slots,"content")])])])]),s("div",u,[o(e.$slots,"footer")])]),_:3},8,["show","max-width","closeable"]))}};export{v as _};
