const t=(r,e="DOP")=>{try{return new Intl.NumberFormat("en-US",{style:"currency",currency:e,currencyDisplay:"symbol"}).format(Number(r)||0)}catch{return r}},c=t;export{c as a,t as f};
