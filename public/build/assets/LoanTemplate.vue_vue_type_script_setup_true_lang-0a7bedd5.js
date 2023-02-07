import{o as f,f as y,a as e,k as C,al as k,d as P,c as V,w as i,u as s,b as a,E as v,h as d,t as o,j as F,n as N,y as T,F as I,s as L,g as B,i as D}from"./app-de9d56f0.js";import{_ as A}from"./sharp-payment-2bc236b7.js";import{i as m}from"./atmosphere-ui-55a1c253.js";import{_ as H}from"./AppLayout.vue_vue_type_script_setup_true_lang-5b8fe57e.js";import"./AppLayout.vue_vue_type_style_index_0_lang-1b19aa61.js";import{_ as u}from"./AppButton.vue_vue_type_script_setup_true_lang-81087974.js";import{_ as S}from"./AppSectionHeader-191edb46.js";import z from"./LoanSectionNav-be6b3382.js";import{f as r}from"./formatMoney-b7ef7683.js";import{_ as E}from"./AgreementFormModal.vue_vue_type_script_setup_true_lang-8b9d8191.js";import{f as $}from"./index-8305cd09.js";const O={viewBox:"0 0 24 24",width:"1.2em",height:"1.2em"},R=e("path",{fill:"currentColor",d:"M21.71 8.71c1.25-1.25.68-2.71 0-3.42l-3-3c-1.26-1.25-2.71-.68-3.42 0L13.59 4H11C9.1 4 8 5 7.44 6.15L3 10.59v4l-.71.7c-1.25 1.26-.68 2.71 0 3.42l3 3c.54.54 1.12.74 1.67.74c.71 0 1.36-.35 1.75-.74l2.7-2.71H15c1.7 0 2.56-1.06 2.87-2.1c1.13-.3 1.75-1.16 2-2C21.42 14.5 22 13.03 22 12V9h-.59l.3-.29M20 12c0 .45-.19 1-1 1h-1v1c0 .45-.19 1-1 1h-1v1c0 .45-.19 1-1 1h-4.41l-3.28 3.28c-.31.29-.49.12-.6.01l-2.99-2.98c-.29-.31-.12-.49-.01-.6L5 15.41v-4l2-2V11c0 1.21.8 3 3 3s3-1.79 3-3h7v1m.29-4.71L18.59 9H11v2c0 .45-.19 1-1 1s-1-.55-1-1V8c0-.46.17-2 2-2h3.41l2.28-2.28c.31-.29.49-.12.6-.01l2.99 2.98c.29.31.12.49.01.6Z"},null,-1),q=[R];function J(t,_){return f(),y("svg",O,q)}const U={name:"mdi-handshake-outline",render:J},Z={class:"mt-16"},G={class:"w-full px-6 pb-2 mb-5 space-y-5 text-gray-600 bg-white border-gray-200 shadow-md rounded-b-md"},K={class:"flex justify-between space-x-4"},Q={class:"w-full"},W={class:"flex justify-between w-full"},X=e("span",null," No.: ",-1),Y={class:"font-bold"},ee={class:"flex justify-between w-full"},te=e("span",null," Fecha: ",-1),se={class:"font-bold"},ae={class:"flex justify-between w-full"},ne=e("span",null," Monto Prestado/Total a pagar: ",-1),oe={class:"font-bold"},le={class:"w-full"},ie={class:"flex justify-between w-full"},ce=e("span",null," Interes: ",-1),de={class:"font-bold"},re={class:"flex justify-between w-full"},ue=e("span",null," Pago/Modo: ",-1),me={class:"font-bold"},fe={class:"flex justify-between w-full"},_e=e("span",null," Dias de gracia: ",-1),he={class:"font-bold"},pe={class:"flex justify-between w-full"},ye=e("span",null," Monto pagado: ",-1),be={class:"font-bold"},ge={class:"flex space-x-2"},we={class:"flex w-full space-x-8 rounded-t-none border-t-none"},xe={class:"w-9/12 space-y-2 overflow-auto"},ve={class:"flex space-x-4"},$e={class:"w-3/12 p-4 space-y-2 overflow-hidden border rounded-md shadow-md bg-base-lvl-3"},Me={class:"grid grid-cols-1 gap-2"},je={class:"py-4 mt-8 space-y-2"},Ce={key:0,class:"w-full px-2 py-4 rounded-md bg-base-lvl-2 text-body-1"},ke=e("header",{class:"font-bold"},"Razon de cancelacion",-1),Se=C({__name:"LoanTemplate",props:{loans:null,currentTab:{default:""},stats:null},setup(t){const _=t,[h,b]=k(),g=P(()=>{var l;return(l=_.loans.client)==null?void 0:l.fullName}),M={"":"Detalles",payments:"Pagos",agreements:"Acuerdos de pago"},j=()=>{selectedPayment.value={amount:0,id:void 0,documents:_.loans.installments.map(l=>({name:`Pago ${l.id}`,...l,amount:parseFloat(l.amount_due),payment:0})).filter(l=>(console.log(l),parseFloat(l.amount||0)))},isPaymentModalOpen.value=!0};return(l,n)=>{const w=A,x=U;return f(),V(H,{title:`Prestamo ${s(g)}`},{header:i(()=>[a(z,null,{actions:i(()=>[a(u,{variant:"inverse",onClick:n[0]||(n[0]=c=>s(v).visit(`/loans/${t.loans.id}/edit`))},{default:i(()=>[d(" Editar prestamo ")]),_:1})]),_:1})]),default:i(()=>[e("main",Z,[a(S,{name:"Prestamo a",class:"px-5 border-2 border-white rounded-md rounded-b-none shadow-md",resource:t.loans,title:s(g),"hide-action":"",onCreate:n[1]||(n[1]=c=>s(v).visit("/loans/create"))},null,8,["resource","title"]),e("section",G,[e("header",K,[e("section",Q,[e("p",W,[X,e("span",Y,o(t.loans.id),1)]),e("p",ee,[te,e("span",se,o(s($)(t.loans.date)),1)]),e("p",ae,[ne,e("span",oe,o(s(r)(t.loans.amount))+" / "+o(s(r)(t.loans.total)),1)])]),e("section",le,[e("p",ie,[ce,e("span",de,o(t.loans.interest_rate)+" %",1)]),e("p",re,[ue,e("span",me,o(t.loans.payment)+"/ "+o(t.loans.frequency),1)]),e("p",fe,[_e,e("span",he,o(t.loans.grace_days)+" Dias",1)]),e("p",pe,[ye,e("span",be,o(s(r)(t.loans.amount_paid)),1)])])]),e("footer",ge,[(f(),y(I,null,F(M,(c,p)=>a(s(T),{class:N(["px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200",{"bg-primary/10 text-primary font-bold":p==t.currentTab}]),key:p,href:`/loans/${t.loans.id}/${p}`,replace:""},{default:i(()=>[d(o(c),1)]),_:2},1032,["class","href"])),64))])]),e("section",we,[e("article",xe,[e("section",ve,[a(s(m),{class:"w-full bg-white border h-28 text-body-1",title:"Capital pendiente",value:s(r)(t.stats.outstandingPrincipal)},null,8,["value"]),a(s(m),{class:"w-full bg-white border h-28 text-body-1",title:"Interes pendiente",value:s(r)(t.stats.outstandingInterest)},null,8,["value"]),a(s(m),{class:"w-full bg-white border h-28 text-body-1",title:"Mora pendiente",value:s(r)(t.stats.outstandingFee)},null,8,["value"]),a(s(m),{class:"w-full bg-white border h-28 text-body-1",title:"Monto pendiente",value:s(r)(t.stats.outstandingTotal)},null,8,["value"])]),L(l.$slots,"default")]),e("article",$e,[e("section",Me,[a(u,{onClick:n[2]||(n[2]=c=>j()),class:"flex items-center justify-center text-sm"},{default:i(()=>[a(w,{class:"mr-2"}),d(" Recibo Multiple ")]),_:1}),a(u,{variant:"primary",class:"flex items-center justify-center text-sm"},{default:i(()=>[a(w,{class:"mr-2"}),d(" Saldar prestamo ")]),_:1}),a(u,{onClick:n[3]||(n[3]=c=>s(b)()),variant:"secondary",class:"flex items-center justify-center text-sm"},{default:i(()=>[a(x,{class:"mr-1"}),d(" Acuerdo de pago ")]),_:1}),a(u,{variant:"secondary",class:"flex items-center justify-center w-full text-sm"},{default:i(()=>[a(x,{class:"mr-1"}),d(" Finalizar ")]),_:1})]),e("section",je,[t.loans.cancelled_at?(f(),y("div",Ce,[e("h4",null,"Cancelado en "+o(s($)(t.loans.cancelled_at,"dd MMMM yyyy")),1),ke,d(" "+o(t.loans.cancel_reason),1)])):B("",!0)])])])]),a(E,{modelValue:s(h),"onUpdate:modelValue":[n[4]||(n[4]=c=>D(h)?h.value=c:null),n[5]||(n[5]=c=>s(b)(!1))],loan:t.loans},null,8,["modelValue","loan"])]),_:3},8,["title"])}}});export{Se as _};