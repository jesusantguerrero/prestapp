import{f as v,a as l,s as i,n as p,o as f,z as _,B as u,d as g}from"./app-b9464164.js";const m={class:"app-content"},b={class:"app-side-container"},h={class:"border app-content__inner ic-scroller bg-base-100"},E={__name:"AppShell",props:{navClass:{type:[String,Object]},isExpanded:{type:Boolean,default:!0}},setup(e){return(s,o)=>(f(),v("main",{class:p(["min-h-screen bg-base-lvl-3 home-container",{expanded:e.isExpanded}])},[l("nav",{class:p(["app-header md:pl-{var(--app)} bg-neutral imary border-b",e.navClass])},[i(s.$slots,"navigation")],2),l("article",m,[l("aside",b,[i(s.$slots,"aside")]),l("section",h,[i(s.$slots,"main-section")])])],2))}},O=E;const n=_({}),S=e=>{n[e]||(n[e]={isOpen:!1,data:null});const s=()=>{n[e].isOpen=!1,n[e].data=null},o=(r={data:null,isOpen:!0})=>{n[e].data=r.data??null,n[e].isOpen=!0},c=r=>{n[e].isOpen?s():o(r)},{isOpen:t}=u(n[e]),d=g(()=>n[e].data??null);return{toggleModal:c,openModal:o,closeModal:s,isOpen:t,data:d}},a=_({isOpen:!1,transactionData:null,mode:"EXPENSE",recurrence:!1,automatic:!1}),x=()=>{const e=()=>{a.isOpen=!1,a.automatic=!1,a.transactionData=null,a.mode="EXPENSE",a.recurrence=!1},s=(t={})=>{a.automatic=t.automatic??!1,a.transactionData=t.transactionData??null,a.recurrence=t.recurrence??!1,a.mode=t.mode??"EXPENSE",a.isOpen=!0},o=t=>{a.isOpen?e():s(t)},{isOpen:c}=u(a);return{toggleTransactionModal:o,openTransactionModal:s,closeTransactionModal:e,isOpen:c}};export{O as A,S as a,a as t,x as u};