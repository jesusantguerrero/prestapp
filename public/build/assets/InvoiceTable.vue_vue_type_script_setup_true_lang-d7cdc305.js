import{B as p}from"./atmosphere-ui-43ec926d.js";import{g as m,a as u,b,_ as f}from"./constants-ba74557c.js";import{f as _}from"./index-901d5032.js";import{k as h,o as x,c as y,w as o,a as e,t as a,u as s,b as l,h as n,y as d,n as i}from"./app-fef21123.js";import{f as r}from"./formatMoney-b7ef7683.js";const g=[{label:"Concept",name:"concept",width:300},{label:"Date",name:"date",width:200,render(c){return format(c.date)}},{label:"Categoria / Propiedad",name:"category",width:100},{label:"Total / Deuda",name:"total",type:"money"},{label:"Status",name:"status",width:200},{label:"",name:"actions",width:300,type:"custom"}],v={class:"font-bold text-blue-400"},D={class:"font-bold text-gray-300"},C=e("i",{class:"fa fa-user text-xs"},null,-1),S={class:"font-bold capitalize text-primary"},B={class:"text-sm"},$={class:"font-bold"},z={class:"flex items-center justify-end space-x-2"},A=h({__name:"InvoiceTable",props:{invoiceData:{type:Array},accountsEndpoint:{type:String}},setup(c){return(I,N)=>(x(),y(s(p),{cols:s(g),tableData:c.invoiceData,"show-prepend":!0,class:"mt-10 bg-base-lvl-3"},{date:o(({scope:{row:t}})=>[e("div",null,[e("div",v,a(s(_)(t.date)),1)])]),concept:o(({scope:{row:t}})=>[l(s(d),{href:`/${t.type=="INVOICE"?"invoices":"bills"}/${t.id}`,class:"text-blue-400 capitalize border-b border-blue-400 border-dashed cursor-pointer text-sm"},{default:o(()=>[n(a(t.concept)+" ",1),e("span",D,a(t.series)+" #"+a(t.number),1)]),_:2},1032,["href"]),l(s(d),{class:"text-sm text-body-1 mt-2",href:`/clients/${t.client_id||t.contact_id}`},{default:o(()=>[C,n(" "+a(t.client_name),1)]),_:2},1032,["href"])]),status:o(({scope:{row:t}})=>[e("div",{class:i(["font-bold capitalize text-sm",s(b)(t.status)])},[e("i",{class:i(s(m)(t.status))},null,2),n(" "+a(s(u)(t.status)),1)],2)]),category:o(({scope:{row:t}})=>[e("div",S,a(t.category),1),e("p",B,a(t.account_name),1)]),total:o(({scope:{row:t}})=>[e("div",$,[n(a(s(r)(t.total))+" ",1),e("p",{class:i(["font-bold",[t.debt>0?"text-red-500":"text-green-500"]])},a(s(r)(t.debt)),3)])]),actions:o(({scope:{row:t}})=>[e("div",z,[l(f,{invoice:t,"accounts-endpoint":c.accountsEndpoint},null,8,["invoice","accounts-endpoint"])])]),_:1},8,["cols","tableData"]))}});export{A as _};
