import{a as g}from"./formatMoney-b7ef7683.js";import{C}from"./customCell-9e9ed16f.js";import{l as w,d as x,f as a,a as n,F as c,j as f,u as d,n as r,k as y,g as b,o as t,t as u,R as B,c as $}from"./app-494b6f90.js";const v=["data"],A={class:"px-2 py-4 font-bold text-left border-b border-gray-200 text-body"},D={key:0},S={key:0},T=["colspan"],_={key:3},E={key:1},H={key:0},N=["colspan"],j=["colspan"],z={class:"w-full py-5 text-center text-base-200"},F=["colspan"],V=w({__name:"AtTable",props:{cols:{type:Array,required:!0},isLoading:{type:Boolean,default:!1},tableData:{type:Array},config:{type:Object,default(){return{}}},showPrepend:{type:Boolean,default:!1},showAppend:{type:Boolean,default:!1},emptyText:{type:String,default:"No data found"},hideEmptyText:{type:Boolean,default:!1},hideHeaders:{type:Boolean,default:!1},hiddenCols:{type:[Array,null]}},setup(o){const p=o,k=({row:l})=>l.headerClass,i=x(()=>p.hiddenCols?p.cols.filter(l=>!p.hiddenCols.includes(l.name)):p.cols);return(l,m)=>(t(),a("table",{class:"table-fixed",style:{width:"100%"},data:o.tableData,"header-cell-class-name":k,onSortChange:m[0]||(m[0]=s=>l.$emit("sort",s)),onRowClick:m[1]||(m[1]=s=>l.$emit("row-click",s))},[n("thead",{class:r({hidden:o.hideHeaders})},[n("tr",A,[(t(!0),a(c,null,f(d(i),s=>(t(),a("th",{key:s.name,class:r(["px-2 py-4",[s.headerClass]])},u(s.label),3))),128))])],2),o.tableData.length?(t(),a("tbody",D,[o.showPrepend?(t(),a("tr",S,[n("td",{colspan:d(i).length},[y(l.$slots,"prepend")],8,T)])):b("",!0),(t(!0),a(c,null,f(o.tableData,(s,h)=>(t(),a("tr",{key:`data-row-${h}`,class:r(["text-body",{"bg-base-lvl-2":h%2}])},[(t(!0),a(c,null,f(d(i),e=>(t(),a("td",{key:e.name,class:"h-full align-baseline",style:B({width:e.width,maxWidth:e.maxWidth})},[n("div",{class:r(["my-auto",e.class])},[y(l.$slots,e.name,{scope:{row:s,value:s[e.name],col:e,field:e.name,$index:h}},()=>[e.type=="calc"?(t(),a("div",{key:0,class:r(e.class)},u(e.formula(s)),3)):e.type=="money"?(t(),a("div",{key:1,class:r(e.class)},u(d(g)(s[e.name])),3)):e.render?(t(),$(d(C),{key:2,class:r(e.class),col:e,data:s},null,8,["class","col","data"])):(t(),a("div",_,u(s[e.name]),1))])],2)],4))),128))],2))),128))])):(t(),a("tbody",E,[o.showAppend?(t(),a("tr",H,[n("td",{colspan:d(i).length},[y(l.$slots,"append")],8,N)])):b("",!0),n("tr",null,[n("td",{colspan:d(i).length,class:"flex flex-col w-full"},[o.hideEmptyText?b("",!0):y(l.$slots,"empty",{key:0},()=>[n("div",z,u(o.emptyText),1)])],8,j)])])),n("tfoot",null,[n("tr",null,[n("td",{colspan:d(i).length},[y(l.$slots,"append")],8,F)])])],40,v))}});export{V as _};