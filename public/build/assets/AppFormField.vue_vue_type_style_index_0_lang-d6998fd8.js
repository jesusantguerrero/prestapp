import{k as n,z as t}from"./atmosphere-ui-86b7f8ed.js";import{l as s,c as i,w as a,u as d,o as m,k as r,b as f}from"./app-e7293397.js";const p=s({__name:"AppFormField",props:{label:null,modelValue:null,required:{type:Boolean,default:!1},placeholder:null,disabled:{type:Boolean},numberFormat:{type:Boolean}},setup(e){return(l,o)=>(m(),i(d(n),{label:e.label,class:"w-full text-secondary font-bold",required:e.required},{default:a(()=>[r(l.$slots,"default",{},()=>[f(d(t),{"model-value":e.modelValue,"onUpdate:modelValue":o[0]||(o[0]=u=>l.$emit("update:modelValue",u)),rounded:"",required:e.required,placeholder:e.placeholder,disabled:e.disabled,"number-format":e.numberFormat,class:"bg-neutral/20 shadow-none border-neutral hover:border-secondary/60 focus:border-secondary/60"},{suffix:a(()=>[r(l.$slots,"suffix")]),prefix:a(()=>[r(l.$slots,"prefix")]),_:3},8,["model-value","required","placeholder","disabled","number-format"])])]),_:3},8,["label","required"]))}});export{p as _};