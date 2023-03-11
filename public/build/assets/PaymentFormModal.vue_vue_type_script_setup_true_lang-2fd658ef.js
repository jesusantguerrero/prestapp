import{_ as B}from"./close-40de6817.js";import{l as H,r as k,m as f,d as D,c as v,w as d,u as m,$ as T,o as y,a as r,t as z,b as u,h as g,a0 as A,e as S,U as j,g as I,N as R,M as x}from"./app-494b6f90.js";import{F as q,z as G,s as L}from"./atmosphere-ui-83e2a306.js";import{_ as U}from"./AppButton.vue_vue_type_script_setup_true_lang-6731c3fc.js";import O from"./PaymentGrid-a2bc0bdb.js";import{M as J}from"./mathHelper-d6bc48cd.js";import{p as _}from"./constants-0a903a05.js";import{_ as K}from"./AccountSelect.vue_vue_type_script_setup_true_lang-93902e28.js";import{_ as i}from"./AppFormField.vue_vue_type_style_index_0_lang-2eb2a149.js";import{f as F}from"./index-c251e33c.js";const Q={class:"border-b -mx-6 -mt-6 -mr-10 bg-secondary/80 text-white py-4 px-4 flex items-center justify-between"},W={class:"font-bold text-xl"},X={class:""},Y={class:"flex space-x-4"},Z={class:"flex space-x-4"},ee={class:"space-x-2 dialog-footer flex justify-end"},ie=H({__name:"PaymentFormModal",props:{modelValue:{type:Boolean},defaultConcept:null,defaultAmount:null,payment:null,due:null,endpoint:null,title:null,accountsEndpoint:{default:"/api/accounts"}},emits:["update:modelValue","saved"],setup(p,{emit:c}){const n=p,V={amount:0,account_id:""},t=k(C()),w=()=>{t.value=C()};function C(){var a,e;return{...V,concept:n.defaultConcept,amount:n.due,payment_method_id:_[0].id,paymentMethod:_[0],payment_date:new Date,documents:((a=n.payment)==null?void 0:a.documents)??[],...(e=n.payment)!=null&&e.id?n.payment:{}}}f(()=>n.defaultConcept,a=>{t.value.concept=a},{immediate:!0}),f(()=>n.due,a=>{t.value.id||(t.value.amount=a)},{immediate:!0}),f(()=>n.payment,a=>{a&&w()},{deep:!0,immediate:!0});const M=D(()=>{var a;return(a=t.value.documents)==null?void 0:a.reduce((e,l)=>J.sum(e,l.payment),0)});f(()=>M.value,a=>{R(()=>{t.value.amount=a})});const P=D(()=>{var a,e;return(e=(a=t.value)==null?void 0:a.documents)==null?void 0:e.length}),s=k(!1);function $(){var e;if(s.value)return;if(!t.id){N();return}s.value=!0;const a={payment_date:F(t.value.payment_date||new Date,"yyyy-MM-dd"),amount:t.value.amount,concept:t.value.concept,payment_method_id:t.value.payment_method,account_id:t.value.account_id,reference:t.value.reference,notes:t.value.notes,documents:(e=t.value.documents)==null?void 0:e.filter(l=>l.payment)};axios.post(n.endpoint,a).then(()=>{b(!0),c("saved")}).catch(l=>{console.log(l),x({type:"error",message:l.response?l.response.data.status.message:"Ha ocurrido un error"})}).finally(()=>{s.value=!1})}function N(){var e;if(!t.value.amount){x({type:"error",message:"should specify an amount"});return}s.value=!0;const a={resource_id:n.resourceId,payment_date:F(t.value.payment_date||new Date,"yyyy-MM-dd"),amount:t.value.amount,concept:t.value.concept,payment_method_id:t.value.payment_method,account_id:t.value.account_id,reference:t.value.reference,notes:t.value.notes,documents:(e=t.value.documents)==null?void 0:e.filter(l=>l.payment)};axios.post(n.endpoint,a).then(()=>{b(!0),c("saved")}).catch(l=>{console.log(l),x({type:"error",message:l.response?l.response.data.status.message:"Ha ocurrido un error"})}).finally(()=>{s.value=!1})}function E(){var e;const a=n.endpoint?`${n.endpoint}`:`/invoice/${n.payment.resource_id}/payments/${(e=n.payment)==null?void 0:e.id}`;axios.delete(a).then(()=>{c("saved"),b(!0)}).catch(l=>{notify({type:"error",message:l.response?l.response.data.status.message:"Ha ocurrido un error"})})}function b(a){t.value={...V,concept:n.defaultConcept},a&&h(!1)}function h(a){c("update:modelValue",a)}return(a,e)=>{const l=B;return y(),v(m(T),{class:"rounded-lg overflow-hidden",onOpen:e[13]||(e[13]=o=>w()),"model-value":p.modelValue,"onUpdate:modelValue":e[14]||(e[14]=o=>a.$emit("update:modelValue",o))},{header:d(()=>[r("header",Q,[r("h4",W,z(p.title??p.defaultConcept),1),r("button",{class:"hover:text-danger",onClick:e[0]||(e[0]=o=>a.close())},[u(l)])])]),footer:d(()=>[r("div",ee,[u(m(q),{disabled:s.value,onClick:e[10]||(e[10]=o=>h(!1)),class:"bg-white border rounded-md text-gray"},{default:d(()=>[g(" Cancel ")]),_:1},8,["disabled"]),t.value.id?(y(),v(U,{key:0,class:"text-white bg-blue-500",onClick:e[11]||(e[11]=o=>E()),disabled:s.value},{default:d(()=>[g(" Delete ")]),_:1},8,["disabled"])):(y(),v(U,{key:1,variant:"secondary",onClick:e[12]||(e[12]=o=>$()),processing:s.value,disabled:s.value,loading:s.value},{default:d(()=>[g(" Efectuar pago ")]),_:1},8,["processing","disabled","loading"]))])]),default:d(()=>[r("div",X,[r("section",Y,[u(i,{class:"w-full text-left",label:"Concepto",modelValue:t.value.concept,"onUpdate:modelValue":e[1]||(e[1]=o=>t.value.concept=o)},null,8,["modelValue"]),u(i,{class:"w-full text-left",label:"Referencia",modelValue:t.value.reference,"onUpdate:modelValue":e[2]||(e[2]=o=>t.value.reference=o),rounded:""},null,8,["modelValue"]),u(i,{class:"w-full text-left",label:"Monto Recibido"},{default:d(()=>[u(m(G),{class:"form-control","number-format":"","onUpdate:modelValue":e[3]||(e[3]=o=>t.value.amount=o),"model-value":t.value.amount,rounded:"",disabled:m(M),required:""},null,8,["model-value","disabled"])]),_:1})]),r("section",Z,[u(i,{class:"w-5/12 mb-5 text-left",label:"Cuenta de Pago"},{default:d(()=>[u(K,{endpoint:p.accountsEndpoint,modelValue:t.value.paymentAccount,"onUpdate:modelValue":[e[4]||(e[4]=o=>t.value.paymentAccount=o),e[5]||(e[5]=o=>t.value.account_id=o==null?void 0:o.id)],placeholder:"Selecciona una cuenta"},null,8,["endpoint","modelValue"])]),_:1}),u(i,{class:"w-3/12 mb-5 text-left",label:"Metodo de Pago"},{default:d(()=>[u(m(L),{modelValue:t.value.payment_method_id,"onUpdate:modelValue":e[6]||(e[6]=o=>t.value.payment_method_id=o),selected:t.value.paymentMethod,"onUpdate:selected":e[7]||(e[7]=o=>t.value.paymentMethod=o),options:m(_),placeholder:"Forma pago",class:"w-full",label:"name","key-track":"id"},null,8,["modelValue","selected","options"])]),_:1}),u(i,{label:"Fecha de pago",class:"w-3/12"},{default:d(()=>[u(m(A),{modelValue:t.value.payment_date,"onUpdate:modelValue":e[8]||(e[8]=o=>t.value.payment_date=o),size:"large",class:"w-full",rounded:""},null,8,["modelValue"])]),_:1})]),u(i,{class:"w-full text-left",label:"Notes"},{default:d(()=>[S(r("textarea",{class:"w-full px-3 py-2 border border-gray-200 rounded-md focus:outline-none focus:border-gray-400","onUpdate:modelValue":e[9]||(e[9]=o=>t.value.notes=o),cols:"3",rows:"3"},null,512),[[j,t.value.notes]])]),_:1}),m(P)?(y(),v(O,{key:0,"table-data":t.value.documents,"available-taxes":[]},null,8,["table-data"])):I("",!0)])]),_:1},8,["model-value"])}}});export{ie as _};