import{_ as m}from"./BaseTable.vue_vue_type_style_index_0_lang-39691f37.js";import{g as b,a as _,b as f,_ as h}from"./constants-a62dae4c.js";import{f as p}from"./index-9dc2d84c.js";import{l as x,o as y,c as g,w as o,a as e,t as a,u as s,b as l,h as c,y as d,n as i,k as v}from"./app-494b6f90.js";import{f as r}from"./formatMoney-b7ef7683.js";const C=[{label:"Concept",name:"concept",width:240},{label:"Fecha",name:"date",align:"center",class:"text-center",width:90,render(n){return p(n.date)}},{label:"Categoria / Propiedad",name:"category",width:300},{label:"Total / Deuda",name:"total",type:"money",width:150},{label:"Status",name:"status",width:150},{label:" ",width:300,name:"actions",type:"custom"}],D={class:"font-bold text-blue-400"},S={class:"font-bold text-gray-300"},$=e("i",{class:"fa fa-user text-xs"},null,-1),z={class:"font-bold capitalize text-primary"},I={class:"text-sm"},N={class:"font-bold"},V={class:"flex items-center justify-end space-x-2"},F=x({__name:"InvoiceTable",props:{invoiceData:{type:Array},accountsEndpoint:{type:String}},setup(n){return(u,k)=>(y(),g(m,{cols:s(C),tableData:n.invoiceData,"show-prepend":!0,class:"mt-10 bg-base-lvl-3"},{date:o(({scope:{row:t}})=>[e("div",null,[e("div",D,a(s(p)(t.date)),1)])]),concept:o(({scope:{row:t}})=>[e("section",null,[e("p",null,[l(s(d),{href:`/${t.type=="INVOICE"?"invoices":"bills"}/${t.id}`,class:"text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-sm"},{default:o(()=>[c(a(t.concept)+" ",1),e("span",S,a(t.series)+" #"+a(t.number),1)]),_:2},1032,["href"])]),e("p",null,[l(s(d),{class:"text-sm text-body-1 mt-2",href:`/clients/${t.client_id||t.contact_id}`},{default:o(()=>[$,c(" "+a(t.client_name),1)]),_:2},1032,["href"])])])]),status:o(({scope:{row:t}})=>[e("div",{class:i(["font-bold capitalize text-sm",s(f)(t.status)])},[e("i",{class:i(s(b)(t.status))},null,2),c(" "+a(s(_)(t.status)),1)],2)]),category:o(({scope:{row:t}})=>[e("div",z,a(t.category),1),e("p",I,a(t.account_name),1)]),total:o(({scope:{row:t}})=>[e("div",N,[c(a(s(r)(t.total))+" ",1),e("p",{class:i(["font-bold",[t.debt>0?"text-red-500":"text-green-500"]])},a(s(r)(t.debt)),3)])]),actions:o(({scope:{row:t}})=>[v(u.$slots,"actions",{row:t},()=>[e("div",V,[l(h,{invoice:t,"accounts-endpoint":n.accountsEndpoint},null,8,["invoice","accounts-endpoint"])])])]),_:3},8,["cols","tableData"]))}});export{F as _};
