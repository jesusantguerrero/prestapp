import{_ as o}from"./SectionNav.vue_vue_type_script_setup_true_lang-3cbc58a3.js";import{_ as n}from"./NextPaymentsWidget.vue_vue_type_script_setup_true_lang-37f6b92c.js";import{l as m,r as i,f as c,c as r,w as d,o as t,b as u}from"./app-494b6f90.js";const p={class:"rounded-md overflow-hidden"},x=m({__name:"RepaymentWidget",setup(b){const e=i("next"),s={next:{label:"Proximas cuotas"},overdue:{label:"Cuotas vencidas"}};return(f,l)=>(t(),c("section",p,[e.value=="next"?(t(),r(n,{key:0},{title:d(()=>[u(o,{class:"bg-base-lvl-3 w-full","selected-class":"border-primary font-bold text-primary",modelValue:e.value,"onUpdate:modelValue":l[0]||(l[0]=a=>e.value=a),sections:s},null,8,["modelValue"])]),_:1})):(t(),r(n,{key:1,title:"Cuotas atrasadas",endpoint:"/api/repayments?filter[payment_status]=late",method:"back","default-range":"All","date-field":"payment_date",ranges:[{label:"All",value:null},{label:"90D",value:[90,0]},{label:"30D",value:[30,0]},{label:"7D",value:[7,0]},{label:"1D",value:[1,1]}]},{title:d(()=>[u(o,{class:"bg-base-lvl-3 w-full","selected-class":"border-primary font-bold text-primary",modelValue:e.value,"onUpdate:modelValue":l[1]||(l[1]=a=>e.value=a),sections:s},null,8,["modelValue"])]),_:1}))]))}});export{x as _};
