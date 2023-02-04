import{k as v,c as g,w as o,C as d,o as r,a as t,b as i,h as _,g as m,t as c,f as b,n as w,u as p,V as x,W as y,M as D}from"./app-fef21123.js";import{f as V}from"./formatMoney-b7ef7683.js";import{_ as $}from"./AppLayout.vue_vue_type_script_setup_true_lang-dc441d96.js";import"./AppLayout.vue_vue_type_style_index_0_lang-cff2a039.js";import{_ as u}from"./AppButton.vue_vue_type_script_setup_true_lang-78bec775.js";import{_ as k}from"./BaseTable.vue_vue_type_style_index_0_lang-6dbbce48.js";import{f as N}from"./index-c251e33c.js";import{_ as A}from"./_plugin-vue_export-helper-c27b6911.js";import"./atmosphere-ui-43ec926d.js";import"./PaymentFormModal.vue_vue_type_script_setup_true_lang-3ea39cd1.js";import"./PaymentGrid-926a1204.js";/* empty css                                                                    */import"./mathHelper-02ca0ff8.js";import"./exact-math.node-8398c915.js";import"./constants-0a903a05.js";import"./AccountSelect.vue_vue_type_script_setup_true_lang-f3e358be.js";import"./BaseSelect.vue_vue_type_script_setup_true_lang-c6d5c1da.js";/* empty css                                                   */import"./Modal.vue_vue_type_script_setup_true_lang-05fdc6cc.js";import"./usePaymentModal-a07a875e.js";import"./ClientFormModal.vue_vue_type_script_setup_true_lang-396b1c58.js";import"./close-ce456e3e.js";import"./SectionNav.vue_vue_type_script_setup_true_lang-5401ee99.js";import"./clientInteractions-23fb4c77.js";import"./AppSearch.vue_vue_type_script_setup_true_lang-7eed6d28.js";import"./AppSearchFilters-54a857cd.js";import"./customCell-775a8e84.js";import"./index-81f33ca2.js";const B=[{label:"Description",name:"description",width:300},{label:"Account",name:"account"},{label:"Category",name:"category"},{label:"Date",name:"date",width:200},{label:"Amount",name:"total"},{label:"",name:"actions",width:300,type:"custom"}],f=n=>(x("data-v-535db07e"),n=n(),y(),n),C={class:"w-full py-10 mx-auto sm:px-6 lg:px-8"},I={class:"flex items-center justify-between py-2 mb-10 border-4 border-white rounded-md bg-gray-50"},T=f(()=>t("div",{class:"px-5 font-bold text-gray-600"},"Transactions",-1)),S={class:"flex space-x-2 overflow-hidden font-bold text-gray-500 rounded-t-lg max-w-min"},M={class:"w-full rounded-md bg-base-lvl-3"};const L={class:"font-bold"},U={key:0,class:"italic font-normal"},j={class:"font-bold text-blue-400 border-b border-blue-400 border-dashed cursor-pointer"},E={class:"font-bold"},P={class:"space-x-2 w-14"},R=f(()=>t("button",null,[t("i",{class:"fa fa-edit"})],-1)),W=["onClick"],q=v({__name:"Index",props:{transactions:{type:Array},categories:{type:Array}},setup(n){const z={};function l(a=!0,s=null){this.showAdd=s??!this.showAdd,this.formData.date=N(new Date,"yyyy-MM-dd"),this.isIncome=a}function F(){if(this.isLoading)return;const a=this.setRequestData(this.formData);if(this.isLoading=!0,!a.description||!a.account_id||!a.category_id){D({message:"All fields are required",title:"Fill the fields",type:"info"}),this.isLoading=!1;return}axios.post("/transactions",a).then(()=>{this.toggleShowAdd(!1,!1),this.isLoading=!1,this.$inertia.reload()})}function h(a){this.isLoading||(this.isLoading=!0,this.$inertia.delete(`/transactions/${a}`,{onSuccess(){this.$inertia.reload()}}))}return(a,s)=>{const H=d("jet-select"),O=d("at-button");return r(),g($,null,{default:o(()=>[t("div",C,[t("div",I,[T,t("div",S,[i(u,{onClick:s[0]||(s[0]=e=>l(!0,!0)),class:"w-32"},{default:o(()=>[_(" Add Income ")]),_:1}),i(u,{onClick:s[1]||(s[1]=e=>l(!1,!0)),class:"w-36"},{default:o(()=>[_(" Add Expense ")]),_:1})])]),t("div",M,[i(k,{cols:p(B),tableData:n.transactions.data,"show-prepend":!0,class:"text-gray-500"},{prepend:o(()=>[m("",!0)]),name:o(({scope:e})=>[t("div",null,[t("div",L,c(e.row.name),1),e.row.last_transaction_date?(r(),b("div",U," Last transaction on: "+c(e.row.last_transaction_date.date),1)):m("",!0)])]),description:o(({scope:{row:e}})=>[t("span",j,c(e.description)+" #"+c(e.number),1)]),account:o(({scope:{row:e}})=>[t("div",E,c(e),1)]),category:o(({scope:{row:e}})=>[t("div",null,c(e.category?e.category.name:""),1)]),total:o(({scope:{row:e}})=>[t("div",{class:w(["font-bold",{"text-red-500":e.direction=="WITHDRAW","text-green-500":e.direction=="DEPOSIT"}])},c(p(V)(e.total)),3)]),actions:o(({scope:{row:e}})=>[t("div",P,[R,t("button",null,[t("i",{class:"fa fa-trash",onClick:G=>h(e.id)},null,8,W)])])]),_:1},8,["cols","tableData"])])])]),_:1})}}});const $e=A(q,[["__scopeId","data-v-535db07e"]]);export{$e as default};