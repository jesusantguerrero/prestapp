const p=i=>({customPrint:(d=i,{beforePrint:o,delay:r}={beforePrint:()=>{},delay:0})=>{const t=document.getElementById(d),n=t==null?void 0:t.cloneNode(!0);let e=document.getElementById("print");e||(e=document.createElement("div"),e.id="print",document.body.appendChild(e)),e.innerHTML="",n&&(e.appendChild(n),o(),setTimeout(()=>{window.print()},r))}});export{p as u};