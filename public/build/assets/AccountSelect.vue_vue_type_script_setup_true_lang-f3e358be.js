import{f as l}from"./formatMoney-b7ef7683.js";import{_}from"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */import{k as m,o as d,c as p,w as t,a as e,t as s,u as n}from"./app-fef21123.js";const r={class:"option__title"},u={class:"option__small ml-2"},f={class:"option__desc"},V={class:"option__title"},h={class:"option__small ml-2"},S=m({__name:"AccountSelect",props:{modelValue:null},emits:["update:modelValue"],setup(c){return(i,o)=>(d(),p(_,{endpoint:"/loan-accounts",modelValue:c.modelValue,"onUpdate:modelValue":o[0]||(o[0]=a=>i.$emit("update:modelValue",a)),placeholder:"Selecciona una cuenta",label:"alias","track-by":"id"},{singleLabel:t(({option:a})=>[e("span",r,s(a.alias??a.name),1),e("span",u,"("+s(n(l)(a.balance))+") ",1)]),option:t(({option:a})=>[e("div",f,[e("span",V,s(a.alias??a.name),1),e("span",h,"("+s(n(l)(a.balance))+") ",1)])]),_:1},8,["modelValue"]))}});export{S as _};