import{k as I,r as d,d as N,C as u,o as p,f as _,c as S,w as s,b,F as g,j as V,h as F,t as T,g as B,a as m,Q as k}from"./app-fef21123.js";import{u as M}from"./usePaymentModal-a07a875e.js";const $=m("button",{class:"px-5 py-2 rounded-md hover:bg-base-lvl-2"},[m("i",{class:"fa fa-ellipsis-h"})],-1),L=["href"],G=I({__name:"InvoicePaymentOptions",props:{invoice:null,accountsEndpoint:null},setup(e){const l=e,{openModal:y}=M(),f={payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},t=d(null),C=N(()=>t.value&&`Pago ${l.invoice.concept}`),w=n=>{t.value={...n,amount:parseFloat(n.debt)||n.total,id:void 0,invoice_id:n.id},k(()=>{var o,a;y({data:{title:`Pagar ${n.concept}`,payment:t.value,endpoint:`/invoices/${(o=l.invoice)==null?void 0:o.id}/payment`,due:(a=t.value)==null?void 0:a.amount,defaultConcept:C.value,accountsEndpoint:l.accountsEndpoint}})})},r=d(""),v=d(),E=n=>{r.value=`/invoices/${n.id}/print`,k(()=>{v.value.click(),r.value=""})},P=(n,o)=>{switch(n){case"payment":w(o);break;case"download":E(o);break}};return(n,o)=>{const a=u("ElDropdownItem"),h=u("ElDropdownMenu"),D=u("ElDropdown");return p(),_(g,null,[f?(p(),S(D,{key:0,onCommand:o[0]||(o[0]=i=>P(i,e.invoice))},{dropdown:s(()=>[b(h,null,{default:s(()=>[(p(),_(g,null,V(f,(i,x)=>b(a,{command:x},{default:s(()=>[F(T(i.label),1)]),_:2},1032,["command"])),64))]),_:1})]),default:s(()=>[$]),_:1})):B("",!0),m("a",{href:r.value,target:"_blank",ref_key:"invoiceLink",ref:v,type:"hidden"},null,8,L)],64)}}}),A={paid:"Pagado",unpaid:"Pendiente"},c={unpaid:{color:"text-body-1",iconClass:"fa fa-circle"},paid:{color:"text-success",iconClass:"fa fa-check"},PENDING:{color:"text-info",iconClass:""}},Q=e=>A[e]||e,R=e=>c[e]&&c[e].iconClass,U=e=>c[e]&&c[e].color;export{G as _,Q as a,U as b,R as g};
