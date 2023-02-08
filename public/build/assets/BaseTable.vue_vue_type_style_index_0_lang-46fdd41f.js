import{C as z}from"./customCell-8a32bb2a.js";import{_ as x}from"./AppSearch.vue_vue_type_script_setup_true_lang-5f2b06a0.js";import{ac as S,ad as E,d as C,k as T,f as r,a as d,b as j,g as o,c as n,n as u,u as y,F as b,j as w,w as f,C as p,o as s,s as g,a4 as A}from"./app-5778ba55.js";const V=()=>{const a=S(E);return{isMobile:C(()=>a.isSmaller("md"))}},M={class:"pt-4 mb-24"},N={key:0,class:"w-full px-4"},D={key:0,class:"space-y-2"},F={class:"flex justify-between items-center py-4"},P=d("div",{class:"w-full"},null,-1),H={class:"w-full flex justify-end"},q=T({__name:"BaseTable",props:{selectable:{type:Boolean},defaultExpandAll:{type:Boolean},showSummary:{type:Boolean},summaryMethod:{type:[null,Function]},cols:{type:Array,required:!0},hiddenCols:{type:[Array,null]},isLoading:{type:Boolean,default:!1},tableData:{type:Array},config:{type:Object,default(){return{}}},pagination:{type:Object,default(){return{}}},total:{type:Number},showPrepend:{type:Boolean,default:!1},showAppend:{type:Boolean,default:!1},emptyText:{type:String,default:"No data found"},hideEmptyText:{type:Boolean,default:!1},hideHeaders:{type:Boolean,default:!1},responsive:{type:Boolean,default:!1},tableClass:{default:"px-4"},layout:{type:String,default:"table"}},setup(a){const i=a,{isMobile:k}=V(),B=t=>t.headerClass,v=C(()=>i.hiddenCols?i.cols.filter(t=>!i.hiddenCols.includes(t.name)):i.cols);return(t,l)=>{const c=p("ElPagination"),m=p("ElTableColumn"),$=p("ElTable");return s(),r("section",M,[d("section",{class:u(["flex justify-between items-center",{"py-4":a.config.search}])},[a.config.search?(s(),r("div",N,[j(x,{class:"w-96",modelValue:a.pagination.search,"onUpdate:modelValue":l[0]||(l[0]=e=>a.pagination.search=e),onSearch:l[1]||(l[1]=e=>t.$emit("search"))},null,8,["modelValue"])])):o("",!0),a.config.pagination?(s(),n(c,{key:1,class:"w-full flex justify-end pr-4",background:"",onCurrentChange:l[2]||(l[2]=e=>t.$emit("paginate",e)),onSizeChange:l[3]||(l[3]=e=>t.$emit("size-change",e)),layout:"total,prev, pager, next,sizes","current-page":a.pagination.page,"page-sizes":[10,20,50,100,200],"page-size":a.pagination.limit,total:a.total},null,8,["current-page","page-size","total"])):o("",!0)],2),d("section",{class:u(a.tableClass)},[a.layout=="grid"||a.responsive&&t.$slots.card&&y(k)?(s(),r("div",D,[(s(!0),r(b,null,w(a.tableData,e=>g(t.$slots,"card",{row:e})),256))])):(s(),n($,{key:1,class:"table-fixed",style:{width:"100%"},"default-expand-all":a.defaultExpandAll,"show-summary":a.showSummary,"summary-method":a.summaryMethod,data:a.tableData,"header-cell-class-name":B,onSortChange:l[4]||(l[4]=e=>t.$emit("sort",e)),onRowClick:l[5]||(l[5]=e=>t.$emit("row-click",e))},{default:f(()=>[a.selectable?(s(),n(m,{key:0,type:"selection",width:"55"})):o("",!0),t.$slots.expand?(s(),n(m,{key:1,type:"expand"},{default:f(e=>[g(t.$slots,"expand",{row:e.row})]),_:3})):o("",!0),(s(!0),r(b,null,w(y(v),e=>(s(),n(m,{prop:e.name,key:e.name,label:e.label||e.name,"cell-class-name":"px-2 py-4","header-cell-class-name":e.headerClass,"class-name":e.class,width:e.width,"min-width":e.minWidth,class:u([e.headerClass])},A({_:2},[t.$slots[e.name]||e.render?{name:"default",fn:f(h=>[g(t.$slots,e.name,{scope:h},()=>[e.render?(s(),n(y(z),{key:0,class:u(e.class),col:e,data:h.row},null,8,["class","col","data"])):o("",!0)])]),key:"0"}:void 0]),1032,["prop","label","header-cell-class-name","class-name","width","min-width","class"]))),128))]),_:3},8,["default-expand-all","show-summary","summary-method","data"]))],2),d("section",F,[P,d("div",H,[a.config.pagination?(s(),n(c,{key:0,class:"w-full flex justify-end pr-4",background:"",onCurrentChange:l[6]||(l[6]=e=>t.$emit("paginate",e)),onSizeChange:l[7]||(l[7]=e=>t.$emit("size-change",e)),"current-page":a.pagination.page,layout:"total,prev, pager, next,sizes","page-sizes":[10,20,50,100,200],"page-size":a.pagination.limit,total:a.total},null,8,["current-page","page-size","total"])):o("",!0)])])])}}});export{q as _};
