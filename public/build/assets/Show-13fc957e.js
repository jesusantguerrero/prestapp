import{k as q,d as w,r as y,c as _,w as l,u as o,o as m,b as i,E as u,h as d,a as e,t as s,f as h,j as P,n as G,y as I,F as D,g as V,V as R}from"./app-b9464164.js";import{_ as j}from"./AppLayout.vue_vue_type_script_setup_true_lang-0afa9cc5.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0d2503f0.js";import{_ as b}from"./AppButton.vue_vue_type_script_setup_true_lang-89aa5f09.js";import{_ as z}from"./AppSectionHeader-611c7f77.js";import{f as $}from"./index-8305cd09.js";import{_ as J}from"./PropertySectionNav.vue_vue_type_script_setup_true_lang-e2241f15.js";import{_ as O}from"./InvoiceCard.vue_vue_type_script_setup_true_lang-b6bbe59b.js";import{_ as U}from"./PaymentFormModal.vue_vue_type_script_setup_true_lang-a1e2938d.js";import{_ as E}from"./WelcomeWidget.vue_vue_type_script_setup_true_lang-e3b9f9b9.js";import{f as F}from"./formatMoney-b7ef7683.js";import"./atmosphere-ui-8913659c.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./Modal.vue_vue_type_script_setup_true_lang-74361783.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-d3f16147.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-89ccd5ed.js";/* empty css                                                   */import"./index-c251e33c.js";import"./index-81f33ca2.js";import"./usePaymentModal-23c75da0.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-3f2645eb.js";import"./close-45140fa9.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-d4d45920.js";import"./clientInteractions-6c951eed.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-b284cd8c.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-b788b25f.js";import"./AppSearchFilters-93f60e1e.js";import"./menus-2c7b33a0.js";import"./constants-f49bdf8c.js";import"./PaymentGrid-88a06cd9.js";/* empty css                                                                    */import"./mathHelper-44355b55.js";import"./exact-math.node-e9867222.js";import"./constants-0a903a05.js";const H={class:"p-5 mt-16 md:mt-8"},K={class:"w-full px-5 pt-10 pb-2 mb-5 space-y-5 text-gray-600 bg-white rounded-b-md"},Q={class:"flex space-x-2"},W={class:"flex flex-col md:flex-row w-full md:space-x-8 rounded-t-none border-t-none"},X={class:"w-full md:w-9/12 space-y-2"},Y={class:"py-4 space-y-2"},Z={class:"hover:bg-base-lvl-1 cursor-pointer py-2"},ee={class:"w-full md:w-3/12 mt-4 md:mt-0 p-4 space-y-2 rounded-md bg-base-lvl-3"},te={class:"flex space-x-4"},ne={class:"py-4 mt-8 space-y-2"},ae={key:0,class:"text-sm"},oe={class:"font-bold text-green-500"},se={class:"font-bold text-primary"},re=["href"],Re=q({__name:"Show",props:{rents:null,currentTab:{default:"summary"}},setup(t){const f=t,N={summary:"Detalles",transactions:"Pagos"},g=w(()=>{var n;return f.rents.client.names+" "+((n=f.rents.client)==null?void 0:n.lastnames)}),T=w(()=>`${g.value.split(",")[0]}`),x=y(!1),c=y(null),A=n=>{c.value={...n,amount:parseFloat(n.debt)||n.total,id:void 0,invoice_id:n.id},x.value=!0},B=w(()=>c.value&&`Pago ${f.rents.id} pago #${c.value.installment_id}`),v=y(""),k=y(),M=n=>{v.value=`/invoices/${n.id}/print`,console.log(v.value,n),R(()=>{k.value.click(),v.value=""})},S=(n,a)=>{switch(n){case"payment":A(a);break;case"download":M(a);break}},C=()=>{u.reload()},L=()=>{u.post(`/rents/${f.rents.id}/generate-next-invoice`,{onSuccess(){C()}})};return(n,a)=>(m(),_(j,{title:o(T)},{header:l(()=>[i(J,null,{actions:l(()=>[i(b,{variant:"neutral",onClick:a[0]||(a[0]=r=>o(u).visit(n.route("rents.create")))},{default:l(()=>[d(" Crear Gasto ")]),_:1}),i(b,{variant:"neutral",onClick:a[1]||(a[1]=r=>o(u).visit(n.route("rents.create")))},{default:l(()=>[d(" Crear Mora ")]),_:1}),i(b,{variant:"inverse-secondary",onClick:a[2]||(a[2]=r=>o(u).visit(n.route("rents.create")))},{default:l(()=>[d(" Crear Cargo Extra ")]),_:1})]),_:1})]),default:l(()=>[e("main",H,[i(z,{name:"Contrato de Alquiler a",class:"px-5 bg-white border-2 border-white rounded-md rounded-b-none",resource:t.rents,title:`${o(g)}`,"hide-action":"",onCreate:a[3]||(a[3]=r=>o(u).visit("/loans/create"))},null,8,["resource","title"]),e("div",K,[e("div",null,"Alquiler #"+s(t.rents.id)+" para "+s(o(g)),1),e("div",Q,[(m(),h(D,null,P(N,(r,p)=>i(o(I),{class:G(["px-2 py-1 transition rounded-md cursor-pointer bg-gray-50 hover:bg-gray-200",{"bg-gray-300":p==t.currentTab}]),key:p,href:`/rents/${t.rents.id}?current-tab=${p}`,replace:""},{default:l(()=>[d(s(r),1)]),_:2},1032,["class","href"])),64))])]),e("section",W,[e("article",X,[i(E,{message:"Detalles de contrato",class:"w-full text-body-1"},{content:l(()=>[e("section",Y,[e("p",null," Mensualidad: "+s(t.rents.amount),1),e("p",null," Fecha de Inicio: "+s(o($)(t.rents.date)),1),e("p",null," Proximo pago: "+s(o($)(t.rents.next_invoice_date)),1),e("p",null," Estatus: "+s(t.rents.status),1),e("p",Z," Deposito "+s(o(F)(t.rents.deposit)),1)])]),_:1}),i(E,{message:"Detalles de propiedad",class:"w-full text-body-1"},{content:l(()=>[e("section",null,[e("article",null,[e("button",null,s(t.rents.property.owner.display_name),1)]),e("article",null,[e("button",null,s(t.rents.property.name),1)])])]),_:1})]),e("article",ee,[e("section",te,[i(b,{class:"w-full",variant:"secondary",onClick:L},{default:l(()=>[d(" Generar proximo pago ")]),_:1})]),e("section",ne,[t.rents.transaction?(m(),h("div",ae,[d(s(t.rents.transaction.description)+" ",1),e("span",oe,s(o(F)(t.rents.transaction.total)),1),d(" en "),e("span",se,s(o($)(t.rents.date)),1)])):V("",!0),(m(!0),h(D,null,P(t.rents.invoices,r=>(m(),_(O,{invoice:r,actions:{payment:{label:"Registrar Pago"},send:{label:"Enviar Correo"},download:{label:"Descargar PDF"},view:{label:"Ver factura"},delete:{label:"Eliminar Factura"}},onAction:p=>S(p,r)},null,8,["invoice","onAction"]))),256))])])]),c.value?(m(),_(U,{key:0,modelValue:x.value,"onUpdate:modelValue":a[4]||(a[4]=r=>x.value=r),title:"Pagar Renta",payment:c.value,endpoint:`/rents/${t.rents.id}/invoices/${c.value.invoice_id}/pay`,due:c.value.amount,"default-concept":o(B),onSaved:a[5]||(a[5]=r=>C())},null,8,["modelValue","payment","endpoint","due","default-concept"])):V("",!0),e("a",{href:v.value,download:"",target:"_blank",ref_key:"invoiceLink",ref:k,type:"hidden"},null,8,re)])]),_:1},8,["title"]))}});export{Re as default};