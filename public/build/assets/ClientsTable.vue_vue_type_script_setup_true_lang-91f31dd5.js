import{_ as C}from"./trash-b41c2bba.js";import{_ as $}from"./file-8bfe57ae.js";import{_ as k}from"./chevron-right-5280f715.js";import{Q as t,ab as I,y as x,ac as f,l as E,ad as N,f as B,b as a,w as c,u as m,o as p,a as u,c as M,E as j,g as T}from"./app-e7293397.js";import{_ as V}from"./BaseTable.vue_vue_type_style_index_0_lang-efd97e4a.js";import{_ as A}from"./ClientCard.vue_vue_type_script_setup_true_lang-a6acaa8d.js";import{_}from"./AppButton.vue_vue_type_script_setup_true_lang-046c4e1c.js";import{g as d}from"./constants-22f9a1fe.js";import{g as h}from"./constants-fbb1b618.js";import{I as S}from"./IconMarker-562faee5.js";import{_ as z}from"./UnitTitle.vue_vue_type_script_setup_true_lang-c25f773c.js";import{f as P}from"./formatMoney-b7ef7683.js";function D(s){return[{name:"client",label:"Cliente",class:"text-center",headerClass:"text-center",width:300,render(e){const i=e.names+" "+e.lastnames,o=e.names?e.names[0]+e.lastnames[0]:"";return t("div",{class:"flex items-center space-x-2 px-4"},[t(I,{shape:"circle",width:20,height:20,maxWidth:20,maxHeight:20},o),t("div",{class:"ml-2 w-full text-left"},[t(x,{class:"font-bold text-primary",href:d(e)},i),t("p",{class:"text-body-1/80 text-sm"},e.dni)])])}},{name:"cellphone",label:"Celular",class:"text-center",headerClass:"text-center",width:130},{name:"address_details",label:"Dirección",class:"text-left",headerClass:"text-left",width:300,render(e){var o;const i=e.rent?e.rent.property.short_name:e.address_details;return e.rent?t(z,{title:e.rent.address,ownerName:e.rent.owner_name,tenantName:P((o=e.rent)==null?void 0:o.amount)}):t("div",{class:"justify-center"},[t("div",{class:"flex items-start space-x-2 text-body-1 font-bold"},[t(S,{class:"font-bold mt-1 w-6 h-6"}),t("span",i)])])}},{name:"status",label:"Estado",align:"center",class:"text-center",render(e){return e.rent?t(f,{type:h(e.rent.status)},s(`commons.${e.status}`)):t(f,{type:h(e.status)},s(`commons.${e.status}`))}},{name:"actions",label:"Acciones"}]}const F={class:"mt-5"},H={class:"flex justify-end items-center"},J={class:"flex"},w=E({__name:"ClientsTable",props:{clients:null,pagination:null,total:null},setup(s){const{t:e}=N(),i={selectable:!0,searchBar:!0,pagination:!0},o=D(e);return d,(l,r)=>{const b=k,g=$,v=C;return p(),B("section",F,[a(V,{"table-data":s.clients,cols:m(o),config:i,pagination:s.pagination,total:s.total,responsive:"",onSearch:r[0]||(r[0]=n=>l.$emit("search")),onPaginate:r[1]||(r[1]=n=>l.$emit("paginate",n)),onSizeChange:r[2]||(r[2]=n=>l.$emit("size-change",n))},{card:c(({row:n})=>[a(A,{client:n,label:l.$t("commons.ACTIVE")},null,8,["client","label"])]),actions:c(({scope:{row:n}})=>[u("div",H,[a(m(x),{class:"relative inline-block cursor-pointer ml-4 hover:bg-primary hover:text-white px-5 py-2 overflow-hidden font-bold text-body transition rounded-md focus:outline-none hover:bg-opacity-80 min-w-max",href:m(d)(n)},{default:c(()=>[a(b)]),_:2},1032,["href"]),u("div",J,[n.contract?(p(),M(_,{key:0,class:"hover:text-primary transition items-center flex flex-col justify-center hover:border-primary-400",variant:"neutral",onClick:y=>m(j).visit(m(d)(n))},{default:c(()=>[a(g)]),_:2},1032,["onClick"])):T("",!0)]),a(_,{variant:"neutral",class:"hover:text-error transition items-center flex flex-col justify-center hover:border-red-400",onClick:y=>l.deleteClient(n)},{default:c(()=>[a(v)]),_:2},1032,["onClick"])])]),_:1},8,["table-data","cols","pagination","total"])])}}});export{w as _};