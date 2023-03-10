import{z as Y,P as H,d as N,m as K,B as Q,f,a as n,b as d,w as u,u as t,t as V,g as _,h as I,a1 as Z,e as C,U as E,A as ee,C as g,o as p,c as P,W as te,Y as oe,E as T,ai as le}from"./app-494b6f90.js";import{p as S}from"./index-1a0c9adf.js";import{z as w,v as ae,k as y,s as se,b as ne}from"./atmosphere-ui-83e2a306.js";import{_ as ie}from"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import{_ as de}from"./InvoiceTotals.vue_vue_type_style_index_0_lang-fb964559.js";import ce from"./InvoiceGrid-4cf1b47f.js";import{u as ue}from"./usePaymentModal-f96f741a.js";import{_ as re}from"./_plugin-vue_export-helper-c27b6911.js";import{f as O}from"./index-c251e33c.js";import"./index-3624ec38.js";import"./index-81f33ca2.js";import"./index-9dc2d84c.js";import"./formatMoney-b7ef7683.js";import"./exact-math.node-f7579f9b.js";import"./IconTrash-bc17a3dd.js";import"./AtTable.vue_vue_type_script_setup_true_lang-3e9914f8.js";import"./customCell-9e9ed16f.js";const x=r=>(te("data-v-dfe42b72"),r=r(),oe(),r),me={class:"w-full py-2 rounded-md section"},pe={class:"section-body"},fe={class:"invoice-body"},ve={class:"invoice-header-details"},be=["src"],_e={key:1,class:"el-icon-plus avatar-uploader-icon"},ye={class:"space-y-4 invoice-details"},xe={class:"invoice-form-row form-group"},Ve=x(()=>n("label",{for:"invoice-description"},"Description: ",-1)),ge={class:"invoice-form-row form-group"},we=x(()=>n("label",{for:"invoice-description"},"Concept: ",-1)),De={class:"flex space-x-4"},Fe={class:"w-6/12 text-left"},he={key:0},Ue={key:0},ke={key:1},Ne=x(()=>n("strong",null,"CIF/NIF:",-1)),Ie={class:"flex justify-between w-6/12 space-x-4 text-left"},Ce={class:"w-full"},Ee={class:"w-full"},Pe={class:"totals-container"},Te={key:0,class:"flex text-left invoice-footer-details"},Se={class:"w-full"},Oe=x(()=>n("label",{for:""}," Footer ",-1)),$e={class:"w-full"},qe=x(()=>n("label",{for:"",class:"block"}," Notes ",-1)),Be={__name:"InvoiceTemplate",props:{type:{type:String,default:"INVOICE"},user:Object,products:Array,clients:Array,invoiceData:[Object,null],availableTaxes:[Array,null],isEditing:Boolean},setup(r,{expose:$}){const v=r,q={expense:{contact:"Proveedor",documentNumber:"Bill Number",orderNumber:"P.O/S.O Number"},invoice:{contact:"Cliente",documentNumber:"No. factura",orderNumber:"No. Orden"}},D=o=>q[v.type.toLowerCase()][o],a=Y({totalValues:{},totals:{subtotalField:"subtotal",totalField:"amount",discountField:"discountTotal",subtotalFormula(o){return o.quantity*o.price},totalFormula(o){return o.quantity*o.price},discountFormula(o){return o.quantity*o.price}},invoice:H({id:null,number:null,concept:null,date:new Date,due_date:new Date,client_id:null,footer:null,notes:null,payments:[],debt:0,status:"DRAFT",created_at:null,updated_at:null,taxes_included:!1}),selectedPayment:null,isPaymentDialogVisible:!1,modals:{email:{value:!1}},activeSections:[],tableData:[],client:null,imageUrl:"",isDraft:N(()=>!a.invoice.status||a.invoice.status.toLowerCase()=="draft"),section:N(()=>v.type.toLowerCase())}),B=o=>{o&&(o.date=S(o.date)||new Date,o.due_date=S(o.due_date)||new Date,Object.keys(o).forEach(e=>{a.invoice[e]=o[e]}),a.client=o.client,a.tableData=o.lines.sort((e,c)=>e.index>c.index?1:-1).map(e=>{var i;const c=(i=e.taxes)!=null&&i.length?le(e.taxes):[];return e.taxes=[...c,{id:"new"}],e})||[])};K(()=>v.invoiceData,o=>{B(o)},{immediate:!0});const{openModal:L}=ue(),M=o=>{L({data:{title:`Pagar ${l.concept}`,payment:o,endpoint:`/invoices/${o.payable_id}/payment/${o.id}`,due:o.amount,account_id:o.account_id,defaultConcept:o.concept}})},F=o=>{const e={...o,items:a.tableData.map((i,m)=>(i.index=m,i.quantity=parseFloat(i.quantity),i.price=parseFloat(i.price),i)).filter(i=>i.concept)};e.date=O(o.date||new Date,"yyyy-MM-dd"),e.due_date=O(o.due_date||e.date,"yyyy-MM-dd");const c=v.type!="INVOICE"?"EXPENSE":v.type;return e.resource_type_id=c,e.type=c,e.concept=e.concept||a.section,e.total=a.totalValues.total,e.discount=a.totalValues.discountTotal,e.subtotal=a.totalValues.subtotal,e.taxes=a.totalValues.taxes,delete e.lines,delete e.paymentDocs,delete e.client,e},h=(o,e,c,i)=>T[o](e,c,{onSuccess(){const m=c.type=="EXPENSE"?"bills":"invoices";T.replace(`/${m}/${c.id}`)}}),R=o=>{const e=F({...a.invoice,type:v.type});o&&(e.status=2);let c="post",i="/invoices";a.invoice.id&&(i=`/invoices/${a.invoice.id}`,c="put"),h(c,i,e)},j=o=>{const e=F(a.invoice);o&&(e.status=2);let c="post",i=`/invoices/${a.invoice.id}/clone`;h(c,i,e).then(m=>{getInvoice(m.id)}).catch(m=>{console.log(m)})},z=o=>{a.imageUrl=URL.createObjectURL(o.raw),a.invoice.logo=o},A=({rowIndex:o,taxes:e})=>{a.tableData[o].taxes=e},{invoice:l,totals:b,tableData:U,totalValues:X,isPaymentDialogVisible:G,isDraft:J}=Q(a);$({saveForm:R,cloneInvoice:j});const W=ee("accountsOptions",[]);return(o,e)=>{const c=g("el-upload"),i=g("ElCollapseItem"),m=g("ElCollapse"),k=g("ElDatePicker");return p(),f("section",me,[n("div",pe,[n("div",fe,[d(m,{modelValue:o.activeSections,"onUpdate:modelValue":e[2]||(e[2]=s=>o.activeSections=s),class:"w-full"},{default:u(()=>[d(i,{title:"Logo, concept and description",name:"header"},{default:u(()=>[n("div",ve,[d(c,{class:"avatar-uploader","v-model":t(l).logo,"show-file-list":!1,"on-change":z,"auto-upload":!1},{default:u(()=>[o.imageUrl?(p(),f("img",{key:0,src:o.imageUrl,class:"avatar"},null,8,be)):(p(),f("i",_e))]),_:1},8,["v-model"]),n("div",ye,[n("div",xe,[Ve,d(t(w),{type:"text",class:"form-control",name:"invoice-description",id:"invoice-description",modelValue:t(l).description,"onUpdate:modelValue":e[0]||(e[0]=s=>t(l).description=s)},null,8,["modelValue"])]),n("div",ge,[we,d(t(w),{type:"text",class:"form-control",name:"invoice-description",id:"invoice-description",modelValue:t(l).concept,"onUpdate:modelValue":e[1]||(e[1]=s=>t(l).concept=s)},null,8,["modelValue"])])])])]),_:1})]),_:1},8,["modelValue"]),n("div",De,[n("div",Fe,[d(t(y),{label:D("contact")},{default:u(()=>[d(t(ae),{modelValue:t(l).client_id,"onUpdate:modelValue":e[3]||(e[3]=s=>t(l).client_id=s),selected:a.client,"onUpdate:selected":e[4]||(e[4]=s=>a.client=s),options:r.clients,label:"fullName","key-track":"id"},null,8,["modelValue","selected","options"])]),_:1},8,["label"]),a.client?(p(),f("div",he,[n("p",null,V(a.client.fullName),1),a.client.country?(p(),f("p",Ue,V(a.client.country),1)):_("",!0),a.client.tax_number?(p(),f("p",ke,[Ne,I(),n("span",null,V(a.client.tax_number),1)])):_("",!0),n("p",null,V(a.client.email),1)])):_("",!0),d(t(y),{label:"Cuenta",class:"w-4/12"},{default:u(()=>[d(t(se),{modelValue:t(l).account_id,"onUpdate:modelValue":e[5]||(e[5]=s=>t(l).account_id=s),selected:t(l).account,"onUpdate:selected":e[6]||(e[6]=s=>t(l).account=s),size:"large","default-expand-all":!0,options:t(W),label:"name","key-track":"id"},null,8,["modelValue","selected","options"])]),_:1})]),n("div",Ie,[n("div",Ce,[d(t(y),{label:"Fecha",class:"flex flex-col"},{default:u(()=>[t(l).date?(p(),P(k,{key:0,modelValue:t(l).date,"onUpdate:modelValue":e[7]||(e[7]=s=>t(l).date=s),size:"large",id:"invoice-date",type:"date",title:"seleccione una fecha",placeholder:"selecciona una fecha"},null,8,["modelValue"])):_("",!0)]),_:1}),d(t(y),{label:"Fecha Limite",class:"mt-2"},{default:u(()=>[t(l).due_date?(p(),P(k,{key:0,modelValue:t(l).due_date,"onUpdate:modelValue":e[8]||(e[8]=s=>t(l).due_date=s),size:"large",id:"invoice-due-date",type:"date",title:"seleccione una fecha",placeholder:"selecciona una fecha"},null,8,["modelValue"])):_("",!0)]),_:1})]),n("div",Ee,[d(t(y),{label:D("documentNumber")},{default:u(()=>[d(t(w),{modelValue:t(l).number,"onUpdate:modelValue":e[9]||(e[9]=s=>t(l).number=s),type:"text",name:"invoice-number",id:"invoice-number"},null,8,["modelValue"])]),_:1},8,["label"]),d(t(y),{label:D("orderNumber"),class:"mt-2"},{default:u(()=>[d(t(w),{modelValue:t(l).order_number,"onUpdate:modelValue":e[10]||(e[10]=s=>t(l).order_number=s),type:"text",name:"invoice-order-number",id:"invoice-order-number"},null,8,["modelValue"])]),_:1},8,["label"])])])])]),d(ce,{tableData:t(U),products:r.products,taxes:o.taxes,"is-editing":r.isEditing,"available-taxes":r.availableTaxes,onTaxesUpdated:A,class:"mt-10"},null,8,["tableData","products","taxes","is-editing","available-taxes"]),n("div",Pe,[n("div",null,[d(t(ne),{label:"Taxes included",modelValue:t(l).taxes_included,"onUpdate:modelValue":e[11]||(e[11]=s=>t(l).taxes_included=s)},null,8,["modelValue"])]),d(de,{tableData:t(U),"subtotal-field":t(b).subtotalField,"discount-field":t(b).discountField,payments:t(l).payments,"total-values":t(X),"total-field":t(b).totalField,subtotalFormula:t(b).subtotalFormula,discountFormula:t(b).discountFormula,totalFormula:t(b).totalFormula,"invoice-taxes":o.invoiceTaxes,"is-tax-included":t(l).taxes_included,onEditPayment:M},Z({_:2},[t(J)?void 0:{name:"add-payment",fn:u(()=>[n("div",null,[d(ie,{class:"w-full",onClick:e[12]||(e[12]=s=>G.value=!0)},{default:u(()=>[I(" Add Payment ")]),_:1})])]),key:"0"}]),1032,["tableData","subtotal-field","discount-field","payments","total-values","total-field","subtotalFormula","discountFormula","totalFormula","invoice-taxes","is-tax-included"])]),t(l).id?(p(),f("div",Te,[n("div",Se,[Oe,C(n("textarea",{name:"",class:"w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400",id:"",cols:"30",rows:"5","onUpdate:modelValue":e[13]||(e[13]=s=>t(l).footer=s)},null,512),[[E,t(l).footer]])]),n("div",$e,[qe,C(n("textarea",{name:"",class:"w-full border border-gray-200 rounded-md focus:outline-none focus:border-gray-400",id:"",cols:"30",rows:"5","onUpdate:modelValue":e[14]||(e[14]=s=>t(l).notes=s)},null,512),[[E,t(l).notes]])])])):_("",!0)])])}}},ot=re(Be,[["__scopeId","data-v-dfe42b72"]]);export{ot as default};
