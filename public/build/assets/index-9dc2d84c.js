import"./formatMoney-b7ef7683.js";import{f as o}from"./index-c251e33c.js";import{p as f}from"./index-3624ec38.js";import{r as s,t as p,a as u}from"./index-81f33ca2.js";function m(r,t){s(2,arguments);var e=p(r),a=u(t);return isNaN(a)?new Date(NaN):(a&&e.setDate(e.getDate()+a),e)}function b(r,t){s(2,arguments);var e=u(t);return m(r,-e)}const y=(r,t="d MMM, yyyy")=>{const e="-- --- ----";try{return typeof r=="string"?o(f(r),t):o(r,t)}catch{return r??e}};function w(r,t=new Date().getFullYear()){const e=Number(r);let a=Number(t);if(Number.isNaN(e))throw new TypeError("Parameter 'from' should be a number or string with number");if(Number.isNaN(a))throw new TypeError("Parameter 'to' should be a number or string with number");const n=[];for(;a>=e;)n.push(a),a--;return n}const N=r=>r?y(r,"y-M-d"):null,D=(r,t,e="back")=>{const a=new Date,n=e=="back"?b:m;if(!t)return"";const i=t.map(c=>N(n(a,c))).join("~");return`filter[${r}]=${i}`};export{m as a,w as c,N as d,y as f,D as g};