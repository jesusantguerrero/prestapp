import{_ as p}from"./BaseTable.vue_vue_type_style_index_0_lang-b5975dc2.js";import{g as r,a as c}from"./constants-ca1255a1.js";import{I as u}from"./IconMarker-92fd0c89.js";import{U as t,aa as f,y as h,ab as o,k as b,f as x,b as m,w as g,u as C,o as y}from"./app-de9d56f0.js";import{_ as $}from"./ClientCard.vue_vue_type_script_setup_true_lang-d710f240.js";const v=[{name:"client",label:"Cliente",class:"text-center",headerClass:"text-center",width:270,render(e){const n=e.names+" "+e.lastnames,l=e.names?e.names[0]+e.lastnames[0]:"",s=Object.entries(e).reduce((a,[i,d])=>(i.match(/owner|tenant|lender/)&&d==1&&(a=i.replace("is_","")),a),"");return t("div",{class:"flex items-center space-x-2 px-4"},[t(f,{shape:"circle",width:20,height:20,maxWidth:20,maxHeight:20},l),t("div",{class:"ml-2 w-full text-left"},[t(h,{class:"font-bold text-primary",href:`/contacts/${e.id}/${s}`},n),t("p",{class:"text-body-1/80 text-sm"},e.dni)])])}},{name:"cellphone",label:"Celular",class:"text-center",headerClass:"text-center",width:130},{name:"address_details",label:"Dirección",class:"text-left",headerClass:"text-left",render(e){const n=e.rent?e.rent.property.short_name:e.address_details;return t("div",{class:"justify-center"},[t("div",{class:"flex items-start space-x-2 text-body-1 font-bold"},[t(u,{class:"font-bold mt-1 w-6 h-6"}),t("span",n)])])}},{name:"status",label:"Estado",render(e){return e.rent?t(o,{type:c(e.rent.status)},r(e.rent.status)):t(o,{type:c(e.status)},r(e.status))}},{name:"actions",label:"Acciones"}],_={class:"mt-5"},j=b({__name:"ClientsTable",props:{clients:null,pagination:null,total:null},setup(e){const n={selectable:!0,searchBar:!0,pagination:!0};return(l,s)=>(y(),x("section",_,[m(p,{"table-data":e.clients,cols:C(v),config:n,pagination:e.pagination,total:e.total,responsive:"",onSearch:s[0]||(s[0]=a=>l.$emit("search")),onPaginate:s[1]||(s[1]=a=>l.$emit("paginate",a)),onSizeChange:s[2]||(s[2]=a=>l.$emit("size-change",a))},{card:g(({row:a})=>[m($,{client:a},null,8,["client"])]),_:1},8,["table-data","cols","pagination","total"])]))}});export{j as _};