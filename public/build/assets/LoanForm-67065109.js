import{E as k,k as $,z as A,l as Y,d as D,r as P,ab as O,c as E,w as o,u as s,C as S,o as v,b as l,h as V,t as z,a as c,g as L,f as B}from"./app-9b138141.js";import{R as m,V as h,a as H,N as W}from"./atmosphere-ui-18006871.js";import{_ as K}from"./AppLayout.vue_vue_type_script_setup_true_lang-e699ee9b.js";import"./AppLayout.vue_vue_type_style_index_0_lang-0c70bcc1.js";import{_ as R}from"./AppButton.vue_vue_type_script_setup_true_lang-70b35c26.js";import{_ as j}from"./InstallmentTable.vue_vue_type_script_setup_true_lang-deac3847.js";import G from"./LoanSectionNav-894e86bf.js";import{L as y,l as J}from"./constants-0a903a05.js";import{f as Q}from"./index-c251e33c.js";import{p as X,f as w}from"./index-901d5032.js";import{a as N}from"./index-af4464e9.js";import{a as U}from"./index-17a9d477.js";import{r as Z,t as ee}from"./index-81f33ca2.js";import{M as x}from"./mathHelper-7298fc42.js";import{_ as te}from"./BaseSelect.vue_vue_type_script_setup_true_lang-71eb1903.js";/* empty css                                                   */import{_ as M}from"./FormSection.vue_vue_type_script_setup_true_lang-f19cc5f5.js";import{_ as ae}from"./LoanSummary.vue_vue_type_script_setup_true_lang-444f8647.js";import{_ as le}from"./AccountSelect.vue_vue_type_script_setup_true_lang-79716ae8.js";import"./_plugin-vue_export-helper-c27b6911.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-d178a55a.js";import"./PaymentGrid-bb2f1c2e.js";/* empty css                                                                    */import"./Modal.vue_vue_type_script_setup_true_lang-6e9de0f6.js";import"./usePaymentModal-81539917.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-e40a6e31.js";import"./close-f797118c.js";import"./ClientForm.vue_vue_type_script_setup_true_lang-386607dc.js";import"./clientInteractions-762b0273.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-c3d92866.js";import"./formatMoney-b7ef7683.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-da5abf29.js";import"./AppSearchFilters-f097730f.js";import"./sharp-payment-a643f5bb.js";import"./BaseTable.vue_vue_type_style_index_0_lang-8bddfc90.js";import"./customCell-c06172d5.js";import"./menus-1fa29540.js";import"./exact-math.node-df6f4533.js";import"./IconCoins-b4a08641.js";function se(i){Z(1,arguments);var a=ee(i),e=a.getMonth();return a.setFullYear(a.getFullYear(),e+1,0),a.setHours(23,59,59,999),a}const ne=i=>{let a="/loans",e="post";return i.id&&(a=`/loans/${i.id}`,e="put"),k[e].bind(k,a)},oe=(i,a)=>{const e=ne(i);return console.log(e),new Promise((u,p)=>e({...i,installments:a},{onSuccess(r){u(r)},onError(r){console.log(r),p(r)}}))},ie=(i,a)=>{const u={[y.WEEKLY]:{method:N,interval:7},[y.BIWEEKLY]:{method:N,interval:15},[y.SEMIMONTHLY]:{method:re,interval:1},[y.MONTHLY]:{method:U,interval:1}}[a];return Q(u.method(X(i),u.interval),"yyyy-MM-dd")},re=(i,a)=>i.getDate()==15?se(i):N(i,15);class de{constructor({startDate:a,capital:e,interestMonthlyRate:u,count:p,frequency:r},f=ie){this.payments=[],this.totalDebt=0,this.totalInterest=0,this.totalCapital=0,this.startDate=a,this.capital=e,this.interestMonthlyRate=u/100,this.count=p,this.frequency=r,this.nextDateCalculator=f,this.payment=this.calculatePayment(),this.generateAmortizationTable()}calculatePayment(){const a=this.getFrequencyRate();return x.loanPayment({interestRate:a,capital:this.capital,installments:this.count})}getMonthlyPayment(){return this.payment.toFixed(2)}generateAmortizationTable(){let a=0,e=0,u=this.capital,p=this.startDate;const r=this.getFrequencyRate();for(let f=0;f<this.count;f++){a=x.mulWithRounding(u,r),e=x.subWithRounding(this.payment,a);const b=x.subWithRounding(u,e);this.payments.push({number:f+1,due_date:p,days:0,amount:this.payment,amount_paid:0,amount_due:this.payment,interest:a,principal:e,fees:0,late_fee:0,principal_paid:0,interest_paid:0,fees_paid:0,penalty_paid:0,initial_balance:u,final_balance:b}),this.totalCapital+=e,this.totalDebt+=this.payment,this.totalInterest+=a,u=b,p=this.nextDateCalculator(p,this.frequency)}}getInstallment(a){return this.payments[a-1]}getLastPayment(){return this.payments[this.payments.length-1]}getFrequencyRate(){const a={[y.WEEKLY]:4,[y.BIWEEKLY]:2,[y.SEMIMONTHLY]:2,[y.MONTHLY]:1};return this.interestMonthlyRate/a[this.frequency]}}const ue=({interest_rate:i,amount:a,repayment_count:e,first_installment_date:u,frequency:p})=>new de({startDate:u,frequency:p,interestMonthlyRate:i,capital:a,count:e}),me={class:"flex w-full pb-10 mt-16 space-x-4"},ce={class:"w-8/12 p-4 bg-white rounded-md shadow-md text-body-1"},pe={class:"flex space-x-4"},fe=c("div",null,null,-1),_e={class:"flex space-x-4"},ye={class:"flex w-full space-x-4"},he={class:"flex space-x-4"},be={class:"flex space-x-4"},ge={class:"w-4/12 rounded-md bg-white shadow-md relative overflow-hidden grid gap-4 grid-cols-1 grid-rows-[1fr_50px]"},ve={class:"w-full px-4 overflow-hidden text-body-1"},Ve=c("header",{class:"py-4 font-bold"},"Resumen de prestamo",-1),we={key:0,class:"mt-4"},xe=c("h4",{class:"text-xl font-bold"},"Tabla de amortización",-1),Me={class:"flex justify-between w-full px-4 py-1"},ut=$({__name:"LoanForm",props:{loans:null,clients:null},setup(i){const a=i,e=A({id:null,client_id:null,client:void 0,amount:0,repayment_count:0,interest_rate:0,frequency:"MONTHLY",disbursement_date:new Date,first_installment_date:U(new Date,1),grace_days:0,late_fee:0,installments_paid:0,closing_fees:0,category_id:null,source_type:null,source_account_id:null,sourceAccount:null});Y(()=>a.loans,d=>{var t;d&&(Object.keys(e).forEach(_=>{e[_]=d[_]||e[_]}),e.client_id=((t=a.loans.client)==null?void 0:t.id)??1,e.client=a.loans.client)},{immediate:!0,deep:!0});const u=D(()=>{var d,t;return(d=a.loans)!=null&&d.id?`Prestamo ${(t=a.loans.client)==null?void 0:t.fullName}`:"Crear Prestamo"}),p=D(()=>{var d;return(d=a.loans)!=null&&d.id?"Guardar prestamo":"Registrar Prestamo"}),r=P(null);O(()=>{const d=w(e.first_installment_date,"yyyy-MM-dd");e.amount&&e.interest_rate&&e.repayment_count&&(r.value=ue({interest_rate:e.interest_rate,amount:e.amount,repayment_count:e.repayment_count,first_installment_date:d,frequency:e.frequency}))});const f=D(()=>{var d,t;return r.value&&((t=(d=r.value)==null?void 0:d.payments)==null?void 0:t.length)}),b=()=>{var t;const d={...e,date:w(e.disbursement_date,"yyyy-MM-dd"),disbursement_date:w(e.disbursement_date,"yyyy-MM-dd"),first_installment_date:w(e.first_installment_date,"y-M-d"),client_id:e.client.id,source_type:(t=e.sourceType)==null?void 0:t.id,source_account_id:e.sourceAccount.id};oe(d,r.value.payments).then(()=>{close(),T()}).catch(_=>{console.log(_)})},g=P(!1),T=()=>{k.visit("/loans")};return(d,t)=>{const _=S("ElDatePicker");return v(),E(K,{title:s(u)},{header:o(()=>[l(G,null,{actions:o(()=>[l(R,{variant:"secondary",onClick:t[0]||(t[0]=C=>b()),disabled:!s(f)},{default:o(()=>[V(z(s(p)),1)]),_:1},8,["disabled"])]),_:1})]),default:o(()=>{var C,q,F,I;return[c("main",me,[c("section",ce,[l(M,{title:"Datos del cliente"},{default:o(()=>[l(s(m),{label:"Cliente",class:"w-full"},{default:o(()=>[l(te,{modelValue:e.client,"onUpdate:modelValue":t[1]||(t[1]=n=>e.client=n),"track-by":"id",endpoint:"/api/clients",placeholder:"Selecciona un cliente",label:"display_name"},null,8,["modelValue"])]),_:1})]),_:1}),l(M,{title:"Datos de termino","section-class":"w-full"},{default:o(()=>[c("section",pe,[l(s(m),{label:"Monto a prestar",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.amount,"onUpdate:modelValue":t[2]||(t[2]=n=>e.amount=n),"number-format":"",rounded:""},null,8,["modelValue"])]),_:1}),l(s(m),{label:"Interés mensual",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.interest_rate,"onUpdate:modelValue":t[3]||(t[3]=n=>e.interest_rate=n),"number-format":"",max:"100",rounded:""},{suffix:o(()=>[fe]),_:1},8,["modelValue"])]),_:1}),l(s(m),{label:"Cuotas",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.repayment_count,"onUpdate:modelValue":t[4]||(t[4]=n=>e.repayment_count=n),rounded:""},null,8,["modelValue"])]),_:1})]),c("section",_e,[l(s(m),{label:"Fecha de desembolso"},{default:o(()=>[l(_,{modelValue:e.disbursement_date,"onUpdate:modelValue":t[5]||(t[5]=n=>e.disbursement_date=n),size:"large"},null,8,["modelValue"])]),_:1}),l(s(m),{label:"Fecha de primer pago"},{default:o(()=>[l(_,{modelValue:e.first_installment_date,"onUpdate:modelValue":t[6]||(t[6]=n=>e.first_installment_date=n),size:"large"},null,8,["modelValue"])]),_:1}),l(s(m),{label:"Frecuencia",class:"w-full"},{default:o(()=>[l(s(H),{options:s(J),modelValue:e.frequency,"onUpdate:modelValue":t[7]||(t[7]=n=>e.frequency=n)},null,8,["options","modelValue"])]),_:1})])]),_:1}),l(R,{class:"w-full",onClick:t[8]||(t[8]=n=>g.value=!g.value)},{default:o(()=>[V(" Opciones Avanzadas")]),_:1}),g.value?(v(),E(M,{key:0,title:"Avanzadas","section-class":"w-full",class:"mt-4"},{default:o(()=>[c("section",ye,[l(s(m),{label:"Dias de gracia",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.grace_days,"onUpdate:modelValue":t[9]||(t[9]=n=>e.grace_days=n),rounded:""},null,8,["modelValue"])]),_:1}),l(s(m),{label:"Interes de mora",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.late_fee,"onUpdate:modelValue":t[10]||(t[10]=n=>e.late_fee=n),rounded:""},null,8,["modelValue"])]),_:1}),l(s(m),{label:"Cuotas cobradas",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.paid_installments,"onUpdate:modelValue":t[11]||(t[11]=n=>e.paid_installments=n),rounded:""},null,8,["modelValue"])]),_:1})]),c("section",he,[l(s(m),{label:"Gastos de cierre",class:"w-full"},{default:o(()=>[l(s(h),{modelValue:e.closing_fees,"onUpdate:modelValue":t[12]||(t[12]=n=>e.closing_fees=n),rounded:""},null,8,["modelValue"])]),_:1}),l(s(m),{class:"w-full"})])]),_:1})):L("",!0),g.value?(v(),E(M,{key:1,title:"Contabilidad","section-class":"w-full",class:"mt-4"},{default:o(()=>[c("section",be,[l(s(m),{label:"Cuenta origen",field:"sourceAccount",class:"w-full"},{default:o(()=>[l(le,{modelValue:e.sourceAccount,"onUpdate:modelValue":t[13]||(t[13]=n=>e.sourceAccount=n)},null,8,["modelValue"])]),_:1})])]),_:1})):L("",!0)]),c("article",ge,[c("section",ve,[Ve,l(ae,{payment:(C=r.value)==null?void 0:C.payment,"total-interest":(q=r.value)==null?void 0:q.totalInterest,"total-debt":(F=r.value)==null?void 0:F.totalDebt},null,8,["payment","total-interest","total-debt"]),s(f)?(v(),B("section",we,[xe,l(j,{installments:(I=r.value)==null?void 0:I.payments},null,8,["installments"])])):L("",!0)]),c("footer",Me,[l(s(W),{class:"font-bold text-red-400 rounded-md bg-base-lvl-2",variant:"secondary",onClick:t[14]||(t[14]=n=>T())},{default:o(()=>[V(" Cancelar ")]),_:1}),l(R,{variant:"secondary",onClick:b,disabled:!s(f)},{default:o(()=>[V(" Registar Prestamo ")]),_:1},8,["disabled"])])])])]}),_:1},8,["title"])}}});export{ut as default};
