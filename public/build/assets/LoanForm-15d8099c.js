import{E as k,k as A,z as Y,l as O,d as D,r as E,ag as S,c as L,w as n,u as d,C as z,o as v,b as a,h as V,t as B,a as m,g as R,f as H}from"./app-b9464164.js";import{V as b,a as W,R as K,N as j}from"./atmosphere-ui-8913659c.js";import{_ as G}from"./AppLayout.vue_vue_type_script_setup_true_lang-0afa9cc5.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0d2503f0.js";import{_ as T}from"./AppButton.vue_vue_type_script_setup_true_lang-89aa5f09.js";import{_ as J}from"./InstallmentTable.vue_vue_type_script_setup_true_lang-b30664d9.js";import Q from"./LoanSectionNav-102d6475.js";import{L as y,l as X}from"./constants-0a903a05.js";import{f as Z}from"./index-c251e33c.js";import{p as ee,a as N,f as x}from"./index-8305cd09.js";import{a as U}from"./index-17a9d477.js";import{r as te,t as le}from"./index-81f33ca2.js";import{M}from"./mathHelper-44355b55.js";import{_ as ae}from"./BaseSelect.vue_vue_type_script_setup_true_lang-89ccd5ed.js";/* empty css                                                   */import{_ as C}from"./FormSection.vue_vue_type_script_setup_true_lang-e70cc900.js";import{_ as se}from"./LoanSummary.vue_vue_type_script_setup_true_lang-775c12ad.js";import{_ as ne}from"./AccountSelect.vue_vue_type_script_setup_true_lang-d3f16147.js";import{a as c}from"./ClientForm.vue_vue_type_script_setup_true_lang-d4d45920.js";import"./clientInteractions-6c951eed.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-a1e2938d.js";import"./PaymentGrid-88a06cd9.js";/* empty css                                                                    */import"./Modal.vue_vue_type_script_setup_true_lang-74361783.js";import"./usePaymentModal-23c75da0.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-3f2645eb.js";import"./close-45140fa9.js";import"./formatMoney-b7ef7683.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-b788b25f.js";import"./AppSearchFilters-93f60e1e.js";import"./sharp-payment-ccd5d474.js";import"./BaseTable.vue_vue_type_style_index_0_lang-d3c40c15.js";import"./customCell-57b73ca8.js";import"./menus-2c7b33a0.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-b284cd8c.js";import"./exact-math.node-e9867222.js";import"./IconCoins-55131ff2.js";function oe(o){te(1,arguments);var l=le(o),e=l.getMonth();return l.setFullYear(l.getFullYear(),e+1,0),l.setHours(23,59,59,999),l}const ie=o=>{let l="/loans",e="post";return o.id&&(l=`/loans/${o.id}`,e="put"),k[e].bind(k,l)},re=(o,l)=>{const e=ie(o);return console.log(e),new Promise((u,f)=>e({...o,installments:l},{onSuccess(i){u(i)},onError(i){console.log(i),f(i)}}))},de=(o,l)=>{const u={[y.WEEKLY]:{method:N,interval:7},[y.BIWEEKLY]:{method:N,interval:15},[y.SEMIMONTHLY]:{method:ue,interval:1},[y.MONTHLY]:{method:U,interval:1}}[l];return Z(u.method(ee(o),u.interval),"yyyy-MM-dd")},ue=(o,l)=>o.getDate()==15?oe(o):N(o,15);class me{constructor({startDate:l,capital:e,interestMonthlyRate:u,count:f,frequency:i},p=de){this.payments=[],this.totalDebt=0,this.totalInterest=0,this.totalCapital=0,this.startDate=l,this.capital=e,this.interestMonthlyRate=u/100,this.count=f,this.frequency=i,this.nextDateCalculator=p,this.payment=this.calculatePayment(),this.generateAmortizationTable()}calculatePayment(){const l=this.getFrequencyRate();return M.loanPayment({interestRate:l,capital:this.capital,installments:this.count})}getMonthlyPayment(){return this.payment.toFixed(2)}generateAmortizationTable(){let l=0,e=0,u=this.capital,f=this.startDate;const i=this.getFrequencyRate();for(let p=0;p<this.count;p++){l=M.mulWithRounding(u,i),e=M.subWithRounding(this.payment,l);const h=M.subWithRounding(u,e);this.payments.push({number:p+1,due_date:f,days:0,amount:this.payment,amount_paid:0,amount_due:this.payment,interest:l,principal:e,fees:0,late_fee:0,principal_paid:0,interest_paid:0,fees_paid:0,penalty_paid:0,initial_balance:u,final_balance:h}),this.totalCapital+=e,this.totalDebt+=this.payment,this.totalInterest+=l,u=h,f=this.nextDateCalculator(f,this.frequency)}}getInstallment(l){return this.payments[l-1]}getLastPayment(){return this.payments[this.payments.length-1]}getFrequencyRate(){const l={[y.WEEKLY]:4,[y.BIWEEKLY]:2,[y.SEMIMONTHLY]:2,[y.MONTHLY]:1};return this.interestMonthlyRate/l[this.frequency]}}const ce=({interest_rate:o,amount:l,repayment_count:e,first_installment_date:u,frequency:f})=>new me({startDate:u,frequency:f,interestMonthlyRate:o,capital:l,count:e}),fe={class:"flex flex-col md:flex-row w-full pb-10 mt-24 md:mt-16 md:space-x-4"},pe={class:"w-full md:w-8/12 p-4 bg-white rounded-md shadow-md text-body-1"},_e={class:"flex flex-col md:flex-row md:space-x-4"},ye=m("div",null,null,-1),he={class:"flex flex-col md:flex-row md:space-x-4"},be={class:"flex flex-col md:flex-row w-full md:space-x-4"},ge={class:"flex space-x-4"},we={class:"flex space-x-4"},ve={class:"w-full md:w-4/12 mt-4 md:mt-0 rounded-md bg-white shadow-md relative overflow-hidden grid gap-4 grid-cols-1 grid-rows-[1fr_50px]"},Ve={class:"w-full px-4 overflow-hidden text-body-1"},xe=m("header",{class:"py-4 font-bold"},"Resumen de prestamo",-1),Me={key:0,class:"mt-4"},Ce=m("h4",{class:"text-xl font-bold"},"Tabla de amortización",-1),De={class:"flex justify-between w-full px-4 py-1"},mt=A({__name:"LoanForm",props:{loans:null,clients:null},setup(o){const l=o,e=Y({id:null,client_id:null,client:void 0,amount:0,repayment_count:0,interest_rate:0,frequency:"MONTHLY",disbursement_date:new Date,first_installment_date:U(new Date,1),grace_days:0,late_fee:0,installments_paid:0,closing_fees:0,category_id:null,source_type:null,source_account_id:null,sourceAccount:null});O(()=>l.loans,r=>{var t;r&&(Object.keys(e).forEach(_=>{e[_]=r[_]||e[_]}),e.client_id=((t=l.loans.client)==null?void 0:t.id)??1,e.client=l.loans.client)},{immediate:!0,deep:!0});const u=D(()=>{var r,t;return(r=l.loans)!=null&&r.id?`Prestamo ${(t=l.loans.client)==null?void 0:t.fullName}`:"Crear Prestamo"}),f=D(()=>{var r;return(r=l.loans)!=null&&r.id?"Guardar prestamo":"Registrar Prestamo"}),i=E(null);S(()=>{const r=x(e.first_installment_date,"yyyy-MM-dd");e.amount&&e.interest_rate&&e.repayment_count&&(i.value=ce({interest_rate:e.interest_rate,amount:e.amount,repayment_count:e.repayment_count,first_installment_date:r,frequency:e.frequency}))});const p=D(()=>{var r,t;return i.value&&((t=(r=i.value)==null?void 0:r.payments)==null?void 0:t.length)}),h=E(!1),$=()=>{var t,_;const r={...e,date:x(e.disbursement_date,"yyyy-MM-dd"),disbursement_date:x(e.disbursement_date,"yyyy-MM-dd"),first_installment_date:x(e.first_installment_date,"y-M-d"),client_id:e.client.id,source_type:(t=e.sourceType)==null?void 0:t.id,source_account_id:(_=e.sourceAccount)==null?void 0:_.id};h.value=!0,re(r,i.value.payments).then(()=>{close(),q()}).catch(g=>{console.log(g)}).finally(()=>{h.value=!1})},w=E(!1),q=()=>{k.visit("/loans")};return(r,t)=>{const _=z("ElDatePicker");return v(),L(G,{title:d(u)},{header:n(()=>[a(Q,null,{actions:n(()=>[a(T,{variant:"secondary",class:"hidden md:flex",onClick:t[0]||(t[0]=g=>$()),disabled:!d(p)},{default:n(()=>[V(B(d(f)),1)]),_:1},8,["disabled"])]),_:1})]),default:n(()=>{var g,F,I,P;return[m("main",fe,[m("section",pe,[a(C,{title:"Datos del cliente",class:"w-full","section-class":"w-full"},{default:n(()=>[a(c,{label:"Cliente",class:"w-full"},{default:n(()=>[a(ae,{modelValue:e.client,"onUpdate:modelValue":t[1]||(t[1]=s=>e.client=s),"track-by":"id",endpoint:"/api/clients",placeholder:"Selecciona un cliente",label:"display_name",class:"w-full"},null,8,["modelValue"])]),_:1})]),_:1}),a(C,{title:"Terminos de prestamo","section-class":"w-full"},{default:n(()=>[m("section",_e,[a(c,{label:"Monto a prestar",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.amount,"onUpdate:modelValue":t[2]||(t[2]=s=>e.amount=s),"number-format":"",rounded:""},null,8,["modelValue"])]),_:1}),a(c,{label:"Interés mensual",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.interest_rate,"onUpdate:modelValue":t[3]||(t[3]=s=>e.interest_rate=s),"number-format":"",max:"100",rounded:""},{suffix:n(()=>[ye]),_:1},8,["modelValue"])]),_:1}),a(c,{label:"Cuotas",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.repayment_count,"onUpdate:modelValue":t[4]||(t[4]=s=>e.repayment_count=s),rounded:""},null,8,["modelValue"])]),_:1})]),m("section",he,[a(c,{label:"Fecha de desembolso"},{default:n(()=>[a(_,{modelValue:e.disbursement_date,"onUpdate:modelValue":t[5]||(t[5]=s=>e.disbursement_date=s),size:"large",class:"w-full"},null,8,["modelValue"])]),_:1}),a(c,{label:"Fecha de primer pago"},{default:n(()=>[a(_,{modelValue:e.first_installment_date,"onUpdate:modelValue":t[6]||(t[6]=s=>e.first_installment_date=s),size:"large",class:"w-full"},null,8,["modelValue"])]),_:1}),a(c,{label:"Frecuencia",class:"w-full"},{default:n(()=>[a(d(W),{options:d(X),modelValue:e.frequency,"onUpdate:modelValue":t[7]||(t[7]=s=>e.frequency=s)},null,8,["options","modelValue"])]),_:1})])]),_:1}),a(T,{class:"w-full",onClick:t[8]||(t[8]=s=>w.value=!w.value)},{default:n(()=>[V(" Opciones Avanzadas")]),_:1}),w.value?(v(),L(C,{key:0,title:"Avanzadas","section-class":"w-full",class:"mt-4"},{default:n(()=>[m("section",be,[a(c,{label:"Dias de gracia",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.grace_days,"onUpdate:modelValue":t[9]||(t[9]=s=>e.grace_days=s),rounded:""},null,8,["modelValue"])]),_:1}),a(c,{label:"Interes de mora",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.late_fee,"onUpdate:modelValue":t[10]||(t[10]=s=>e.late_fee=s),rounded:""},null,8,["modelValue"])]),_:1}),a(c,{label:"Cuotas cobradas",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.paid_installments,"onUpdate:modelValue":t[11]||(t[11]=s=>e.paid_installments=s),rounded:""},null,8,["modelValue"])]),_:1})]),m("section",ge,[a(c,{label:"Gastos de cierre",class:"w-full"},{default:n(()=>[a(d(b),{modelValue:e.closing_fees,"onUpdate:modelValue":t[12]||(t[12]=s=>e.closing_fees=s),rounded:""},null,8,["modelValue"])]),_:1}),a(d(K),{class:"w-full hidden"})])]),_:1})):R("",!0),w.value?(v(),L(C,{key:1,title:"Contabilidad","section-class":"w-full",class:"mt-4"},{default:n(()=>[m("section",we,[a(c,{label:"Cuenta origen",field:"sourceAccount",class:"w-full"},{default:n(()=>[a(ne,{modelValue:e.sourceAccount,"onUpdate:modelValue":t[13]||(t[13]=s=>e.sourceAccount=s)},null,8,["modelValue"])]),_:1})])]),_:1})):R("",!0)]),m("article",ve,[m("section",Ve,[xe,a(se,{payment:(g=i.value)==null?void 0:g.payment,"total-interest":(F=i.value)==null?void 0:F.totalInterest,"total-debt":(I=i.value)==null?void 0:I.totalDebt},null,8,["payment","total-interest","total-debt"]),d(p)?(v(),H("section",Me,[Ce,a(J,{installments:(P=i.value)==null?void 0:P.payments},null,8,["installments"])])):R("",!0)]),m("footer",De,[a(d(j),{class:"font-bold text-red-400 rounded-md bg-base-lvl-2",variant:"secondary",onClick:t[14]||(t[14]=s=>q())},{default:n(()=>[V(" Cancelar ")]),_:1}),a(T,{processing:h.value,variant:"secondary",onClick:$,disabled:!d(p)||h.value},{default:n(()=>[V(" Registar Prestamo ")]),_:1},8,["processing","disabled"])])])])]}),_:1},8,["title"])}}});export{mt as default};
