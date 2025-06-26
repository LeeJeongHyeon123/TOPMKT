var Ns=Object.defineProperty;var hs=(r,s,o)=>s in r?Ns(r,s,{enumerable:!0,configurable:!0,writable:!0,value:o}):r[s]=o;var pe=(r,s,o)=>(hs(r,typeof s!="symbol"?s+"":s,o),o);import{r as d,a as xs,g as lr,b as ue}from"./vendor-680bbf5c.js";import{u as Ne,L,O as vs,a as Lr,N as Sr,b as Cr,c as We,R as ws,d as ee,B as ks}from"./router-d86d4a44.js";import{a as ir}from"./utils-917b1704.js";(function(){const s=document.createElement("link").relList;if(s&&s.supports&&s.supports("modulepreload"))return;for(const n of document.querySelectorAll('link[rel="modulepreload"]'))a(n);new MutationObserver(n=>{for(const i of n)if(i.type==="childList")for(const l of i.addedNodes)l.tagName==="LINK"&&l.rel==="modulepreload"&&a(l)}).observe(document,{childList:!0,subtree:!0});function o(n){const i={};return n.integrity&&(i.integrity=n.integrity),n.referrerPolicy&&(i.referrerPolicy=n.referrerPolicy),n.crossOrigin==="use-credentials"?i.credentials="include":n.crossOrigin==="anonymous"?i.credentials="omit":i.credentials="same-origin",i}function a(n){if(n.ep)return;n.ep=!0;const i=o(n);fetch(n.href,i)}})();var _r={exports:{}},rr={};/**
 * @license React
 * react-jsx-dev-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */(function(){var r=d,s=Symbol.for("react.element"),o=Symbol.for("react.portal"),a=Symbol.for("react.fragment"),n=Symbol.for("react.strict_mode"),i=Symbol.for("react.profiler"),l=Symbol.for("react.provider"),c=Symbol.for("react.context"),u=Symbol.for("react.forward_ref"),f=Symbol.for("react.suspense"),g=Symbol.for("react.suspense_list"),p=Symbol.for("react.memo"),b=Symbol.for("react.lazy"),w=Symbol.for("react.offscreen"),k=Symbol.iterator,h="@@iterator";function C(t){if(t===null||typeof t!="object")return null;var x=k&&t[k]||t[h];return typeof x=="function"?x:null}var _=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;function v(t){{for(var x=arguments.length,y=new Array(x>1?x-1:0),F=1;F<x;F++)y[F-1]=arguments[F];N("error",t,y)}}function N(t,x,y){{var F=_.ReactDebugCurrentFrame,Y=F.getStackAddendum();Y!==""&&(x+="%s",y=y.concat([Y]));var G=y.map(function($){return String($)});G.unshift("Warning: "+x),Function.prototype.apply.call(console[t],console,G)}}var m=!1,E=!1,D=!1,z=!1,T=!1,P;P=Symbol.for("react.module.reference");function H(t){return!!(typeof t=="string"||typeof t=="function"||t===a||t===i||T||t===n||t===f||t===g||z||t===w||m||E||D||typeof t=="object"&&t!==null&&(t.$$typeof===b||t.$$typeof===p||t.$$typeof===l||t.$$typeof===c||t.$$typeof===u||t.$$typeof===P||t.getModuleId!==void 0))}function q(t,x,y){var F=t.displayName;if(F)return F;var Y=x.displayName||x.name||"";return Y!==""?y+"("+Y+")":y}function R(t){return t.displayName||"Context"}function W(t){if(t==null)return null;if(typeof t.tag=="number"&&v("Received an unexpected object in getComponentNameFromType(). This is likely a bug in React. Please file an issue."),typeof t=="function")return t.displayName||t.name||null;if(typeof t=="string")return t;switch(t){case a:return"Fragment";case o:return"Portal";case i:return"Profiler";case n:return"StrictMode";case f:return"Suspense";case g:return"SuspenseList"}if(typeof t=="object")switch(t.$$typeof){case c:var x=t;return R(x)+".Consumer";case l:var y=t;return R(y._context)+".Provider";case u:return q(t,t.render,"ForwardRef");case p:var F=t.displayName||null;return F!==null?F:W(t.type)||"Memo";case b:{var Y=t,G=Y._payload,$=Y._init;try{return W($(G))}catch{return null}}}return null}var j=Object.assign,M=0,B,J,S,U,X,V,O;function K(){}K.__reactDisabledLog=!0;function xe(){{if(M===0){B=console.log,J=console.info,S=console.warn,U=console.error,X=console.group,V=console.groupCollapsed,O=console.groupEnd;var t={configurable:!0,enumerable:!0,value:K,writable:!0};Object.defineProperties(console,{info:t,log:t,warn:t,error:t,group:t,groupCollapsed:t,groupEnd:t})}M++}}function je(){{if(M--,M===0){var t={configurable:!0,enumerable:!0,writable:!0};Object.defineProperties(console,{log:j({},t,{value:B}),info:j({},t,{value:J}),warn:j({},t,{value:S}),error:j({},t,{value:U}),group:j({},t,{value:X}),groupCollapsed:j({},t,{value:V}),groupEnd:j({},t,{value:O})})}M<0&&v("disabledDepth fell below zero. This is a bug in React. Please file an issue.")}}var ge=_.ReactCurrentDispatcher,te;function oe(t,x,y){{if(te===void 0)try{throw Error()}catch(Y){var F=Y.stack.trim().match(/\n( *(at )?)/);te=F&&F[1]||""}return`
`+te+t}}var Ve=!1,Me;{var Gr=typeof WeakMap=="function"?WeakMap:Map;Me=new Gr}function ur(t,x){if(!t||Ve)return"";{var y=Me.get(t);if(y!==void 0)return y}var F;Ve=!0;var Y=Error.prepareStackTrace;Error.prepareStackTrace=void 0;var G;G=ge.current,ge.current=null,xe();try{if(x){var $=function(){throw Error()};if(Object.defineProperty($.prototype,"props",{set:function(){throw Error()}}),typeof Reflect=="object"&&Reflect.construct){try{Reflect.construct($,[])}catch(ne){F=ne}Reflect.construct(t,[],$)}else{try{$.call()}catch(ne){F=ne}t.call($.prototype)}}else{try{throw Error()}catch(ne){F=ne}t()}}catch(ne){if(ne&&F&&typeof ne.stack=="string"){for(var I=ne.stack.split(`
`),se=F.stack.split(`
`),Z=I.length-1,Q=se.length-1;Z>=1&&Q>=0&&I[Z]!==se[Q];)Q--;for(;Z>=1&&Q>=0;Z--,Q--)if(I[Z]!==se[Q]){if(Z!==1||Q!==1)do if(Z--,Q--,Q<0||I[Z]!==se[Q]){var ie=`
`+I[Z].replace(" at new "," at ");return t.displayName&&ie.includes("<anonymous>")&&(ie=ie.replace("<anonymous>",t.displayName)),typeof t=="function"&&Me.set(t,ie),ie}while(Z>=1&&Q>=0);break}}}finally{Ve=!1,ge.current=G,je(),Error.prepareStackTrace=Y}var Pe=t?t.displayName||t.name:"",ve=Pe?oe(Pe):"";return typeof t=="function"&&Me.set(t,ve),ve}function Kr(t,x,y){return ur(t,!1)}function Xr(t){var x=t.prototype;return!!(x&&x.isReactComponent)}function Ae(t,x,y){if(t==null)return"";if(typeof t=="function")return ur(t,Xr(t));if(typeof t=="string")return oe(t);switch(t){case f:return oe("Suspense");case g:return oe("SuspenseList")}if(typeof t=="object")switch(t.$$typeof){case u:return Kr(t.render);case p:return Ae(t.type,x,y);case b:{var F=t,Y=F._payload,G=F._init;try{return Ae(G(Y),x,y)}catch{}}}return""}var Le=Object.prototype.hasOwnProperty,fr={},gr=_.ReactDebugCurrentFrame;function ze(t){if(t){var x=t._owner,y=Ae(t.type,t._source,x?x.type:null);gr.setExtraStackFrame(y)}else gr.setExtraStackFrame(null)}function Jr(t,x,y,F,Y){{var G=Function.call.bind(Le);for(var $ in t)if(G(t,$)){var I=void 0;try{if(typeof t[$]!="function"){var se=Error((F||"React class")+": "+y+" type `"+$+"` is invalid; it must be a function, usually from the `prop-types` package, but received `"+typeof t[$]+"`.This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.");throw se.name="Invariant Violation",se}I=t[$](x,$,F,y,null,"SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED")}catch(Z){I=Z}I&&!(I instanceof Error)&&(ze(Y),v("%s: type specification of %s `%s` is invalid; the type checker function must return `null` or an `Error` but returned a %s. You may have forgotten to pass an argument to the type checker creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and shape all require an argument).",F||"React class",y,$,typeof I),ze(null)),I instanceof Error&&!(I.message in fr)&&(fr[I.message]=!0,ze(Y),v("Failed %s type: %s",y,I.message),ze(null))}}}var Zr=Array.isArray;function Ie(t){return Zr(t)}function Qr(t){{var x=typeof Symbol=="function"&&Symbol.toStringTag,y=x&&t[Symbol.toStringTag]||t.constructor.name||"Object";return y}}function es(t){try{return pr(t),!1}catch{return!0}}function pr(t){return""+t}function br(t){if(es(t))return v("The provided key is an unsupported type %s. This value must be coerced to a string before before using it here.",Qr(t)),pr(t)}var Se=_.ReactCurrentOwner,rs={key:!0,ref:!0,__self:!0,__source:!0},Nr,hr,$e;$e={};function ss(t){if(Le.call(t,"ref")){var x=Object.getOwnPropertyDescriptor(t,"ref").get;if(x&&x.isReactWarning)return!1}return t.ref!==void 0}function os(t){if(Le.call(t,"key")){var x=Object.getOwnPropertyDescriptor(t,"key").get;if(x&&x.isReactWarning)return!1}return t.key!==void 0}function ns(t,x){if(typeof t.ref=="string"&&Se.current&&x&&Se.current.stateNode!==x){var y=W(Se.current.type);$e[y]||(v('Component "%s" contains the string ref "%s". Support for string refs will be removed in a future major release. This case cannot be automatically converted to an arrow function. We ask you to manually fix this case by using useRef() or createRef() instead. Learn more about using refs safely here: https://reactjs.org/link/strict-mode-string-ref',W(Se.current.type),t.ref),$e[y]=!0)}}function as(t,x){{var y=function(){Nr||(Nr=!0,v("%s: `key` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",x))};y.isReactWarning=!0,Object.defineProperty(t,"key",{get:y,configurable:!0})}}function ts(t,x){{var y=function(){hr||(hr=!0,v("%s: `ref` is not a prop. Trying to access it will result in `undefined` being returned. If you need to access the same value within the child component, you should pass it as a different prop. (https://reactjs.org/link/special-props)",x))};y.isReactWarning=!0,Object.defineProperty(t,"ref",{get:y,configurable:!0})}}var ls=function(t,x,y,F,Y,G,$){var I={$$typeof:s,type:t,key:x,ref:y,props:$,_owner:G};return I._store={},Object.defineProperty(I._store,"validated",{configurable:!1,enumerable:!1,writable:!0,value:!1}),Object.defineProperty(I,"_self",{configurable:!1,enumerable:!1,writable:!1,value:F}),Object.defineProperty(I,"_source",{configurable:!1,enumerable:!1,writable:!1,value:Y}),Object.freeze&&(Object.freeze(I.props),Object.freeze(I)),I};function is(t,x,y,F,Y){{var G,$={},I=null,se=null;y!==void 0&&(br(y),I=""+y),os(x)&&(br(x.key),I=""+x.key),ss(x)&&(se=x.ref,ns(x,Y));for(G in x)Le.call(x,G)&&!rs.hasOwnProperty(G)&&($[G]=x[G]);if(t&&t.defaultProps){var Z=t.defaultProps;for(G in Z)$[G]===void 0&&($[G]=Z[G])}if(I||se){var Q=typeof t=="function"?t.displayName||t.name||"Unknown":t;I&&as($,Q),se&&ts($,Q)}return ls(t,I,se,Y,F,Se.current,$)}}var Be=_.ReactCurrentOwner,xr=_.ReactDebugCurrentFrame;function ke(t){if(t){var x=t._owner,y=Ae(t.type,t._source,x?x.type:null);xr.setExtraStackFrame(y)}else xr.setExtraStackFrame(null)}var Ye;Ye=!1;function qe(t){return typeof t=="object"&&t!==null&&t.$$typeof===s}function vr(){{if(Be.current){var t=W(Be.current.type);if(t)return`

Check the render method of \``+t+"`."}return""}}function cs(t){{if(t!==void 0){var x=t.fileName.replace(/^.*[\\\/]/,""),y=t.lineNumber;return`

Check your code at `+x+":"+y+"."}return""}}var wr={};function ms(t){{var x=vr();if(!x){var y=typeof t=="string"?t:t.displayName||t.name;y&&(x=`

Check the top-level render call using <`+y+">.")}return x}}function kr(t,x){{if(!t._store||t._store.validated||t.key!=null)return;t._store.validated=!0;var y=ms(x);if(wr[y])return;wr[y]=!0;var F="";t&&t._owner&&t._owner!==Be.current&&(F=" It was passed a child from "+W(t._owner.type)+"."),ke(t),v('Each child in a list should have a unique "key" prop.%s%s See https://reactjs.org/link/warning-keys for more information.',y,F),ke(null)}}function Pr(t,x){{if(typeof t!="object")return;if(Ie(t))for(var y=0;y<t.length;y++){var F=t[y];qe(F)&&kr(F,x)}else if(qe(t))t._store&&(t._store.validated=!0);else if(t){var Y=C(t);if(typeof Y=="function"&&Y!==t.entries)for(var G=Y.call(t),$;!($=G.next()).done;)qe($.value)&&kr($.value,x)}}}function ds(t){{var x=t.type;if(x==null||typeof x=="string")return;var y;if(typeof x=="function")y=x.propTypes;else if(typeof x=="object"&&(x.$$typeof===u||x.$$typeof===p))y=x.propTypes;else return;if(y){var F=W(x);Jr(y,t.props,"prop",F,t)}else if(x.PropTypes!==void 0&&!Ye){Ye=!0;var Y=W(x);v("Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?",Y||"Unknown")}typeof x.getDefaultProps=="function"&&!x.getDefaultProps.isReactClassApproved&&v("getDefaultProps is only used on classic React.createClass definitions. Use a static property named `defaultProps` instead.")}}function us(t){{for(var x=Object.keys(t.props),y=0;y<x.length;y++){var F=x[y];if(F!=="children"&&F!=="key"){ke(t),v("Invalid prop `%s` supplied to `React.Fragment`. React.Fragment can only have `key` and `children` props.",F),ke(null);break}}t.ref!==null&&(ke(t),v("Invalid attribute `ref` supplied to `React.Fragment`."),ke(null))}}var Er={};function fs(t,x,y,F,Y,G){{var $=H(t);if(!$){var I="";(t===void 0||typeof t=="object"&&t!==null&&Object.keys(t).length===0)&&(I+=" You likely forgot to export your component from the file it's defined in, or you might have mixed up default and named imports.");var se=cs(Y);se?I+=se:I+=vr();var Z;t===null?Z="null":Ie(t)?Z="array":t!==void 0&&t.$$typeof===s?(Z="<"+(W(t.type)||"Unknown")+" />",I=" Did you accidentally export a JSX literal instead of a component?"):Z=typeof t,v("React.jsx: type is invalid -- expected a string (for built-in components) or a class/function (for composite components) but got: %s.%s",Z,I)}var Q=is(t,x,y,Y,G);if(Q==null)return Q;if($){var ie=x.children;if(ie!==void 0)if(F)if(Ie(ie)){for(var Pe=0;Pe<ie.length;Pe++)Pr(ie[Pe],t);Object.freeze&&Object.freeze(ie)}else v("React.jsx: Static children should always be an array. You are likely explicitly calling React.jsxs or React.jsxDEV. Use the Babel transform instead.");else Pr(ie,t)}if(Le.call(x,"key")){var ve=W(t),ne=Object.keys(x).filter(function(bs){return bs!=="key"}),Ge=ne.length>0?"{key: someKey, "+ne.join(": ..., ")+": ...}":"{key: someKey}";if(!Er[ve+Ge]){var ps=ne.length>0?"{"+ne.join(": ..., ")+": ...}":"{}";v(`A props object containing a "key" prop is being spread into JSX:
  let props = %s;
  <%s {...props} />
React keys must be passed directly to JSX without using spread:
  let props = %s;
  <%s key={someKey} {...props} />`,Ge,ve,ps,ve),Er[ve+Ge]=!0}}return t===a?us(Q):ds(Q),Q}}var gs=fs;rr.Fragment=a,rr.jsxDEV=gs})();_r.exports=rr;var e=_r.exports,sr={},Ke=xs;{var Re=Ke.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;sr.createRoot=function(r,s){Re.usingClientEntryPoint=!0;try{return Ke.createRoot(r,s)}finally{Re.usingClientEntryPoint=!1}},sr.hydrateRoot=function(r,s,o){Re.usingClientEntryPoint=!0;try{return Ke.hydrateRoot(r,s,o)}finally{Re.usingClientEntryPoint=!1}}}var Ps=typeof Element<"u",Es=typeof Map=="function",ys=typeof Set=="function",Ds=typeof ArrayBuffer=="function"&&!!ArrayBuffer.isView;function Fe(r,s){if(r===s)return!0;if(r&&s&&typeof r=="object"&&typeof s=="object"){if(r.constructor!==s.constructor)return!1;var o,a,n;if(Array.isArray(r)){if(o=r.length,o!=s.length)return!1;for(a=o;a--!==0;)if(!Fe(r[a],s[a]))return!1;return!0}var i;if(Es&&r instanceof Map&&s instanceof Map){if(r.size!==s.size)return!1;for(i=r.entries();!(a=i.next()).done;)if(!s.has(a.value[0]))return!1;for(i=r.entries();!(a=i.next()).done;)if(!Fe(a.value[1],s.get(a.value[0])))return!1;return!0}if(ys&&r instanceof Set&&s instanceof Set){if(r.size!==s.size)return!1;for(i=r.entries();!(a=i.next()).done;)if(!s.has(a.value[0]))return!1;return!0}if(Ds&&ArrayBuffer.isView(r)&&ArrayBuffer.isView(s)){if(o=r.length,o!=s.length)return!1;for(a=o;a--!==0;)if(r[a]!==s[a])return!1;return!0}if(r.constructor===RegExp)return r.source===s.source&&r.flags===s.flags;if(r.valueOf!==Object.prototype.valueOf&&typeof r.valueOf=="function"&&typeof s.valueOf=="function")return r.valueOf()===s.valueOf();if(r.toString!==Object.prototype.toString&&typeof r.toString=="function"&&typeof s.toString=="function")return r.toString()===s.toString();if(n=Object.keys(r),o=n.length,o!==Object.keys(s).length)return!1;for(a=o;a--!==0;)if(!Object.prototype.hasOwnProperty.call(s,n[a]))return!1;if(Ps&&r instanceof Element)return!1;for(a=o;a--!==0;)if(!((n[a]==="_owner"||n[a]==="__v"||n[a]==="__o")&&r.$$typeof)&&!Fe(r[n[a]],s[n[a]]))return!1;return!0}return r!==r&&s!==s}var Ts=function(s,o){try{return Fe(s,o)}catch(a){if((a.message||"").match(/stack|recursion/i))return console.warn("react-fast-compare cannot handle circular refs"),!1;throw a}};const js=lr(Ts);var Vs=function(r,s,o,a,n,i,l,c){if(s===void 0)throw new Error("invariant requires an error message argument");if(!r){var u;if(s===void 0)u=new Error("Minified exception occurred; use the non-minified dev environment for the full error message and additional helpful warnings.");else{var f=[o,a,n,i,l,c],g=0;u=new Error(s.replace(/%s/g,function(){return f[g++]})),u.name="Invariant Violation"}throw u.framesToPop=1,u}},Ls=Vs;const yr=lr(Ls);var Ss=function(s,o,a,n){var i=a?a.call(n,s,o):void 0;if(i!==void 0)return!!i;if(s===o)return!0;if(typeof s!="object"||!s||typeof o!="object"||!o)return!1;var l=Object.keys(s),c=Object.keys(o);if(l.length!==c.length)return!1;for(var u=Object.prototype.hasOwnProperty.bind(o),f=0;f<l.length;f++){var g=l[f];if(!u(g))return!1;var p=s[g],b=o[g];if(i=a?a.call(n,p,b,g):void 0,i===!1||i===void 0&&p!==b)return!1}return!0};const Cs=lr(Ss);var Mr=(r=>(r.BASE="base",r.BODY="body",r.HEAD="head",r.HTML="html",r.LINK="link",r.META="meta",r.NOSCRIPT="noscript",r.SCRIPT="script",r.STYLE="style",r.TITLE="title",r.FRAGMENT="Symbol(react.fragment)",r))(Mr||{}),Xe={link:{rel:["amphtml","canonical","alternate"]},script:{type:["application/ld+json"]},meta:{charset:"",name:["generator","robots","description"],property:["og:type","og:title","og:url","og:image","og:image:alt","og:description","twitter:url","twitter:title","twitter:description","twitter:image","twitter:image:alt","twitter:card","twitter:site"]}},Dr=Object.values(Mr),cr={accesskey:"accessKey",charset:"charSet",class:"className",contenteditable:"contentEditable",contextmenu:"contextMenu","http-equiv":"httpEquiv",itemprop:"itemProp",tabindex:"tabIndex"},_s=Object.entries(cr).reduce((r,[s,o])=>(r[o]=s,r),{}),me="data-rh",ye={DEFAULT_TITLE:"defaultTitle",DEFER:"defer",ENCODE_SPECIAL_CHARACTERS:"encodeSpecialCharacters",ON_CHANGE_CLIENT_STATE:"onChangeClientState",TITLE_TEMPLATE:"titleTemplate",PRIORITIZE_SEO_TAGS:"prioritizeSeoTags"},De=(r,s)=>{for(let o=r.length-1;o>=0;o-=1){const a=r[o];if(Object.prototype.hasOwnProperty.call(a,s))return a[s]}return null},Ms=r=>{let s=De(r,"title");const o=De(r,ye.TITLE_TEMPLATE);if(Array.isArray(s)&&(s=s.join("")),o&&s)return o.replace(/%s/g,()=>s);const a=De(r,ye.DEFAULT_TITLE);return s||a||void 0},As=r=>De(r,ye.ON_CHANGE_CLIENT_STATE)||(()=>{}),Je=(r,s)=>s.filter(o=>typeof o[r]<"u").map(o=>o[r]).reduce((o,a)=>({...o,...a}),{}),zs=(r,s)=>s.filter(o=>typeof o.base<"u").map(o=>o.base).reverse().reduce((o,a)=>{if(!o.length){const n=Object.keys(a);for(let i=0;i<n.length;i+=1){const c=n[i].toLowerCase();if(r.indexOf(c)!==-1&&a[c])return o.concat(a)}}return o},[]),Rs=r=>console&&typeof console.warn=="function"&&console.warn(r),Ce=(r,s,o)=>{const a={};return o.filter(n=>Array.isArray(n[r])?!0:(typeof n[r]<"u"&&Rs(`Helmet: ${r} should be of type "Array". Instead found type "${typeof n[r]}"`),!1)).map(n=>n[r]).reverse().reduce((n,i)=>{const l={};i.filter(u=>{let f;const g=Object.keys(u);for(let b=0;b<g.length;b+=1){const w=g[b],k=w.toLowerCase();s.indexOf(k)!==-1&&!(f==="rel"&&u[f].toLowerCase()==="canonical")&&!(k==="rel"&&u[k].toLowerCase()==="stylesheet")&&(f=k),s.indexOf(w)!==-1&&(w==="innerHTML"||w==="cssText"||w==="itemprop")&&(f=w)}if(!f||!u[f])return!1;const p=u[f].toLowerCase();return a[f]||(a[f]={}),l[f]||(l[f]={}),a[f][p]?!1:(l[f][p]=!0,!0)}).reverse().forEach(u=>n.push(u));const c=Object.keys(l);for(let u=0;u<c.length;u+=1){const f=c[u],g={...a[f],...l[f]};a[f]=g}return n},[]).reverse()},Hs=(r,s)=>{if(Array.isArray(r)&&r.length){for(let o=0;o<r.length;o+=1)if(r[o][s])return!0}return!1},Os=r=>({baseTag:zs(["href"],r),bodyAttributes:Je("bodyAttributes",r),defer:De(r,ye.DEFER),encode:De(r,ye.ENCODE_SPECIAL_CHARACTERS),htmlAttributes:Je("htmlAttributes",r),linkTags:Ce("link",["rel","href"],r),metaTags:Ce("meta",["name","charset","http-equiv","property","itemprop"],r),noscriptTags:Ce("noscript",["innerHTML"],r),onChangeClientState:As(r),scriptTags:Ce("script",["src","innerHTML"],r),styleTags:Ce("style",["cssText"],r),title:Ms(r),titleAttributes:Je("titleAttributes",r),prioritizeSeoTags:Hs(r,ye.PRIORITIZE_SEO_TAGS)}),Ar=r=>Array.isArray(r)?r.join(""):r,Fs=(r,s)=>{const o=Object.keys(r);for(let a=0;a<o.length;a+=1)if(s[o[a]]&&s[o[a]].includes(r[o[a]]))return!0;return!1},Ze=(r,s)=>Array.isArray(r)?r.reduce((o,a)=>(Fs(a,s)?o.priority.push(a):o.default.push(a),o),{priority:[],default:[]}):{default:r,priority:[]},Tr=(r,s)=>({...r,[s]:void 0}),Us=["noscript","script","style"],or=(r,s=!0)=>s===!1?String(r):String(r).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;").replace(/'/g,"&#x27;"),zr=r=>Object.keys(r).reduce((s,o)=>{const a=typeof r[o]<"u"?`${o}="${r[o]}"`:`${o}`;return s?`${s} ${a}`:a},""),Ws=(r,s,o,a)=>{const n=zr(o),i=Ar(s);return n?`<${r} ${me}="true" ${n}>${or(i,a)}</${r}>`:`<${r} ${me}="true">${or(i,a)}</${r}>`},Is=(r,s,o=!0)=>s.reduce((a,n)=>{const i=n,l=Object.keys(i).filter(f=>!(f==="innerHTML"||f==="cssText")).reduce((f,g)=>{const p=typeof i[g]>"u"?g:`${g}="${or(i[g],o)}"`;return f?`${f} ${p}`:p},""),c=i.innerHTML||i.cssText||"",u=Us.indexOf(r)===-1;return`${a}<${r} ${me}="true" ${l}${u?"/>":`>${c}</${r}>`}`},""),Rr=(r,s={})=>Object.keys(r).reduce((o,a)=>{const n=cr[a];return o[n||a]=r[a],o},s),$s=(r,s,o)=>{const a={key:s,[me]:!0},n=Rr(o,a);return[ue.createElement("title",n,s)]},Ue=(r,s)=>s.map((o,a)=>{const n={key:a,[me]:!0};return Object.keys(o).forEach(i=>{const c=cr[i]||i;if(c==="innerHTML"||c==="cssText"){const u=o.innerHTML||o.cssText;n.dangerouslySetInnerHTML={__html:u}}else n[c]=o[i]}),ue.createElement(r,n)}),ce=(r,s,o=!0)=>{switch(r){case"title":return{toComponent:()=>$s(r,s.title,s.titleAttributes),toString:()=>Ws(r,s.title,s.titleAttributes,o)};case"bodyAttributes":case"htmlAttributes":return{toComponent:()=>Rr(s),toString:()=>zr(s)};default:return{toComponent:()=>Ue(r,s),toString:()=>Is(r,s,o)}}},Bs=({metaTags:r,linkTags:s,scriptTags:o,encode:a})=>{const n=Ze(r,Xe.meta),i=Ze(s,Xe.link),l=Ze(o,Xe.script);return{priorityMethods:{toComponent:()=>[...Ue("meta",n.priority),...Ue("link",i.priority),...Ue("script",l.priority)],toString:()=>`${ce("meta",n.priority,a)} ${ce("link",i.priority,a)} ${ce("script",l.priority,a)}`},metaTags:n.default,linkTags:i.default,scriptTags:l.default}},Ys=r=>{const{baseTag:s,bodyAttributes:o,encode:a=!0,htmlAttributes:n,noscriptTags:i,styleTags:l,title:c="",titleAttributes:u,prioritizeSeoTags:f}=r;let{linkTags:g,metaTags:p,scriptTags:b}=r,w={toComponent:()=>{},toString:()=>""};return f&&({priorityMethods:w,linkTags:g,metaTags:p,scriptTags:b}=Bs(r)),{priority:w,base:ce("base",s,a),bodyAttributes:ce("bodyAttributes",o,a),htmlAttributes:ce("htmlAttributes",n,a),link:ce("link",g,a),meta:ce("meta",p,a),noscript:ce("noscript",i,a),script:ce("script",b,a),style:ce("style",l,a),title:ce("title",{title:c,titleAttributes:u},a)}},nr=Ys,He=[],Hr=!!(typeof window<"u"&&window.document&&window.document.createElement),ar=class{constructor(r,s){pe(this,"instances",[]);pe(this,"canUseDOM",Hr);pe(this,"context");pe(this,"value",{setHelmet:r=>{this.context.helmet=r},helmetInstances:{get:()=>this.canUseDOM?He:this.instances,add:r=>{(this.canUseDOM?He:this.instances).push(r)},remove:r=>{const s=(this.canUseDOM?He:this.instances).indexOf(r);(this.canUseDOM?He:this.instances).splice(s,1)}}});this.context=r,this.canUseDOM=s||!1,s||(r.helmet=nr({baseTag:[],bodyAttributes:{},encodeSpecialCharacters:!0,htmlAttributes:{},linkTags:[],metaTags:[],noscriptTags:[],scriptTags:[],styleTags:[],title:"",titleAttributes:{}}))}},qs={},Or=ue.createContext(qs),Te,Fr=(Te=class extends d.Component{constructor(o){super(o);pe(this,"helmetData");this.helmetData=new ar(this.props.context||{},Te.canUseDOM)}render(){return ue.createElement(Or.Provider,{value:this.helmetData.value},this.props.children)}},pe(Te,"canUseDOM",Hr),Te),Ee=(r,s)=>{const o=document.head||document.querySelector("head"),a=o.querySelectorAll(`${r}[${me}]`),n=[].slice.call(a),i=[];let l;return s&&s.length&&s.forEach(c=>{const u=document.createElement(r);for(const f in c)if(Object.prototype.hasOwnProperty.call(c,f))if(f==="innerHTML")u.innerHTML=c.innerHTML;else if(f==="cssText")u.styleSheet?u.styleSheet.cssText=c.cssText:u.appendChild(document.createTextNode(c.cssText));else{const g=f,p=typeof c[g]>"u"?"":c[g];u.setAttribute(f,p)}u.setAttribute(me,"true"),n.some((f,g)=>(l=g,u.isEqualNode(f)))?n.splice(l,1):i.push(u)}),n.forEach(c=>{var u;return(u=c.parentNode)==null?void 0:u.removeChild(c)}),i.forEach(c=>o.appendChild(c)),{oldTags:n,newTags:i}},tr=(r,s)=>{const o=document.getElementsByTagName(r)[0];if(!o)return;const a=o.getAttribute(me),n=a?a.split(","):[],i=[...n],l=Object.keys(s);for(const c of l){const u=s[c]||"";o.getAttribute(c)!==u&&o.setAttribute(c,u),n.indexOf(c)===-1&&n.push(c);const f=i.indexOf(c);f!==-1&&i.splice(f,1)}for(let c=i.length-1;c>=0;c-=1)o.removeAttribute(i[c]);n.length===i.length?o.removeAttribute(me):o.getAttribute(me)!==l.join(",")&&o.setAttribute(me,l.join(","))},Gs=(r,s)=>{typeof r<"u"&&document.title!==r&&(document.title=Ar(r)),tr("title",s)},jr=(r,s)=>{const{baseTag:o,bodyAttributes:a,htmlAttributes:n,linkTags:i,metaTags:l,noscriptTags:c,onChangeClientState:u,scriptTags:f,styleTags:g,title:p,titleAttributes:b}=r;tr("body",a),tr("html",n),Gs(p,b);const w={baseTag:Ee("base",o),linkTags:Ee("link",i),metaTags:Ee("meta",l),noscriptTags:Ee("noscript",c),scriptTags:Ee("script",f),styleTags:Ee("style",g)},k={},h={};Object.keys(w).forEach(C=>{const{newTags:_,oldTags:v}=w[C];_.length&&(k[C]=_),v.length&&(h[C]=w[C].oldTags)}),s&&s(),u(r,k,h)},_e=null,Ks=r=>{_e&&cancelAnimationFrame(_e),r.defer?_e=requestAnimationFrame(()=>{jr(r,()=>{_e=null})}):(jr(r),_e=null)},Xs=Ks,Vr=class extends d.Component{constructor(){super(...arguments);pe(this,"rendered",!1)}shouldComponentUpdate(s){return!Cs(s,this.props)}componentDidUpdate(){this.emitChange()}componentWillUnmount(){const{helmetInstances:s}=this.props.context;s.remove(this),this.emitChange()}emitChange(){const{helmetInstances:s,setHelmet:o}=this.props.context;let a=null;const n=Os(s.get().map(i=>{const l={...i.props};return delete l.context,l}));Fr.canUseDOM?Xs(n):nr&&(a=nr(n)),o(a)}init(){if(this.rendered)return;this.rendered=!0;const{helmetInstances:s}=this.props.context;s.add(this),this.emitChange()}render(){return this.init(),null}},er,Js=(er=class extends d.Component{shouldComponentUpdate(r){return!js(Tr(this.props,"helmetData"),Tr(r,"helmetData"))}mapNestedChildrenToProps(r,s){if(!s)return null;switch(r.type){case"script":case"noscript":return{innerHTML:s};case"style":return{cssText:s};default:throw new Error(`<${r.type} /> elements are self-closing and can not contain children. Refer to our API for more information.`)}}flattenArrayTypeChildren(r,s,o,a){return{...s,[r.type]:[...s[r.type]||[],{...o,...this.mapNestedChildrenToProps(r,a)}]}}mapObjectTypeChildren(r,s,o,a){switch(r.type){case"title":return{...s,[r.type]:a,titleAttributes:{...o}};case"body":return{...s,bodyAttributes:{...o}};case"html":return{...s,htmlAttributes:{...o}};default:return{...s,[r.type]:{...o}}}}mapArrayTypeChildrenToProps(r,s){let o={...s};return Object.keys(r).forEach(a=>{o={...o,[a]:r[a]}}),o}warnOnInvalidChildren(r,s){return yr(Dr.some(o=>r.type===o),typeof r.type=="function"?"You may be attempting to nest <Helmet> components within each other, which is not allowed. Refer to our API for more information.":`Only elements types ${Dr.join(", ")} are allowed. Helmet does not support rendering <${r.type}> elements. Refer to our API for more information.`),yr(!s||typeof s=="string"||Array.isArray(s)&&!s.some(o=>typeof o!="string"),`Helmet expects a string as a child of <${r.type}>. Did you forget to wrap your children in braces? ( <${r.type}>{\`\`}</${r.type}> ) Refer to our API for more information.`),!0}mapChildrenToProps(r,s){let o={};return ue.Children.forEach(r,a=>{if(!a||!a.props)return;const{children:n,...i}=a.props,l=Object.keys(i).reduce((u,f)=>(u[_s[f]||f]=i[f],u),{});let{type:c}=a;switch(typeof c=="symbol"?c=c.toString():this.warnOnInvalidChildren(a,n),c){case"Symbol(react.fragment)":s=this.mapChildrenToProps(n,s);break;case"link":case"meta":case"noscript":case"script":case"style":o=this.flattenArrayTypeChildren(a,o,l,n);break;default:s=this.mapObjectTypeChildren(a,s,l,n);break}}),this.mapArrayTypeChildrenToProps(o,s)}render(){const{children:r,...s}=this.props;let o={...s},{helmetData:a}=s;if(r&&(o=this.mapChildrenToProps(r,o)),a&&!(a instanceof ar)){const n=a;a=new ar(n.context,!0),delete o.helmetData}return a?ue.createElement(Vr,{...o,context:a.value}):ue.createElement(Or.Consumer,null,n=>ue.createElement(Vr,{...o,context:n}))}},pe(er,"defaultProps",{defer:!0,encodeSpecialCharacters:!0,prioritizeSeoTags:!1}),er);const mr={}.REACT_APP_API_URL||"",Zs=3e4;let Oe="";const Qs=async()=>{if(Oe)return Oe;try{return Oe=(await ir.get(`${mr}/csrf-token`)).data.csrf_token||"",Oe}catch(r){return console.warn("CSRF 토큰 가져오기 실패:",r),""}},re=ir.create({baseURL:mr,timeout:Zs,headers:{"Content-Type":"application/json",Accept:"application/json","X-Requested-With":"XMLHttpRequest"},withCredentials:!0});re.interceptors.request.use(async r=>{var o,a;const s=localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token");if(s&&(r.headers=r.headers||{},r.headers.Authorization=`Bearer ${s}`),["post","put","patch","delete"].includes(((o=r.method)==null?void 0:o.toLowerCase())||""))try{const n=await Qs();n&&(r.headers=r.headers||{},r.headers["X-CSRF-TOKEN"]=n)}catch(n){console.warn("CSRF 토큰 설정 실패:",n)}return console.log(`🚀 [${(a=r.method)==null?void 0:a.toUpperCase()}] ${r.url}`,{params:r.params,data:r.data,headers:r.headers}),r},r=>(console.error("요청 인터셉터 오류:",r),Promise.reject(r)));re.interceptors.response.use(r=>{var s;return console.log(`✅ [${(s=r.config.method)==null?void 0:s.toUpperCase()}] ${r.config.url}`,{status:r.status,data:r.data}),r},async r=>{var o,a,n,i,l,c,u,f;const s=r.config;if(console.error(`❌ [${(o=s==null?void 0:s.method)==null?void 0:o.toUpperCase()}] ${s==null?void 0:s.url}`,{status:(a=r.response)==null?void 0:a.status,data:(n=r.response)==null?void 0:n.data,message:r.message}),((i=r.response)==null?void 0:i.status)===401&&!s._retry){s._retry=!0;try{const g=await ir.post(`${mr}/auth/refresh`,{},{headers:{Authorization:`Bearer ${localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token")}`},withCredentials:!0});if(g.data.success&&g.data.data.token){const p=g.data.data.token;return(localStorage.getItem("auth_token")?localStorage:sessionStorage).setItem("auth_token",p),s.headers&&(s.headers.Authorization=`Bearer ${p}`),re(s)}}catch(g){return console.error("토큰 갱신 실패:",g),localStorage.removeItem("auth_token"),sessionStorage.removeItem("auth_token"),localStorage.removeItem("user_data"),sessionStorage.removeItem("user_data"),window.location.href="/auth/login",Promise.reject(g)}}return((l=r.response)==null?void 0:l.status)===403&&console.warn("접근 권한이 없습니다."),((c=r.response)==null?void 0:c.status)===422&&console.warn("유효성 검사 오류:",r.response.data),((u=r.response)==null?void 0:u.status)===429&&console.warn("요청 제한 초과:",r.response.data),((f=r.response)==null?void 0:f.status)===500&&console.error("서버 내부 오류:",r.response.data),r.response||console.error("네트워크 오류:",r.message),Promise.reject(r)});class eo{async login(s){var o,a;try{const n=await re.post("/api/auth/login",s);if(n.data.success&&n.data.data){const{token:i,user:l}=n.data.data,c=s.remember?localStorage:sessionStorage;return c.setItem("auth_token",i),c.setItem("user_data",JSON.stringify(l)),n.data}else throw new Error(n.data.message||"로그인에 실패했습니다.")}catch(n){throw(a=(o=n.response)==null?void 0:o.data)!=null&&a.message?new Error(n.response.data.message):new Error("로그인 중 오류가 발생했습니다.")}}async signup(s){var o,a;try{const n=await re.post("/auth/signup",s);if(n.data.success&&n.data.data){const{token:i,user:l}=n.data.data;return sessionStorage.setItem("auth_token",i),sessionStorage.setItem("user_data",JSON.stringify(l)),n.data}else throw new Error(n.data.message||"회원가입에 실패했습니다.")}catch(n){throw(a=(o=n.response)==null?void 0:o.data)!=null&&a.message?new Error(n.response.data.message):new Error("회원가입 중 오류가 발생했습니다.")}}async logout(){try{await re.post("/auth/logout")}catch(s){console.warn("로그아웃 API 호출 실패:",s)}finally{localStorage.removeItem("auth_token"),sessionStorage.removeItem("auth_token"),localStorage.removeItem("user_data"),sessionStorage.removeItem("user_data")}}async getCurrentUser(){var s;try{const o=await re.get("/auth/me");if(o.data.success&&o.data.data)return(localStorage.getItem("auth_token")?localStorage:sessionStorage).setItem("user_data",JSON.stringify(o.data.data)),o.data;throw new Error(o.data.message||"사용자 정보를 가져올 수 없습니다.")}catch(o){throw((s=o.response)==null?void 0:s.status)===401?(this.logout(),new Error("로그인이 필요합니다.")):new Error("사용자 정보를 가져오는 중 오류가 발생했습니다.")}}async refreshToken(){try{const s=await re.post("/auth/refresh");if(s.data.success&&s.data.data){const{token:o}=s.data.data;return(localStorage.getItem("auth_token")?localStorage:sessionStorage).setItem("auth_token",o),o}else throw new Error("토큰 갱신에 실패했습니다.")}catch{throw this.logout(),new Error("인증이 만료되었습니다. 다시 로그인해주세요.")}}async sendVerificationCode(s,o){var a,n;try{const i=await re.post("/auth/verification/send",{phone:s,type:o});if(!i.data.success)throw new Error(i.data.message||"인증번호 발송에 실패했습니다.")}catch(i){throw(n=(a=i.response)==null?void 0:a.data)!=null&&n.message?new Error(i.response.data.message):new Error("인증번호 발송 중 오류가 발생했습니다.")}}async verifyCode(s,o,a){var n,i;try{const l=await re.post("/auth/verification/verify",{phone:s,code:o,type:a});if(!l.data.success)throw new Error(l.data.message||"인증번호가 올바르지 않습니다.")}catch(l){throw(i=(n=l.response)==null?void 0:n.data)!=null&&i.message?new Error(l.response.data.message):new Error("인증번호 확인 중 오류가 발생했습니다.")}}async requestPasswordReset(s){var o,a;try{const n=await re.post("/auth/password/reset-request",{phone:s});if(!n.data.success)throw new Error(n.data.message||"비밀번호 재설정 요청에 실패했습니다.")}catch(n){throw(a=(o=n.response)==null?void 0:o.data)!=null&&a.message?new Error(n.response.data.message):new Error("비밀번호 재설정 요청 중 오류가 발생했습니다.")}}async resetPassword(s){var o,a;try{const n=await re.post("/auth/password/reset",s);if(!n.data.success)throw new Error(n.data.message||"비밀번호 재설정에 실패했습니다.")}catch(n){throw(a=(o=n.response)==null?void 0:o.data)!=null&&a.message?new Error(n.response.data.message):new Error("비밀번호 재설정 중 오류가 발생했습니다.")}}async changePassword(s,o,a){var n,i;try{const l=await re.post("/auth/password/change",{current_password:s,new_password:o,new_password_confirmation:a});if(!l.data.success)throw new Error(l.data.message||"비밀번호 변경에 실패했습니다.")}catch(l){throw(i=(n=l.response)==null?void 0:n.data)!=null&&i.message?new Error(l.response.data.message):new Error("비밀번호 변경 중 오류가 발생했습니다.")}}async requestEmailVerification(){var s,o;try{const a=await re.post("/auth/email/verification-request");if(!a.data.success)throw new Error(a.data.message||"이메일 인증 요청에 실패했습니다.")}catch(a){throw(o=(s=a.response)==null?void 0:s.data)!=null&&o.message?new Error(a.response.data.message):new Error("이메일 인증 요청 중 오류가 발생했습니다.")}}async verifyEmail(s){var o,a;try{const n=await re.post("/auth/email/verify",{token:s});if(!n.data.success)throw new Error(n.data.message||"이메일 인증에 실패했습니다.")}catch(n){throw(a=(o=n.response)==null?void 0:o.data)!=null&&a.message?new Error(n.response.data.message):new Error("이메일 인증 중 오류가 발생했습니다.")}}getToken(){return localStorage.getItem("auth_token")||sessionStorage.getItem("auth_token")}getStoredUser(){try{const s=localStorage.getItem("user_data")||sessionStorage.getItem("user_data");return s?JSON.parse(s):null}catch(s){return console.error("사용자 데이터 파싱 오류:",s),null}}isAuthenticated(){return!!this.getToken()}async socialLogin(s,o){var a,n;try{const i=await re.post(`/auth/social/${s}`,{access_token:o});if(i.data.success&&i.data.data){const{token:l,user:c}=i.data.data;return sessionStorage.setItem("auth_token",l),sessionStorage.setItem("user_data",JSON.stringify(c)),i.data.data}else throw new Error(i.data.message||"소셜 로그인에 실패했습니다.")}catch(i){throw(n=(a=i.response)==null?void 0:a.data)!=null&&n.message?new Error(i.response.data.message):new Error("소셜 로그인 중 오류가 발생했습니다.")}}}const de=new eo,ro={user:null,isAuthenticated:!1,isLoading:!0,error:null},so=(r,s)=>{switch(s.type){case"AUTH_START":return{...r,isLoading:!0,error:null};case"AUTH_SUCCESS":return{...r,user:s.payload,isAuthenticated:!0,isLoading:!1,error:null};case"AUTH_ERROR":return{...r,user:null,isAuthenticated:!1,isLoading:!1,error:s.payload};case"AUTH_LOGOUT":return{...r,user:null,isAuthenticated:!1,isLoading:!1,error:null};case"CLEAR_ERROR":return{...r,error:null};case"UPDATE_USER":return{...r,user:r.user?{...r.user,...s.payload}:null};default:return r}},Ur=d.createContext(void 0),oo=({children:r})=>{const[s,o]=d.useReducer(so,ro);d.useEffect(()=>{a()},[]);const a=async()=>{try{if(!de.getToken()){o({type:"AUTH_LOGOUT"});return}o({type:"AUTH_START"});const p=await de.getCurrentUser();p.success&&p.data?o({type:"AUTH_SUCCESS",payload:p.data}):o({type:"AUTH_LOGOUT"})}catch{const p=de.getToken(),b=de.getStoredUser();o(p&&b?{type:"AUTH_SUCCESS",payload:b}:{type:"AUTH_LOGOUT"})}},f={...s,login:async(g,p,b=!1)=>{var w;try{o({type:"AUTH_START"});const k=await de.login({phone:g,password:p,remember:b});if(k.success&&((w=k.data)!=null&&w.user))o({type:"AUTH_SUCCESS",payload:k.data.user});else throw new Error(k.message||"로그인에 실패했습니다.")}catch(k){const h=k instanceof Error?k.message:"로그인에 실패했습니다.";throw o({type:"AUTH_ERROR",payload:h}),k}},logout:async()=>{try{await de.logout()}catch(g){console.error("Logout error:",g)}finally{o({type:"AUTH_LOGOUT"})}},signup:async g=>{var p;try{o({type:"AUTH_START"});const b=await de.signup(g);if(b.success&&((p=b.data)!=null&&p.user))o({type:"AUTH_SUCCESS",payload:b.data.user});else throw new Error(b.message||"회원가입에 실패했습니다.")}catch(b){const w=b instanceof Error?b.message:"회원가입에 실패했습니다.";throw o({type:"AUTH_ERROR",payload:w}),b}},updateUser:g=>{o({type:"UPDATE_USER",payload:g})},clearError:()=>{o({type:"CLEAR_ERROR"})},checkAuth:a};return e.jsxDEV(Ur.Provider,{value:f,children:r},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/context/AuthContext.tsx",lineNumber:214,columnNumber:5},globalThis)},le=()=>{const r=d.useContext(Ur);if(r===void 0)throw new Error("useAuth must be used within an AuthProvider");return r},Wr=d.createContext(void 0),no=({children:r,maxToasts:s=5})=>{const[o,a]=d.useState([]),n=d.useCallback(b=>{const w=Math.random().toString(36).substr(2,9),k={id:w,duration:5e3,...b};a(h=>[k,...h].slice(0,s)),k.duration&&k.duration>0&&setTimeout(()=>{i(w)},k.duration)},[s]),i=d.useCallback(b=>{a(w=>w.filter(k=>k.id!==b))},[]),l=d.useCallback(()=>{a([])},[]),c=d.useCallback((b,w,k)=>{n({type:"success",message:b,title:w,duration:k})},[n]),u=d.useCallback((b,w,k)=>{n({type:"error",message:b,title:w,duration:k||7e3})},[n]),f=d.useCallback((b,w,k)=>{n({type:"warning",message:b,title:w,duration:k})},[n]),g=d.useCallback((b,w,k)=>{n({type:"info",message:b,title:w,duration:k})},[n]),p={toasts:o,addToast:n,removeToast:i,clearToasts:l,success:c,error:u,warning:f,info:g};return e.jsxDEV(Wr.Provider,{value:p,children:r},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/context/ToastContext.tsx",lineNumber:101,columnNumber:5},globalThis)},fe=()=>{const r=d.useContext(Wr);if(r===void 0)throw new Error("useToast must be used within a ToastProvider");return r};function we(...r){return r.filter(Boolean).join(" ").replace(/\s+/g," ").trim()}const A=d.forwardRef(({className:r,variant:s="primary",size:o="md",loading:a=!1,fullWidth:n=!1,leftIcon:i,rightIcon:l,disabled:c,children:u,...f},g)=>{const p=["inline-flex items-center justify-center","font-medium transition-all duration-200","focus:outline-none focus:ring-2 focus:ring-offset-2","disabled:opacity-50 disabled:cursor-not-allowed","rounded-lg"],b={primary:["bg-blue-600 text-white","hover:bg-blue-700 active:bg-blue-800","focus:ring-blue-500","shadow-sm hover:shadow-md"],secondary:["bg-gray-100 text-gray-900","hover:bg-gray-200 active:bg-gray-300","focus:ring-gray-500","border border-gray-300"],outline:["bg-transparent text-blue-600 border-2 border-blue-600","hover:bg-blue-50 active:bg-blue-100","focus:ring-blue-500"],ghost:["bg-transparent text-gray-700","hover:bg-gray-100 active:bg-gray-200","focus:ring-gray-500"],danger:["bg-red-600 text-white","hover:bg-red-700 active:bg-red-800","focus:ring-red-500","shadow-sm hover:shadow-md"]},w={sm:"text-sm px-3 py-1.5 gap-1.5",md:"text-sm px-4 py-2 gap-2",lg:"text-base px-6 py-3 gap-2",xl:"text-lg px-8 py-4 gap-3"},k=n?"w-full":"",h=c||a;return e.jsxDEV("button",{className:we(p.join(" "),b[s].join(" "),w[o],k,r),disabled:h,ref:g,...f,children:a?e.jsxDEV(e.Fragment,{children:[e.jsxDEV("svg",{className:"animate-spin h-4 w-4",fill:"none",viewBox:"0 0 24 24",children:[e.jsxDEV("circle",{className:"opacity-25",cx:"12",cy:"12",r:"10",stroke:"currentColor",strokeWidth:"4"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:100,columnNumber:15},globalThis),e.jsxDEV("path",{className:"opacity-75",fill:"currentColor",d:"M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:108,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:95,columnNumber:13},globalThis),"처리 중..."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:94,columnNumber:11},globalThis):e.jsxDEV(e.Fragment,{children:[i&&e.jsxDEV("span",{className:"flex-shrink-0",children:i},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:119,columnNumber:15},globalThis),e.jsxDEV("span",{children:u},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:123,columnNumber:13},globalThis),l&&e.jsxDEV("span",{className:"flex-shrink-0",children:l},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:125,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:117,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Button.tsx",lineNumber:81,columnNumber:7},globalThis)});A.displayName="Button";const ao=()=>{const{user:r,isAuthenticated:s,logout:o}=le(),{success:a}=fe(),n=Ne(),[i,l]=d.useState(!1),c=async()=>{try{await o(),a("성공적으로 로그아웃되었습니다."),n("/")}catch(g){console.error("Logout error:",g)}},u=[{name:"홈",path:"/",public:!0},{name:"커뮤니티",path:"/community",public:!0},{name:"강의",path:"/lectures",public:!0},{name:"이벤트",path:"/events",public:!0}],f=()=>l(!i);return e.jsxDEV("header",{className:"bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50",children:[e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8",children:[e.jsxDEV("div",{className:"flex justify-between items-center h-16",children:[e.jsxDEV("div",{className:"flex-shrink-0",children:e.jsxDEV("h1",{className:"logo",children:e.jsxDEV(L,{to:"/",className:"logo-link",children:[e.jsxDEV("div",{className:"logo-icon",children:e.jsxDEV("i",{className:"fas fa-rocket header-rocket"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:42,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:41,columnNumber:17},globalThis),e.jsxDEV("span",{className:"logo-text",children:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:44,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:40,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:39,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:38,columnNumber:11},globalThis),e.jsxDEV("nav",{className:"hidden md:flex space-x-8",children:u.map(g=>e.jsxDEV(L,{to:g.path,className:"text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium transition-colors",children:g.name},g.path,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:52,columnNumber:15},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:50,columnNumber:11},globalThis),e.jsxDEV("div",{className:"hidden md:flex items-center space-x-4",children:s&&r?e.jsxDEV("div",{className:"flex items-center space-x-4",children:[e.jsxDEV("div",{className:"flex items-center space-x-3",children:[e.jsxDEV("div",{className:"w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center",children:r.profile_image_thumb?e.jsxDEV("img",{src:r.profile_image_thumb,alt:r.nickname,className:"w-8 h-8 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:70,columnNumber:23},globalThis):e.jsxDEV("span",{className:"text-blue-600 text-sm font-medium",children:r.nickname.charAt(0).toUpperCase()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:76,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:68,columnNumber:19},globalThis),e.jsxDEV("span",{className:"text-sm font-medium text-gray-700",children:r.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:81,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:67,columnNumber:17},globalThis),e.jsxDEV("div",{className:"relative",children:e.jsxDEV("div",{className:"flex items-center space-x-2",children:[e.jsxDEV(L,{to:"/profile",className:"text-gray-600 hover:text-blue-600 text-sm font-medium transition-colors",children:"프로필"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:89,columnNumber:21},globalThis),e.jsxDEV(A,{variant:"ghost",size:"sm",onClick:c,children:"로그아웃"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:95,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:88,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:87,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:65,columnNumber:15},globalThis):e.jsxDEV("div",{className:"flex items-center space-x-3",children:[e.jsxDEV(L,{to:"/login",children:e.jsxDEV(A,{variant:"ghost",size:"sm",children:"로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:108,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:107,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/signup",children:e.jsxDEV(A,{variant:"primary",size:"sm",children:"회원가입"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:113,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:112,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:106,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:63,columnNumber:11},globalThis),e.jsxDEV("div",{className:"md:hidden",children:e.jsxDEV("button",{onClick:f,className:"text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2",children:e.jsxDEV("svg",{className:"h-6 w-6",fill:"none",strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:"2",viewBox:"0 0 24 24",stroke:"currentColor",children:i?e.jsxDEV("path",{d:"M6 18L18 6M6 6l12 12"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:137,columnNumber:19},globalThis):e.jsxDEV("path",{d:"M4 6h16M4 12h16M4 18h16"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:139,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:127,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:123,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:122,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:36,columnNumber:9},globalThis),e.jsxDEV("div",{className:we("md:hidden transition-all duration-300 ease-in-out overflow-hidden",i?"max-h-96 pb-4":"max-h-0"),children:e.jsxDEV("div",{className:"px-2 pt-2 pb-3 space-y-1 border-t border-gray-200 mt-2",children:[u.map(g=>e.jsxDEV(L,{to:g.path,className:"block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors",onClick:()=>l(!1),children:g.name},g.path,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:153,columnNumber:15},globalThis)),e.jsxDEV("div",{className:"border-t border-gray-200 pt-3 mt-3",children:s&&r?e.jsxDEV("div",{className:"space-y-2",children:[e.jsxDEV("div",{className:"flex items-center px-3 py-2",children:[e.jsxDEV("div",{className:"w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3",children:r.profile_image_thumb?e.jsxDEV("img",{src:r.profile_image_thumb,alt:r.nickname,className:"w-10 h-10 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:169,columnNumber:25},globalThis):e.jsxDEV("span",{className:"text-blue-600 font-medium",children:r.nickname.charAt(0).toUpperCase()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:175,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:167,columnNumber:21},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("div",{className:"text-sm font-medium text-gray-900",children:r.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:181,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-xs text-gray-500",children:r.email},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:184,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:180,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:166,columnNumber:19},globalThis),e.jsxDEV(L,{to:"/profile",className:"block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors",onClick:()=>l(!1),children:"프로필"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:189,columnNumber:19},globalThis),e.jsxDEV("button",{onClick:()=>{c(),l(!1)},className:"block w-full text-left px-3 py-2 text-base font-medium text-gray-600 hover:text-red-600 hover:bg-gray-50 rounded-md transition-colors",children:"로그아웃"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:196,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:165,columnNumber:17},globalThis):e.jsxDEV("div",{className:"space-y-2",children:[e.jsxDEV(L,{to:"/login",className:"block px-3 py-2 text-base font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 rounded-md transition-colors",onClick:()=>l(!1),children:"로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:208,columnNumber:19},globalThis),e.jsxDEV(L,{to:"/signup",className:"block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-md transition-colors",onClick:()=>l(!1),children:"회원가입"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:215,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:207,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:163,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:151,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:147,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:35,columnNumber:7},globalThis),e.jsxDEV("style",{children:`
        /* 🚀 헤더 로켓 애니메이션 */
        .header-rocket {
          display: inline-block;
          transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
          transform-origin: center bottom;
          position: relative;
          color: #3b82f6;
          font-size: 1.8rem;
        }
        
        /* 페이지 로딩 시 로켓 착륙 애니메이션 */
        .header-rocket {
          animation: rocketLanding 2.5s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards,
                     headerRocketFloat 4s ease-in-out infinite 2.5s;
        }
        
        @keyframes rocketLanding {
          0% {
            transform: translateX(-150vw) translateY(-50vh) rotate(-45deg) scale(0.3);
            opacity: 0;
            filter: blur(3px);
          }
          20% {
            opacity: 0.3;
            filter: blur(2px);
          }
          40% {
            transform: translateX(-80vw) translateY(-20vh) rotate(-30deg) scale(0.5);
            opacity: 0.6;
            filter: blur(1px);
          }
          60% {
            transform: translateX(-20vw) translateY(-5vh) rotate(-15deg) scale(0.8);
            opacity: 0.8;
            filter: blur(0.5px);
          }
          80% {
            transform: translateX(-5vw) translateY(-1vh) rotate(-5deg) scale(0.95);
            opacity: 0.9;
            filter: blur(0px);
          }
          90% {
            transform: translateX(0) translateY(2px) rotate(5deg) scale(1.1);
            opacity: 1;
          }
          95% {
            transform: translateX(0) translateY(-2px) rotate(-2deg) scale(1.05);
          }
          100% {
            transform: translateX(0) translateY(0) rotate(0deg) scale(1);
            opacity: 1;
            filter: blur(0px);
          }
        }
        
        /* 기본 헤더 로켓 애니메이션 - 우주에서 떠다니는 느낌 (착륙 후) */
        @keyframes headerRocketFloat {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          20% {
            transform: translateY(-2px) rotate(3deg);
          }
          40% {
            transform: translateY(-4px) rotate(0deg);
          }
          60% {
            transform: translateY(-2px) rotate(-3deg);
          }
          80% {
            transform: translateY(-1px) rotate(1deg);
          }
        }
        
        /* 로고 링크 호버 시 로켓 특수 효과 */
        .logo-link {
          position: relative;
          text-decoration: none;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
        }
        
        /* 착륙 시 추진 효과 */
        .logo-icon::before {
          content: '';
          position: absolute;
          bottom: -8px;
          left: 50%;
          width: 0;
          height: 0;
          background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, #ef4444, transparent);
          transform: translateX(-50%);
          opacity: 0;
          transition: all 0.3s ease;
          animation: landingThruster 2.5s ease-out;
        }
        
        .logo-icon::after {
          content: '💨';
          position: absolute;
          left: -35px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0;
          font-size: 0.8rem;
          animation: landingSmoke 2.5s ease-out;
        }
        
        /* 착륙 추진 효과 애니메이션 */
        @keyframes landingThruster {
          0%, 70% {
            width: 0;
            height: 0;
            opacity: 0;
          }
          75% {
            width: 30px;
            height: 3px;
            opacity: 0.8;
            box-shadow: 0 0 10px #fbbf24, 0 0 20px #f59e0b;
          }
          85% {
            width: 40px;
            height: 5px;
            opacity: 1;
            box-shadow: 0 0 15px #fbbf24, 0 0 30px #f59e0b, 0 0 45px #ef4444;
          }
          95% {
            width: 20px;
            height: 2px;
            opacity: 0.5;
          }
          100% {
            width: 0;
            height: 0;
            opacity: 0;
          }
        }
        
        /* 착륙 연기 효과 */
        @keyframes landingSmoke {
          0%, 60% {
            opacity: 0;
            left: -35px;
          }
          70% {
            opacity: 0.8;
            left: -25px;
          }
          80% {
            opacity: 1;
            left: -20px;
          }
          90% {
            opacity: 0.6;
            left: -15px;
          }
          100% {
            opacity: 0;
            left: -10px;
          }
        }
        
        /* 착륙 완료 시 충격파 효과 */
        @keyframes landingShockwave {
          0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7);
          }
          25% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(59, 130, 246, 0.5);
          }
          50% {
            transform: scale(1.05);
            box-shadow: 0 0 0 20px rgba(59, 130, 246, 0.3);
          }
          75% {
            transform: scale(1.02);
            box-shadow: 0 0 0 30px rgba(59, 130, 246, 0.1);
          }
          100% {
            transform: scale(1);
            box-shadow: 0 0 0 40px rgba(59, 130, 246, 0);
          }
        }
        
        /* 로고 아이콘 */
        .logo-icon {
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
          border-radius: 50%;
          background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(29, 78, 216, 0.05));
          transition: all 0.3s ease;
          margin-right: 12px;
          position: relative;
          overflow: visible;
          opacity: 0;
          animation: logoIconAppear 2.6s ease-out forwards,
                     landingShockwave 1s ease-out 2.3s;
        }
        
        @keyframes logoIconAppear {
          0%, 50% {
            opacity: 0;
            transform: scale(0.8);
          }
          70% {
            opacity: 0.5;
            transform: scale(0.9);
          }
          85% {
            opacity: 0.8;
            transform: scale(1.05);
          }
          100% {
            opacity: 1;
            transform: scale(1);
          }
        }
        
        /* 로고 텍스트 */
        .logo-text {
          transition: all 0.3s ease;
          color: #1f2937;
          font-weight: 700;
          font-size: 1.5rem;
          opacity: 0;
          animation: logoTextAppear 2.8s ease-out forwards;
        }
        
        @keyframes logoTextAppear {
          0%, 60% {
            opacity: 0;
            transform: translateY(10px);
          }
          70% {
            opacity: 0.3;
            transform: translateY(5px);
          }
          80% {
            opacity: 0.6;
            transform: translateY(2px);
          }
          90% {
            opacity: 0.8;
            transform: translateY(-1px);
          }
          100% {
            opacity: 1;
            transform: translateY(0);
          }
        }
        
        /* 호버 효과 */
        .logo-link:hover .header-rocket {
          animation: headerRocketIgnition 0.8s ease-in-out;
          transform: translateY(-3px) rotate(-8deg) scale(1.1);
          color: #1d4ed8;
          filter: drop-shadow(0 0 8px rgba(59, 130, 246, 0.4));
        }
        
        @keyframes headerRocketIgnition {
          0% {
            transform: translateY(0px) rotate(0deg) scale(1);
          }
          30% {
            transform: translateY(-1px) rotate(-4deg) scale(1.05);
          }
          60% {
            transform: translateY(-2px) rotate(-6deg) scale(1.08);
          }
          100% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
          }
        }
        
        .logo-link:hover .logo-text {
          color: #1d4ed8;
          text-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
        }
        
        .logo-link:hover .logo-icon {
          background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(29, 78, 216, 0.1));
          box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }
        
        /* 클릭 시 로켓 발사! */
        .logo-link:active .header-rocket {
          animation: headerRocketLaunch 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          transform: translateY(-8px) rotate(-20deg) scale(1.15);
        }
        
        @keyframes headerRocketLaunch {
          0% {
            transform: translateY(-3px) rotate(-8deg) scale(1.1);
          }
          40% {
            transform: translateY(-5px) rotate(-15deg) scale(1.12);
          }
          70% {
            transform: translateY(-10px) rotate(-18deg) scale(1.18);
          }
          100% {
            transform: translateY(-8px) rotate(-20deg) scale(1.15);
          }
        }
        
        /* 로고 */
        .logo {
          margin: 0;
          padding: 0;
        }
        
        /* 모바일 반응형 */
        @media (max-width: 768px) {
          .header-rocket {
            font-size: 1.5rem;
          }
          
          .logo-text {
            font-size: 1.3rem;
          }
          
          .logo-icon {
            width: 35px;
            height: 35px;
            margin-right: 8px;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:230,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Header.tsx",lineNumber:34,columnNumber:5},globalThis)},to=()=>{const{isAuthenticated:r}=le(),s=new Date().getFullYear();return e.jsxDEV("footer",{className:"main-footer modern-footer",children:[e.jsxDEV("div",{className:"container",children:e.jsxDEV("div",{className:"footer-content",children:[e.jsxDEV("div",{className:"footer-top",children:[e.jsxDEV("div",{className:"footer-section",children:[e.jsxDEV("div",{className:"footer-logo",children:[e.jsxDEV("div",{className:"logo-icon",children:e.jsxDEV("i",{className:"fas fa-rocket"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:18,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:17,columnNumber:17},globalThis),e.jsxDEV("span",{className:"logo-text",children:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:20,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:16,columnNumber:15},globalThis),e.jsxDEV("p",{className:"footer-description",children:["글로벌 네트워크 마케팅 리더들의 커뮤니티",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:23,columnNumber:39},globalThis),"함께 성장하고 성공을 만들어가는 플랫폼"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:22,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:15,columnNumber:13},globalThis),e.jsxDEV("div",{className:"footer-section",children:[e.jsxDEV("h3",{className:"footer-title",children:"서비스"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:29,columnNumber:15},globalThis),e.jsxDEV("ul",{className:"footer-links",children:[e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/community",children:"커뮤니티"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:31,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:31,columnNumber:17},globalThis),e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/lectures",children:"강의 일정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:32,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:32,columnNumber:17},globalThis),e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/events",children:"행사 일정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:33,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:33,columnNumber:17},globalThis),!r&&e.jsxDEV(e.Fragment,{children:[e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/login",children:"로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:36,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:36,columnNumber:21},globalThis),e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/signup",children:"회원가입"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:37,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:37,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:35,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:30,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:28,columnNumber:13},globalThis),e.jsxDEV("div",{className:"footer-section",children:[e.jsxDEV("h3",{className:"footer-title",children:"정책"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:44,columnNumber:15},globalThis),e.jsxDEV("ul",{className:"footer-links",children:[e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/terms",children:"이용약관"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:46,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:46,columnNumber:17},globalThis),e.jsxDEV("li",{children:e.jsxDEV(L,{to:"/privacy",children:"개인정보처리방침"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:47,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:47,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:45,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:43,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:14,columnNumber:11},globalThis),e.jsxDEV("div",{className:"footer-bottom",children:e.jsxDEV("div",{className:"footer-copyright",children:[e.jsxDEV("p",{children:["© ",s," 탑마케팅. All rights reserved."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:55,columnNumber:15},globalThis),e.jsxDEV("p",{className:"company-info",children:["상호명: (주)윈카드 | 대표자: 이정현 | 사업자등록번호: 133-88-02437 | 전화번호: ",e.jsxDEV("a",{href:"tel:070-4138-8899",children:"070-4138-8899"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:58,columnNumber:23},globalThis)," | 이메일: ",e.jsxDEV("a",{href:"mailto:jh@wincard.kr",children:"jh@wincard.kr"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:58,columnNumber:76},globalThis)," | 주소: 서울시 금천구 가산디지털1로 204, 반도 아이비밸리 6층"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:56,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:54,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:53,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:12,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:11,columnNumber:7},globalThis),e.jsxDEV("style",{children:`
        .main-footer {
          background: #1a1a1a;
          color: #e5e5e5;
          padding: 60px 0 30px;
          margin-top: auto;
        }

        .footer-content {
          max-width: 1200px;
          margin: 0 auto;
        }

        .footer-top {
          display: grid;
          grid-template-columns: 2fr 1fr 1fr;
          gap: 60px;
          margin-bottom: 50px;
        }

        .footer-section {
          display: flex;
          flex-direction: column;
        }

        .footer-logo {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 20px;
        }

        .footer-logo .logo-icon {
          width: 40px;
          height: 40px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.2rem;
        }

        .footer-logo .logo-text {
          font-size: 1.5rem;
          font-weight: 700;
          color: #fff;
        }

        .footer-description {
          color: #b0b0b0;
          line-height: 1.6;
          margin-bottom: 0;
        }

        .footer-title {
          font-size: 1.1rem;
          font-weight: 600;
          color: #fff;
          margin-bottom: 20px;
        }

        .footer-links {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .footer-links li {
          margin-bottom: 12px;
        }

        .footer-links a {
          color: #b0b0b0;
          text-decoration: none;
          transition: color 0.3s ease;
          font-size: 0.95rem;
        }

        .footer-links a:hover {
          color: #667eea;
        }

        .footer-bottom {
          border-top: 1px solid #333;
          padding-top: 30px;
        }

        .footer-copyright {
          text-align: left;
        }

        .footer-copyright p {
          margin: 0 0 8px 0;
          color: #888;
          font-size: 0.9rem;
        }

        .company-info {
          font-size: 0.85rem !important;
          line-height: 1.5;
        }

        .company-info a {
          color: #667eea;
          text-decoration: none;
        }

        .company-info a:hover {
          text-decoration: underline;
        }

        /* 반응형 */
        @media (max-width: 768px) {
          .footer-top {
            grid-template-columns: 1fr;
            gap: 40px;
          }

          .footer-logo .logo-text {
            font-size: 1.3rem;
          }

          .company-info {
            font-size: 0.8rem !important;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:66,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Footer.tsx",lineNumber:10,columnNumber:5},globalThis)},be=({size:r="md",message:s,overlay:o=!1,className:a})=>{const n={sm:"w-4 h-4",md:"w-8 h-8",lg:"w-12 h-12",xl:"w-16 h-16"},i=e.jsxDEV("div",{className:we("flex flex-col items-center justify-center",a),children:[e.jsxDEV("div",{className:"relative",children:e.jsxDEV("div",{className:we("animate-spin rounded-full border-4 border-gray-200 border-t-blue-600",n[r])},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:27,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:26,columnNumber:7},globalThis),s&&e.jsxDEV("p",{className:"mt-3 text-sm text-gray-600 text-center max-w-xs",children:s},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:35,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:25,columnNumber:5},globalThis);return o?e.jsxDEV("div",{className:"fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50",children:e.jsxDEV("div",{className:"bg-white rounded-lg p-8 max-w-sm w-full mx-4",children:i},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:45,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/LoadingSpinner.tsx",lineNumber:44,columnNumber:7},globalThis):i},Ir=()=>{const{toasts:r,removeToast:s}=fe(),o=n=>{switch(n){case"success":return e.jsxDEV("svg",{className:"w-6 h-6 text-green-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:13,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:12,columnNumber:11},globalThis);case"error":return e.jsxDEV("svg",{className:"w-6 h-6 text-red-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:19,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:18,columnNumber:11},globalThis);case"warning":return e.jsxDEV("svg",{className:"w-6 h-6 text-yellow-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:25,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:24,columnNumber:11},globalThis);case"info":return e.jsxDEV("svg",{className:"w-6 h-6 text-blue-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:31,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:30,columnNumber:11},globalThis);default:return null}},a=n=>{switch(n){case"success":return"bg-white border-l-4 border-green-400 shadow-lg";case"error":return"bg-white border-l-4 border-red-400 shadow-lg";case"warning":return"bg-white border-l-4 border-yellow-400 shadow-lg";case"info":return"bg-white border-l-4 border-blue-400 shadow-lg";default:return"bg-white border-l-4 border-gray-400 shadow-lg"}};return r.length===0?null:e.jsxDEV("div",{className:"fixed top-4 right-4 z-50 space-y-2 max-w-md w-full",children:r.map(n=>e.jsxDEV("div",{className:we("transform transition-all duration-300 ease-in-out","animate-in slide-in-from-right-full",a(n.type),"rounded-lg p-4 backdrop-blur-sm"),children:e.jsxDEV("div",{className:"flex items-start",children:[e.jsxDEV("div",{className:"flex-shrink-0",children:o(n.type)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:69,columnNumber:13},globalThis),e.jsxDEV("div",{className:"ml-3 flex-1",children:[n.title&&e.jsxDEV("p",{className:"text-sm font-semibold text-gray-900 mb-1",children:n.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:74,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-700",children:n.message},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:78,columnNumber:15},globalThis),n.action&&e.jsxDEV("div",{className:"mt-3",children:e.jsxDEV("button",{onClick:n.action.onClick,className:"text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors",children:n.action.label},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:83,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:82,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:72,columnNumber:13},globalThis),e.jsxDEV("div",{className:"ml-4 flex-shrink-0",children:e.jsxDEV("button",{onClick:()=>s(n.id),className:"inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors",children:e.jsxDEV("svg",{className:"w-4 h-4",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M6 18L18 6M6 6l12 12"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:98,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:97,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:93,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:92,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:68,columnNumber:11},globalThis)},n.id,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:59,columnNumber:9},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ToastContainer.tsx",lineNumber:57,columnNumber:5},globalThis)},$r=d.createContext(void 0),lo=({children:r})=>{const[s,o]=d.useState({isLoading:!1,message:void 0}),l={loading:s,setLoading:(c,u)=>{o({isLoading:c,message:u})},startLoading:c=>{o({isLoading:!0,message:c})},stopLoading:()=>{o({isLoading:!1,message:void 0})}};return e.jsxDEV($r.Provider,{value:l,children:r},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/context/LoadingContext.tsx",lineNumber:48,columnNumber:5},globalThis)},Br=()=>{const r=d.useContext($r);if(r===void 0)throw new Error("useLoading must be used within a LoadingProvider");return r},dr=({children:r})=>{const{loading:s}=Br();return e.jsxDEV("div",{className:"min-h-screen flex flex-col bg-gray-50",children:[e.jsxDEV(ao,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:18,columnNumber:7},globalThis),e.jsxDEV("main",{className:"flex-grow",children:r||e.jsxDEV(vs,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:21,columnNumber:22},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:20,columnNumber:7},globalThis),e.jsxDEV(to,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:24,columnNumber:7},globalThis),s.isLoading&&e.jsxDEV(be,{message:s.message,overlay:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:28,columnNumber:9},globalThis),e.jsxDEV(Ir,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:35,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Layout.tsx",lineNumber:17,columnNumber:5},globalThis)},Qe=({children:r,requiredRole:s,requireRole:o,fallback:a})=>{const n=o||s,{isAuthenticated:i,user:l,isLoading:c}=le(),u=Lr();if(c)return e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"인증 정보를 확인하고 있습니다..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:28,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:27,columnNumber:7},globalThis);if(!i||!l)return e.jsxDEV(Sr,{to:"/login",state:{from:u.pathname},replace:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:36,columnNumber:7},globalThis);if(n&&l.role!==n){if((n==="ROLE_ADMIN"||n==="admin")&&l.role!=="ROLE_ADMIN")return a||e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center bg-gray-50",children:e.jsxDEV("div",{className:"max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-red-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:53,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:52,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:51,columnNumber:13},globalThis),e.jsxDEV("h2",{className:"text-xl font-semibold text-gray-900 mb-2",children:"접근 권한이 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:56,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-gray-600 mb-6",children:"관리자 권한이 필요한 페이지입니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:59,columnNumber:13},globalThis),e.jsxDEV("button",{onClick:()=>window.history.back(),className:"inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors",children:"이전 페이지로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:62,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:50,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:49,columnNumber:9},globalThis);if(s==="ROLE_CORP"&&l.role==="ROLE_USER")return a||e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center bg-gray-50",children:e.jsxDEV("div",{className:"max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-yellow-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-5a2 2 0 012-2h2a2 2 0 012 2v5"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:80,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:79,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:78,columnNumber:13},globalThis),e.jsxDEV("h2",{className:"text-xl font-semibold text-gray-900 mb-2",children:"기업회원 권한이 필요합니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:83,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-gray-600 mb-6",children:"이 기능을 사용하려면 기업회원으로 등록해야 합니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:86,columnNumber:13},globalThis),e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV("a",{href:"/corp/apply",className:"block w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors",children:"기업회원 신청하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:90,columnNumber:15},globalThis),e.jsxDEV("button",{onClick:()=>window.history.back(),className:"block w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors",children:"이전 페이지로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:96,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:89,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:77,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:76,columnNumber:9},globalThis)}return e.jsxDEV(e.Fragment,{children:r},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/ProtectedRoute.tsx",lineNumber:110,columnNumber:10},globalThis)},io=()=>{const{isAuthenticated:r}=le();return e.jsxDEV("div",{className:"min-h-screen",children:[e.jsxDEV("section",{className:"hero-section modern-hero",children:[e.jsxDEV("div",{className:"hero-background",children:[e.jsxDEV("div",{className:"gradient-overlay"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:13,columnNumber:11},globalThis),e.jsxDEV("div",{className:"animated-shapes",children:[e.jsxDEV("div",{className:"shape shape-1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:15,columnNumber:13},globalThis),e.jsxDEV("div",{className:"shape shape-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:16,columnNumber:13},globalThis),e.jsxDEV("div",{className:"shape shape-3"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:17,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:14,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:12,columnNumber:9},globalThis),e.jsxDEV("div",{className:"container",children:e.jsxDEV("div",{className:"hero-content",children:[e.jsxDEV("div",{className:"hero-badge",children:[e.jsxDEV("span",{className:"badge-icon",children:"🚀"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:23,columnNumber:15},globalThis),e.jsxDEV("span",{className:"badge-text",children:"네트워크 마케팅의 새로운 패러다임"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:24,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:22,columnNumber:13},globalThis),e.jsxDEV("h1",{className:"hero-title",children:[e.jsxDEV("span",{className:"gradient-text",children:"글로벌 리더들과 함께"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:27,columnNumber:15},globalThis),e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:27,columnNumber:65},globalThis),e.jsxDEV("span",{className:"typing-effect",children:"성공을 만들어가세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:28,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:26,columnNumber:13},globalThis),e.jsxDEV("p",{className:"hero-description",children:["전 세계 네트워크 마케팅 전문가들이 모인 커뮤니티에서",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:31,columnNumber:44},globalThis),"지식을 공유하고, 인사이트를 얻으며, 함께 성장하세요"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:30,columnNumber:13},globalThis),e.jsxDEV("div",{className:"hero-actions",children:[e.jsxDEV(L,{to:r?"/community":"/signup",className:"btn btn-primary-gradient rocket-launch-btn",children:[e.jsxDEV("span",{children:"무료로 시작하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:39,columnNumber:17},globalThis),e.jsxDEV("i",{className:"fas fa-rocket rocket-icon"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:40,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:35,columnNumber:15},globalThis),e.jsxDEV("a",{href:"#features",className:"btn btn-ghost",children:[e.jsxDEV("i",{className:"fas fa-play"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:43,columnNumber:17},globalThis),e.jsxDEV("span",{children:"둘러보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:44,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:42,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:34,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:21,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:20,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:11,columnNumber:7},globalThis),e.jsxDEV("section",{id:"features",className:"features-section",children:e.jsxDEV("div",{className:"container",children:[e.jsxDEV("div",{className:"section-header",children:[e.jsxDEV("span",{className:"section-badge",children:"핵심 기능"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:55,columnNumber:13},globalThis),e.jsxDEV("h2",{className:"section-title",children:"탑마케팅이 제공하는 가치"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:56,columnNumber:13},globalThis),e.jsxDEV("p",{className:"section-subtitle",children:"성공적인 네트워크 마케팅을 위한 모든 도구가 여기에"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:57,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:54,columnNumber:11},globalThis),e.jsxDEV("div",{className:"features-grid",children:[e.jsxDEV("div",{className:"feature-card featured",children:[e.jsxDEV("div",{className:"feature-icon",children:e.jsxDEV("div",{className:"icon-bg",children:e.jsxDEV("i",{className:"fas fa-users"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:64,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:63,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:62,columnNumber:15},globalThis),e.jsxDEV("h3",{children:"커뮤니티 네트워킹"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:67,columnNumber:15},globalThis),e.jsxDEV("p",{children:"전 세계 네트워크 마케팅 전문가들과 연결되어 경험과 노하우를 공유하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:68,columnNumber:15},globalThis),e.jsxDEV(L,{to:"/community",className:"feature-link",children:[e.jsxDEV("span",{children:"시작하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:70,columnNumber:17},globalThis),e.jsxDEV("i",{className:"fas fa-arrow-right"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:71,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:69,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:61,columnNumber:13},globalThis),e.jsxDEV("div",{className:"feature-card",children:[e.jsxDEV("div",{className:"feature-icon",children:e.jsxDEV("div",{className:"icon-bg green",children:e.jsxDEV("i",{className:"fas fa-graduation-cap"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:78,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:77,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:76,columnNumber:15},globalThis),e.jsxDEV("h3",{children:"전문 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:81,columnNumber:15},globalThis),e.jsxDEV("p",{children:"업계 전문가들의 실전 강의를 통해 실무 역량을 키워보세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:82,columnNumber:15},globalThis),e.jsxDEV(L,{to:"/lectures",className:"feature-link",children:[e.jsxDEV("span",{children:"강의듣기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:84,columnNumber:17},globalThis),e.jsxDEV("i",{className:"fas fa-arrow-right"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:85,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:83,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:75,columnNumber:13},globalThis),e.jsxDEV("div",{className:"feature-card",children:[e.jsxDEV("div",{className:"feature-icon",children:e.jsxDEV("div",{className:"icon-bg purple",children:e.jsxDEV("i",{className:"fas fa-calendar-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:92,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:91,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:90,columnNumber:15},globalThis),e.jsxDEV("h3",{children:"행사 참여"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:95,columnNumber:15},globalThis),e.jsxDEV("p",{children:"다양한 네트워킹 행사와 컨퍼런스에 참여하여 새로운 기회를 만나보세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:96,columnNumber:15},globalThis),e.jsxDEV(L,{to:"/events",className:"feature-link",children:[e.jsxDEV("span",{children:"둘러보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:98,columnNumber:17},globalThis),e.jsxDEV("i",{className:"fas fa-arrow-right"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:99,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:97,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:89,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:60,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:53,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:52,columnNumber:7},globalThis),e.jsxDEV("section",{className:"cta-section",children:e.jsxDEV("div",{className:"container",children:e.jsxDEV("div",{className:"cta-content",children:[e.jsxDEV("div",{className:"cta-text",children:[e.jsxDEV("h2",{children:"성공의 여정을 함께 시작하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:111,columnNumber:15},globalThis),e.jsxDEV("p",{children:"전 세계 네트워크 마케팅 리더들과 연결되어 새로운 기회를 발견하고 성공을 만들어가세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:112,columnNumber:15},globalThis),e.jsxDEV("ul",{className:"cta-benefits",children:[e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-check"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:114,columnNumber:21},globalThis)," 무료 회원가입 및 기본 기능 이용"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:114,columnNumber:17},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-check"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:115,columnNumber:21},globalThis)," 전문가 네트워크 액세스"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:115,columnNumber:17},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-check"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:116,columnNumber:21},globalThis)," 독점 행사 및 강의 참여"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:116,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:113,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:110,columnNumber:13},globalThis),e.jsxDEV("div",{className:"cta-actions",children:[e.jsxDEV(L,{to:r?"/community":"/signup",className:"btn btn-primary-gradient btn-large rocket-launch-btn",children:[e.jsxDEV("span",{children:"지금 시작하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:124,columnNumber:17},globalThis),e.jsxDEV("i",{className:"fas fa-rocket rocket-icon"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:125,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:120,columnNumber:15},globalThis),e.jsxDEV("p",{className:"cta-note",children:"가입은 무료이며, 언제든지 탈퇴 가능합니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:127,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:119,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:109,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:108,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:107,columnNumber:7},globalThis),e.jsxDEV("style",{children:`
        /* 로켓 애니메이션 효과 */
        .rocket-icon {
          display: inline-block;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          transform-origin: center bottom;
          position: relative;
        }

        .rocket-launch-btn {
          position: relative;
          overflow: hidden;
          transition: all 0.3s ease;
        }

        .rocket-launch-btn::before {
          content: '';
          position: absolute;
          bottom: -2px;
          left: 50%;
          width: 0;
          height: 2px;
          background: linear-gradient(90deg, transparent, #fbbf24, #f59e0b, #d97706, transparent);
          transform: translateX(-50%);
          transition: width 0.6s ease;
        }

        .rocket-launch-btn::after {
          content: '💨';
          position: absolute;
          left: -30px;
          top: 50%;
          transform: translateY(-50%);
          opacity: 0;
          font-size: 0.8rem;
          transition: all 0.3s ease;
        }

        /* 기본 로켓 애니메이션 - 둥둥 떠다니기 */
        .rocket-icon {
          animation: rocketFloat 3s ease-in-out infinite;
        }

        @keyframes rocketFloat {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          25% {
            transform: translateY(-3px) rotate(2deg);
          }
          50% {
            transform: translateY(-6px) rotate(0deg);
          }
          75% {
            transform: translateY(-3px) rotate(-2deg);
          }
        }

        /* 호버 시 로켓 발사 준비 */
        .rocket-launch-btn:hover .rocket-icon {
          animation: rocketPrepare 0.6s ease-in-out;
          transform: translateY(-5px) rotate(-10deg) scale(1.1);
        }

        @keyframes rocketPrepare {
          0% {
            transform: translateY(0px) rotate(0deg) scale(1);
          }
          50% {
            transform: translateY(-2px) rotate(-5deg) scale(1.05);
          }
          100% {
            transform: translateY(-5px) rotate(-10deg) scale(1.1);
          }
        }

        /* 호버 시 추진 불꽃 효과 */
        .rocket-launch-btn:hover::before {
          width: 60px;
          animation: thrusterFlame 0.3s ease-in-out infinite alternate;
        }

        .rocket-launch-btn:hover::after {
          opacity: 1;
          left: -15px;
          animation: smokeTrail 1s ease-in-out infinite;
        }

        @keyframes thrusterFlame {
          0% {
            height: 2px;
            box-shadow: 0 0 5px #fbbf24;
          }
          100% {
            height: 4px;
            box-shadow: 0 0 10px #f59e0b, 0 0 20px #d97706;
          }
        }

        @keyframes smokeTrail {
          0% {
            opacity: 0.8;
            transform: translateY(-50%) scale(1);
          }
          100% {
            opacity: 0.4;
            transform: translateY(-50%) scale(1.2);
          }
        }

        /* 클릭 시 로켓 발사 애니메이션 */
        .rocket-launch-btn:active .rocket-icon {
          animation: rocketLaunch 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
          transform: translateY(-20px) rotate(-15deg) scale(1.2);
        }

        @keyframes rocketLaunch {
          0% {
            transform: translateY(-5px) rotate(-10deg) scale(1.1);
          }
          30% {
            transform: translateY(-15px) rotate(-12deg) scale(1.15);
          }
          60% {
            transform: translateY(-25px) rotate(-15deg) scale(1.25);
          }
          100% {
            transform: translateY(-20px) rotate(-15deg) scale(1.2);
          }
        }

        /* 클릭 시 강력한 추진력 효과 */
        .rocket-launch-btn:active::before {
          width: 80px;
          height: 6px;
          box-shadow: 
            0 0 15px #fbbf24, 
            0 0 30px #f59e0b, 
            0 0 45px #d97706,
            0 2px 0 #ef4444,
            0 4px 0 #dc2626;
          animation: superThruster 0.2s ease-in-out infinite;
        }

        .rocket-launch-btn:active::after {
          content: '💨💨💨';
          left: -40px;
          font-size: 1rem;
          animation: intenseSmokeTrail 0.4s ease-in-out infinite;
        }

        @keyframes superThruster {
          0% {
            transform: translateX(-50%) scaleX(1);
          }
          100% {
            transform: translateX(-50%) scaleX(1.1);
          }
        }

        @keyframes intenseSmokeTrail {
          0% {
            opacity: 1;
            transform: translateY(-50%) translateX(0) scale(1);
          }
          100% {
            opacity: 0.6;
            transform: translateY(-50%) translateX(-10px) scale(1.3);
          }
        }

        /* 터치 기기를 위한 추가 효과 */
        @media (hover: hover) {
          .rocket-launch-btn:hover {
            transform: translateY(-2px);
            box-shadow: 
              0 8px 25px rgba(102, 126, 234, 0.3),
              0 4px 15px rgba(102, 126, 234, 0.2),
              0 0 0 1px rgba(255, 255, 255, 0.1);
          }
        }

        /* 모바일에서의 터치 효과 */
        @media (hover: none) {
          .rocket-launch-btn:active {
            transform: translateY(-1px) scale(0.98);
          }
        }

        /* 히어로 섹션 기본 스타일 */
        .hero-section {
          position: relative;
          min-height: 70vh;
          display: flex;
          align-items: center;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          overflow: hidden;
        }

        .hero-background {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 1;
        }

        .gradient-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .animated-shapes {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 2;
        }

        .shape {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.1);
          animation: float 6s ease-in-out infinite;
        }

        .shape-1 {
          width: 100px;
          height: 100px;
          top: 20%;
          left: 10%;
          animation-delay: 0s;
        }

        .shape-2 {
          width: 150px;
          height: 150px;
          top: 60%;
          right: 15%;
          animation-delay: 2s;
        }

        .shape-3 {
          width: 80px;
          height: 80px;
          bottom: 20%;
          left: 20%;
          animation-delay: 4s;
        }

        @keyframes float {
          0%, 100% {
            transform: translateY(0px) rotate(0deg);
          }
          50% {
            transform: translateY(-20px) rotate(180deg);
          }
        }

        .container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 1rem;
          position: relative;
          z-index: 3;
        }

        .hero-content {
          text-align: center;
          color: white;
          max-width: 800px;
          margin: 0 auto;
        }

        .hero-badge {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          background: rgba(255, 255, 255, 0.1);
          padding: 8px 16px;
          border-radius: 50px;
          margin-bottom: 32px;
          backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .badge-icon {
          font-size: 1.2rem;
        }

        .badge-text {
          font-size: 0.9rem;
          font-weight: 500;
        }

        .hero-title {
          font-size: 3.5rem;
          font-weight: 700;
          line-height: 1.1;
          margin-bottom: 24px;
        }

        .gradient-text {
          color: #fbbf24;
          font-weight: 700;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .typing-effect {
          display: inline-block;
          position: relative;
        }

        .typing-effect::after {
          content: '|';
          position: absolute;
          right: -8px;
          top: 0;
          color: white;
          font-weight: 400;
          animation: blink 1s infinite;
        }

        @keyframes blink {
          0%, 50% {
            opacity: 1;
          }
          51%, 100% {
            opacity: 0;
          }
        }

        .hero-description {
          font-size: 1.25rem;
          line-height: 1.6;
          margin-bottom: 40px;
          opacity: 0.9;
        }

        .hero-actions {
          display: flex;
          gap: 16px;
          justify-content: center;
          flex-wrap: wrap;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 16px 32px;
          border-radius: 12px;
          font-weight: 600;
          font-size: 1rem;
          text-decoration: none;
          transition: all 0.3s ease;
          border: none;
          cursor: pointer;
        }

        .btn-primary-gradient {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-ghost {
          background: rgba(255, 255, 255, 0.1);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
          backdrop-filter: blur(10px);
        }

        .btn-ghost:hover {
          background: rgba(255, 255, 255, 0.2);
          color: white;
        }

        /* 기능 섹션 */
        .features-section {
          padding: 80px 0;
          background: #f8fafc;
        }

        .section-header {
          text-align: center;
          margin-bottom: 60px;
        }

        .section-badge {
          display: inline-block;
          background: rgba(102, 126, 234, 0.1);
          color: #667eea;
          padding: 8px 16px;
          border-radius: 50px;
          font-size: 0.875rem;
          font-weight: 600;
          margin-bottom: 16px;
        }

        .section-title {
          font-size: 2.5rem;
          font-weight: 700;
          color: #1f2937;
          margin-bottom: 16px;
        }

        .section-subtitle {
          font-size: 1.125rem;
          color: #6b7280;
          max-width: 600px;
          margin: 0 auto;
        }

        .features-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 32px;
          max-width: 1000px;
          margin: 0 auto;
        }

        .feature-card {
          background: white;
          padding: 40px 32px;
          border-radius: 20px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          text-align: center;
          transition: all 0.3s ease;
          border: 1px solid #e5e7eb;
        }

        .feature-card:hover {
          transform: translateY(-8px);
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-card.featured {
          border: 2px solid #667eea;
          position: relative;
        }

        .feature-card.featured::before {
          content: '인기';
          position: absolute;
          top: -10px;
          left: 32px;
          background: #667eea;
          color: white;
          padding: 4px 12px;
          border-radius: 6px;
          font-size: 0.75rem;
          font-weight: 600;
        }

        .feature-icon {
          margin-bottom: 24px;
        }

        .icon-bg {
          width: 80px;
          height: 80px;
          border-radius: 20px;
          display: flex;
          align-items: center;
          justify-content: center;
          margin: 0 auto;
          background: #667eea;
        }

        .icon-bg.green {
          background: #10b981;
        }

        .icon-bg.purple {
          background: #8b5cf6;
        }

        .icon-bg i {
          font-size: 2rem;
          color: white;
        }

        .feature-card h3 {
          font-size: 1.5rem;
          font-weight: 700;
          color: #1f2937;
          margin-bottom: 16px;
        }

        .feature-card p {
          font-size: 1rem;
          color: #6b7280;
          line-height: 1.6;
          margin-bottom: 24px;
        }

        .feature-link {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          color: #667eea;
          font-weight: 600;
          text-decoration: none;
          transition: all 0.3s ease;
        }

        .feature-link:hover {
          color: #5a67d8;
          transform: translateX(4px);
        }

        /* CTA 섹션 */
        .cta-section {
          padding: 80px 0;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
        }

        .cta-content {
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 60px;
          align-items: center;
          max-width: 1000px;
          margin: 0 auto;
        }

        .cta-text h2 {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 20px;
        }

        .cta-text p {
          font-size: 1.125rem;
          margin-bottom: 32px;
          opacity: 0.9;
          line-height: 1.6;
        }

        .cta-benefits {
          list-style: none;
          padding: 0;
        }

        .cta-benefits li {
          display: flex;
          align-items: center;
          gap: 12px;
          margin-bottom: 12px;
          font-size: 1rem;
        }

        .cta-benefits i {
          color: #10b981;
          font-size: 1.125rem;
        }

        .cta-actions {
          text-align: center;
        }

        .btn-large {
          padding: 20px 40px;
          font-size: 1.125rem;
        }

        .cta-note {
          margin-top: 16px;
          font-size: 0.875rem;
          opacity: 0.7;
        }

        /* 반응형 디자인 */
        @media (max-width: 768px) {
          .hero-title {
            font-size: 2.5rem;
          }

          .hero-description {
            font-size: 1.125rem;
          }

          .hero-actions {
            flex-direction: column;
            align-items: center;
          }

          .section-title {
            font-size: 2rem;
          }

          .features-grid {
            grid-template-columns: 1fr;
            gap: 24px;
          }

          .cta-content {
            grid-template-columns: 1fr;
            gap: 40px;
            text-align: center;
          }

          .cta-text h2 {
            font-size: 2rem;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:134,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/HomePage.tsx",lineNumber:9,columnNumber:5},globalThis)},Yr=r=>{const{title:s,description:o,keywords:a,ogType:n="website",ogTitle:i,ogDescription:l,ogImage:c,structuredData:u}=r,f="탑마케팅",g="마케팅 전문가들이 모여 지식을 공유하고 함께 성장하는 플랫폼입니다. 세미나, 워크샵, 커뮤니티를 통해 최신 마케팅 트렌드를 만나보세요.",p=`${window.location.origin}/assets/images/topmkt-og-image.png?v=${new Date().toISOString().slice(0,10).replace(/-/g,"")}`,b=s?`${s} - ${f}`:`${f} - 마케팅 전문가들의 지식 공유 플랫폼`,w=o||g,k=i||b,h=l||w,C=c||p,_=window.location.href;return{title:b,description:w,keywords:a||"마케팅, 네트워크 마케팅, 세미나, 워크샵, 커뮤니티, 마케팅 교육, 온라인 강의, 탑마케팅, TopMKT, 비즈니스 매칭, 마케팅 플랫폼",ogType:n,ogTitle:k,ogDescription:h,ogImage:C,ogUrl:_,structuredData:u}},qr=({title:r,description:s,keywords:o,ogType:a,ogTitle:n,ogDescription:i,ogImage:l,ogUrl:c,structuredData:u})=>e.jsxDEV(Js,{children:[e.jsxDEV("title",{children:r},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:30,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"description",content:s},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:31,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"keywords",content:o},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:32,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"author",content:"(주)윈카드"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:33,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"robots",content:"index, follow"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:34,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"googlebot",content:"index, follow"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:35,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"theme-color",content:"#6366f1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:36,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"msapplication-navbutton-color",content:"#6366f1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:37,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"apple-mobile-web-app-status-bar-style",content:"black-translucent"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:38,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"apple-mobile-web-app-capable",content:"yes"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:39,columnNumber:7},globalThis),e.jsxDEV("meta",{name:"mobile-web-app-capable",content:"yes"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:40,columnNumber:7},globalThis),e.jsxDEV("link",{rel:"canonical",href:c},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:43,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:type",content:a},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:46,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:url",content:c},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:47,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:title",content:n},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:48,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:description",content:i},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:49,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:image",content:l},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:50,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:image:width",content:"1200"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:51,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:image:height",content:"630"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:52,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:site_name",content:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:53,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"og:locale",content:"ko_KR"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:54,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"twitter:card",content:"summary_large_image"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:57,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"twitter:url",content:c},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:58,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"twitter:title",content:n},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:59,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"twitter:description",content:i},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:60,columnNumber:7},globalThis),e.jsxDEV("meta",{property:"twitter:image",content:l},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:61,columnNumber:7},globalThis),u&&e.jsxDEV("script",{type:"application/ld+json",children:JSON.stringify(u)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:65,columnNumber:9},globalThis),e.jsxDEV("script",{type:"application/ld+json",children:JSON.stringify({"@context":"https://schema.org","@type":"WebSite",name:"탑마케팅",alternateName:"TopMKT",url:"https://www.topmktx.com",description:"글로벌 네트워크 마케팅 리더들의 커뮤니티 플랫폼",publisher:{"@type":"Organization",name:"(주)윈카드",logo:{"@type":"ImageObject",url:"https://www.topmktx.com/assets/images/logo.png"}},potentialAction:{"@type":"SearchAction",target:"https://www.topmktx.com/community?search={search_term_string}","query-input":"required name=search_term_string"}})},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:71,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/SEOHead.tsx",lineNumber:28,columnNumber:5},globalThis),co=()=>{const[r,s]=d.useState({phone:"",password:"",remember:!1,csrf_token:"",redirect:""}),[o,a]=d.useState({}),[n,i]=d.useState(!1),[l,c]=d.useState(!1),{login:u,isAuthenticated:f}=le(),{success:g,error:p}=fe(),b=Ne(),w=Lr(),k=Yr({title:"로그인",description:"탑마케팅에 로그인하여 네트워크 마케팅 커뮤니티에 참여하세요",ogType:"website"});d.useEffect(()=>{if(f){const N=new URLSearchParams(w.search).get("redirect")||"/";b(N,{replace:!0})}},[f,b,w]);const h=N=>{let m=N.replace(/[^0-9]/g,"");return m.length>11&&(m=m.substring(0,11)),m.length>0&&m.length>=3&&!m.startsWith("010")?a(E=>({...E,phone:"010으로 시작하는 휴대폰 번호만 입력할 수 있습니다."})):a(E=>({...E,phone:""})),m.length>=3&&(m=m.substring(0,3)+"-"+m.substring(3)),m.length>=8&&(m=m.substring(0,8)+"-"+m.substring(8,12)),m},C=N=>{const{name:m,value:E,type:D,checked:z}=N.target;let T=D==="checkbox"?z:E;m==="phone"&&(T=h(E)),s(P=>({...P,[m]:T}))},_=async N=>{if(N.preventDefault(),!r.phone.trim()||!r.password.trim()){p("휴대폰 번호와 비밀번호를 모두 입력해주세요.","");return}if(!/^010-[0-9]{3,4}-[0-9]{4}$/.test(r.phone)){p("010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.","");return}i(!0);try{await u(r.phone,r.password,r.remember),g("로그인되었습니다!","환영합니다");const m=new URLSearchParams(w.search).get("redirect")||"/";b(m,{replace:!0})}catch{p("로그인에 실패했습니다.","휴대폰 번호와 비밀번호를 확인해주세요")}finally{i(!1)}},v=()=>{s(N=>({...N,phone:"010-0000-0000",password:"admin123!"}))};return e.jsxDEV(e.Fragment,{children:[e.jsxDEV(qr,{...k},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:123,columnNumber:7},globalThis),e.jsxDEV("section",{className:"auth-section",children:[e.jsxDEV("div",{className:"auth-background",children:[e.jsxDEV("div",{className:"auth-gradient-overlay"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:128,columnNumber:11},globalThis),e.jsxDEV("div",{className:"auth-shapes",children:[e.jsxDEV("div",{className:"auth-shape auth-shape-1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:130,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-shape auth-shape-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:131,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-shape auth-shape-3"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:132,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:129,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:127,columnNumber:9},globalThis),e.jsxDEV("div",{className:"container",children:e.jsxDEV("div",{className:"auth-content",children:[e.jsxDEV("div",{className:"auth-form-container",children:[e.jsxDEV("div",{className:"auth-header",children:[e.jsxDEV("div",{className:"auth-logo",children:[e.jsxDEV("div",{className:"logo-icon",children:e.jsxDEV("i",{className:"fas fa-rocket"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:144,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:143,columnNumber:19},globalThis),e.jsxDEV("span",{className:"logo-text",children:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:146,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:142,columnNumber:17},globalThis),e.jsxDEV("h1",{className:"auth-title",children:"다시 만나서 반갑습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:148,columnNumber:17},globalThis),e.jsxDEV("p",{className:"auth-subtitle",children:"계정에 로그인하여 커뮤니티 활동을 계속하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:149,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:141,columnNumber:15},globalThis),e.jsxDEV("form",{className:"auth-form",onSubmit:_,id:"login-form",children:[e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"phone",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-mobile-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:156,columnNumber:21},globalThis),"휴대폰 번호"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:155,columnNumber:19},globalThis),e.jsxDEV("input",{type:"tel",id:"phone",name:"phone",className:`form-input ${o.phone?"error":""}`,placeholder:"010-1234-5678",value:r.phone,onChange:C,required:!0,autoComplete:"tel",pattern:"010-[0-9]{3,4}-[0-9]{4}",maxLength:13},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:159,columnNumber:19},globalThis),o.phone&&e.jsxDEV("div",{className:"error-message",children:o.phone},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:172,columnNumber:36},globalThis),e.jsxDEV("small",{className:"form-help",children:"회원가입 시 사용한 휴대폰 번호를 입력하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:173,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:154,columnNumber:17},globalThis),e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"password",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-lock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:178,columnNumber:21},globalThis),"비밀번호"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:177,columnNumber:19},globalThis),e.jsxDEV("div",{className:"password-input-wrapper relative",children:[e.jsxDEV("input",{type:l?"text":"password",id:"password",name:"password",className:"form-input",placeholder:"비밀번호를 입력하세요",value:r.password,onChange:C,required:!0,autoComplete:"current-password"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:182,columnNumber:21},globalThis),e.jsxDEV("button",{type:"button",className:"password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600",onClick:()=>c(!l),children:e.jsxDEV("i",{className:`fas fa-${l?"eye-slash":"eye"}`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:198,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:193,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:181,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:176,columnNumber:17},globalThis),e.jsxDEV("div",{className:"form-options",children:[e.jsxDEV("label",{className:"checkbox-label",children:[e.jsxDEV("input",{type:"checkbox",name:"remember",checked:r.remember,onChange:C},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:205,columnNumber:21},globalThis),e.jsxDEV("span",{className:"checkbox-custom"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:211,columnNumber:21},globalThis),e.jsxDEV("span",{className:"checkbox-text",children:"로그인 상태 유지"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:212,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:204,columnNumber:19},globalThis),e.jsxDEV(L,{to:"/forgot-password",className:"auth-link",children:"비밀번호를 잊으셨나요?"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:214,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:203,columnNumber:17},globalThis),e.jsxDEV("input",{type:"hidden",name:"csrf_token",value:r.csrf_token},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:220,columnNumber:17},globalThis),e.jsxDEV("input",{type:"hidden",name:"redirect",value:r.redirect},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:221,columnNumber:17},globalThis),e.jsxDEV("button",{type:"submit",className:"btn btn-primary-gradient btn-large btn-full",disabled:n,children:[e.jsxDEV("i",{className:"fas fa-sign-in-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:228,columnNumber:19},globalThis),e.jsxDEV("span",{children:n?"로그인 중...":"로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:229,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:223,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:153,columnNumber:15},globalThis),e.jsxDEV("div",{className:"auth-footer",children:e.jsxDEV("p",{className:"auth-switch",children:["아직 계정이 없으신가요?",e.jsxDEV(L,{to:"/auth/signup",className:"auth-link ml-1",children:["회원가입하기",e.jsxDEV("i",{className:"fas fa-arrow-right ml-1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:239,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:237,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:235,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:234,columnNumber:15},globalThis),e.jsxDEV("div",{className:"dev-notice",children:[e.jsxDEV("h4",{children:"🔧 개발자 테스트 계정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:247,columnNumber:19},globalThis),e.jsxDEV("p",{children:[e.jsxDEV("strong",{children:"휴대폰:"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:248,columnNumber:22},globalThis)," 010-0000-0000"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:248,columnNumber:19},globalThis),e.jsxDEV("p",{children:[e.jsxDEV("strong",{children:"비밀번호:"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:249,columnNumber:22},globalThis)," admin123!"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:249,columnNumber:19},globalThis),e.jsxDEV("button",{type:"button",className:"btn btn-outline-secondary",onClick:v,children:"테스트 계정으로 자동 입력"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:250,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:246,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:139,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-side-info",children:e.jsxDEV("div",{className:"side-info-content",children:[e.jsxDEV("div",{className:"side-info-icon",children:e.jsxDEV("i",{className:"fas fa-lock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:265,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:264,columnNumber:17},globalThis),e.jsxDEV("h2",{children:"안전한 로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:267,columnNumber:17},globalThis),e.jsxDEV("p",{children:"최신 보안 기술로 여러분의 계정을 안전하게 보호합니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:268,columnNumber:17},globalThis),e.jsxDEV("div",{className:"security-features",children:[e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-shield-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:272,columnNumber:21},globalThis),e.jsxDEV("span",{children:"SSL 암호화"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:273,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:271,columnNumber:19},globalThis),e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-user-shield"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:276,columnNumber:21},globalThis),e.jsxDEV("span",{children:"2단계 인증"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:277,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:275,columnNumber:19},globalThis),e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-history"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:280,columnNumber:21},globalThis),e.jsxDEV("span",{children:"로그인 기록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:281,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:279,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:270,columnNumber:17},globalThis),e.jsxDEV("div",{className:"login-benefits",children:[e.jsxDEV("h3",{children:"로그인 후 이용 가능한 서비스"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:286,columnNumber:19},globalThis),e.jsxDEV("ul",{children:[e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-comments"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:288,columnNumber:25},globalThis)," 커뮤니티 참여"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:288,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-bell"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:289,columnNumber:25},globalThis)," 실시간 알림"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:289,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-chart-line"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:290,columnNumber:25},globalThis)," 성과 분석 도구"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:290,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-graduation-cap"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:291,columnNumber:25},globalThis)," 전문가 강의"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:291,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:287,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:285,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:263,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:262,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:137,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:136,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:126,columnNumber:7},globalThis),e.jsxDEV("style",{children:`
        /* 인증 페이지 기본 스타일 */
        .auth-section {
          min-height: 100vh;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          position: relative;
          display: flex;
          align-items: center;
          overflow: hidden;
          padding-top: 120px;
          padding-bottom: 60px;
        }

        .auth-background {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 1;
        }

        .auth-gradient-overlay {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
        }

        .auth-shapes {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          overflow: hidden;
        }

        .auth-shape {
          position: absolute;
          border-radius: 50%;
          background: rgba(255, 255, 255, 0.1);
          animation: authFloat 8s ease-in-out infinite;
        }

        .auth-shape-1 {
          width: 120px;
          height: 120px;
          top: 15%;
          left: 10%;
          animation-delay: 0s;
        }

        .auth-shape-2 {
          width: 180px;
          height: 180px;
          top: 60%;
          right: 15%;
          animation-delay: 3s;
        }

        .auth-shape-3 {
          width: 90px;
          height: 90px;
          top: 30%;
          right: 25%;
          animation-delay: 6s;
        }

        @keyframes authFloat {
          0%, 100% { 
            transform: translateY(0px) rotate(0deg) scale(1); 
            opacity: 0.6;
          }
          33% { 
            transform: translateY(-15px) rotate(120deg) scale(1.1); 
            opacity: 0.8;
          }
          66% { 
            transform: translateY(-5px) rotate(240deg) scale(0.9); 
            opacity: 0.4;
          }
        }

        .auth-content {
          position: relative;
          z-index: 2;
          display: grid;
          grid-template-columns: 1fr 1fr;
          gap: 80px;
          align-items: start;
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 20px;
        }

        .auth-form-container {
          background: rgba(255, 255, 255, 0.95);
          backdrop-filter: blur(20px);
          border-radius: 20px;
          padding: 40px;
          box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
          border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .auth-header {
          text-align: center;
          margin-bottom: 32px;
        }

        .auth-logo {
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 24px;
          gap: 12px;
        }

        .auth-logo .logo-icon {
          width: 48px;
          height: 48px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-radius: 12px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: white;
          font-size: 1.5rem;
        }

        .auth-logo .logo-text {
          font-size: 1.8rem;
          font-weight: 700;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          -webkit-background-clip: text;
          background-clip: text;
          -webkit-text-fill-color: transparent;
        }

        .auth-title {
          font-size: 2rem;
          font-weight: 700;
          color: #1a202c;
          margin-bottom: 12px;
          line-height: 1.2;
        }

        .auth-subtitle {
          font-size: 1rem;
          color: #64748b;
          line-height: 1.5;
        }

        .auth-form {
          margin-top: 32px;
        }

        .form-group {
          margin-bottom: 24px;
        }

        .form-label {
          display: flex;
          align-items: center;
          gap: 8px;
          font-weight: 500;
          color: #374151;
          margin-bottom: 8px;
          font-size: 0.95rem;
        }

        .form-label i {
          width: 16px;
          color: #64748b;
          font-size: 0.9rem;
        }

        .form-input {
          width: 100%;
          padding: 14px 16px;
          border: 2px solid #e2e8f0;
          border-radius: 12px;
          font-size: 1rem;
          transition: all 0.3s ease;
          background: white;
          box-sizing: border-box;
        }

        .form-input:focus {
          outline: none;
          border-color: #667eea;
          box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-input::placeholder {
          color: #94a3b8;
        }

        .form-input.error {
          border-color: #ef4444;
          background: #fef2f2;
        }

        .form-help {
          display: block;
          margin-top: 6px;
          font-size: 0.8rem;
          color: #9ca3af;
        }

        .error-message {
          margin-top: 6px;
          font-size: 0.8rem;
          color: #ef4444;
        }

        .password-input-wrapper {
          position: relative;
        }

        .password-toggle {
          position: absolute;
          right: 16px;
          top: 50%;
          transform: translateY(-50%);
          background: none;
          border: none;
          color: #9ca3af;
          cursor: pointer;
          padding: 4px;
          transition: color 0.3s ease;
        }

        .password-toggle:hover {
          color: #374151;
        }

        .form-options {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          flex-wrap: wrap;
          gap: 10px;
        }

        .checkbox-label {
          display: flex;
          align-items: center;
          gap: 8px;
          cursor: pointer;
          font-size: 0.9rem;
          color: #374151;
        }

        .checkbox-label input[type="checkbox"] {
          display: none;
        }

        .checkbox-custom {
          width: 18px;
          height: 18px;
          border: 2px solid #d1d5db;
          border-radius: 4px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.3s ease;
        }

        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border-color: #667eea;
        }

        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
          content: '✓';
          color: white;
          font-size: 0.75rem;
          font-weight: bold;
        }

        .auth-link {
          color: #667eea;
          text-decoration: none;
          font-weight: 500;
          display: inline-flex;
          align-items: center;
          gap: 4px;
          transition: color 0.3s ease;
        }

        .auth-link:hover {
          color: #5a67d8;
          text-decoration: underline;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 8px;
          padding: 16px 32px;
          border: none;
          border-radius: 12px;
          font-weight: 600;
          font-size: 1rem;
          text-decoration: none;
          transition: all 0.3s ease;
          cursor: pointer;
          min-height: 50px;
        }

        .btn-primary-gradient {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-gradient:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-primary-gradient:disabled {
          opacity: 0.7;
          cursor: not-allowed;
          transform: none;
        }

        .btn-large {
          padding: 18px 36px;
          font-size: 1.1rem;
        }

        .btn-full {
          width: 100%;
        }

        .btn-outline-secondary {
          background: transparent;
          color: #0ea5e9;
          border: 1px solid #0ea5e9;
          padding: 8px 16px;
          font-size: 0.8rem;
        }

        .btn-outline-secondary:hover {
          background: #0ea5e9;
          color: white;
        }

        .auth-footer {
          text-align: center;
          margin-top: 32px;
          padding-top: 24px;
          border-top: 1px solid #e2e8f0;
        }

        .auth-switch {
          color: #64748b;
          font-size: 0.95rem;
          margin: 0;
        }

        .auth-side-info {
          display: flex;
          flex-direction: column;
          justify-content: center;
          color: white;
          padding: 40px;
        }

        .side-info-content {
          max-width: 400px;
        }

        .side-info-icon {
          width: 80px;
          height: 80px;
          background: rgba(255, 255, 255, 0.2);
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          margin-bottom: 30px;
          backdrop-filter: blur(10px);
        }

        .side-info-icon i {
          font-size: 2rem;
          color: white;
        }

        .auth-side-info h2 {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 20px;
          color: white;
        }

        .auth-side-info p {
          font-size: 1.1rem;
          color: rgba(255, 255, 255, 0.9);
          line-height: 1.6;
          margin-bottom: 0;
        }

        .security-features {
          display: flex;
          flex-direction: column;
          gap: 12px;
          margin: 20px 0;
        }

        .security-feature {
          display: flex;
          align-items: center;
          gap: 10px;
          color: rgba(255, 255, 255, 0.8);
          font-size: 14px;
        }

        .security-feature i {
          color: #10b981;
          width: 20px;
        }

        .login-benefits {
          margin-top: 30px;
        }

        .login-benefits h3 {
          font-size: 16px;
          margin-bottom: 15px;
          color: white;
        }

        .login-benefits ul {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .login-benefits li {
          display: flex;
          align-items: center;
          gap: 10px;
          margin-bottom: 8px;
          color: rgba(255, 255, 255, 0.8);
          font-size: 14px;
        }

        .login-benefits li i {
          color: #fbbf24;
          width: 16px;
        }

        .dev-notice {
          margin-top: 20px;
          padding: 15px;
          background: #f0f9ff;
          border: 1px solid #0ea5e9;
          border-radius: 6px;
          font-size: 13px;
        }

        .dev-notice h4 {
          margin: 0 0 10px 0;
          color: #0369a1;
        }

        .dev-notice p {
          margin: 5px 0;
          color: #0369a1;
        }

        /* 알림 메시지 스타일 */
        .alert {
          padding: 12px 16px;
          border-radius: 6px;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          gap: 10px;
          font-size: 14px;
          line-height: 1.4;
        }

        .alert-error {
          background-color: #fee;
          border: 1px solid #fcc;
          color: #c33;
        }

        .alert-success {
          background-color: #efe;
          border: 1px solid #cfc;
          color: #363;
        }

        .alert i {
          font-size: 16px;
        }

        /* 반응형 */
        @media (max-width: 1024px) {
          .auth-section {
            padding-top: 100px;
            min-height: auto;
          }

          .auth-content {
            grid-template-columns: 1fr;
            gap: 40px;
            padding: 20px;
          }

          .auth-side-info {
            order: -1;
            text-align: center;
            padding: 20px;
          }

          .auth-side-info h2 {
            font-size: 1.8rem;
          }
        }

        @media (max-width: 768px) {
          .auth-section {
            padding-top: 80px;
          }

          .auth-form-container {
            padding: 30px 20px;
          }

          .auth-title {
            font-size: 1.5rem;
          }

          .auth-side-info h2 {
            font-size: 1.6rem;
          }

          .form-options {
            flex-direction: column;
            align-items: flex-start;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:301,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/LoginPage.tsx",lineNumber:122,columnNumber:5},globalThis)},mo=()=>{const[r,s]=d.useState({nickname:"",phone:"",email:"",password:"",password_confirm:"",verification_code:"",terms:!1,marketing:!1,phone_verified:"0",csrf_token:"",recaptcha_token:""}),[o,a]=d.useState({}),[n,i]=d.useState(!1),[l,c]=d.useState(!1),[u,f]=d.useState(180),[g,p]=d.useState(!1),[b,w]=d.useState(!1),[k,h]=d.useState(!1),[C,_]=d.useState(!1),{isAuthenticated:v}=le(),{success:N,error:m,info:E}=fe(),D=Ne(),z=Yr({title:"회원가입",description:"탑마케팅에 가입하여 글로벌 네트워크 마케팅 커뮤니티에 참여하세요",ogType:"website"});d.useEffect(()=>{v&&D("/",{replace:!0})},[v,D]),d.useEffect(()=>{let V;return l&&u>0?V=setInterval(()=>{f(O=>O-1)},1e3):u===0&&c(!1),()=>clearInterval(V)},[l,u]);const T=d.useCallback(()=>{const V=r.nickname.length>=2&&r.nickname.length<=20,O=/^010-[0-9]{3,4}-[0-9]{4}$/.test(r.phone),K=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(r.email),xe=r.password.length>=8,je=r.password===r.password_confirm,ge=r.terms,te=V&&O&&K&&xe&&je&&ge&&C;return h(te),te},[r,C]);d.useEffect(()=>{T()},[T]);const P=V=>V.trim()?V.length<2?"닉네임은 2자 이상 입력해주세요.":V.length>20?"닉네임은 20자 이하로 입력해주세요.":"":"닉네임을 입력해주세요.",H=V=>{let O=V.replace(/[^0-9]/g,"");return O.length>0&&!O.startsWith("010")?(a(K=>({...K,phone:"010으로 시작하는 휴대폰 번호만 입력할 수 있습니다."})),r.phone):(O.length>=3&&(O=O.substring(0,3)+"-"+O.substring(3)),O.length>=8&&(O=O.substring(0,8)+"-"+O.substring(8,12)),O)},q=V=>V.trim()?/^010-[0-9]{3,4}-[0-9]{4}$/.test(V)?"":"010으로 시작하는 올바른 휴대폰 번호를 입력해주세요.":"휴대폰 번호를 입력해주세요.",R=V=>V.trim()?/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(V)?"":"올바른 이메일 형식을 입력해주세요.":"이메일을 입력해주세요.",W=V=>{if(!V.trim())return"비밀번호를 입력해주세요.";if(V.length<8)return"비밀번호는 8자 이상 입력해주세요.";const O=/[a-zA-Z]/.test(V),K=/[0-9]/.test(V),xe=/[!@#$%^&*(),.?":{}|<>]/.test(V);return!O||!K||!xe?"영문, 숫자, 특수문자를 포함하여 8자 이상 입력해주세요.":""},j=V=>V.trim()?r.password!==V?"비밀번호가 일치하지 않습니다.":"":"비밀번호 확인을 입력해주세요.",M=async V=>{try{if(window.grecaptcha)return await window.grecaptcha.execute("6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb",{action:V})}catch(O){console.error("reCAPTCHA error:",O)}return""},B=V=>{const{name:O,value:K,type:xe,checked:je}=V.target;let ge=xe==="checkbox"?je:K;O==="phone"&&(ge=H(K),C&&(_(!1),s(oe=>({...oe,phone_verified:"0"})),a(oe=>({...oe,phone:""})))),s(oe=>({...oe,[O]:ge}));let te="";switch(O){case"nickname":te=P(K);break;case"phone":te=q(ge);break;case"email":te=R(K);break;case"password":if(te=W(K),r.password_confirm){const oe=j(r.password_confirm);a(Ve=>({...Ve,password_confirm:oe}))}break;case"password_confirm":te=j(K);break}a(oe=>({...oe,[O]:te}))},J=async()=>{const V=q(r.phone);if(V){a(O=>({...O,phone:V}));return}i(!0);try{const O=await M("send_verification");console.log("reCAPTCHA token:",O),c(!0),f(180),N("인증번호가 발송되었습니다.","확인해주세요"),E("인증번호는 3분간 유효합니다.")}catch{m("인증번호 발송에 실패했습니다.","다시 시도해주세요")}finally{i(!1)}},S=async()=>{if(!r.verification_code||r.verification_code.length!==4){a(V=>({...V,verification_code:"4자리 인증번호를 입력해주세요."}));return}i(!0);try{_(!0),s(V=>({...V,phone_verified:"1"})),c(!1),N("휴대폰 인증이 완료되었습니다.","")}catch{m("인증번호가 올바르지 않습니다.","다시 확인해주세요")}finally{i(!1)}},U=async V=>{if(V.preventDefault(),!k){m("모든 필수 항목을 올바르게 입력해주세요.","");return}i(!0);try{const O=await M("signup"),K={...r,recaptcha_token:O};console.log("Submit data:",K),N("회원가입이 완료되었습니다!","환영합니다"),D("/login")}catch{m("회원가입에 실패했습니다.","다시 시도해주세요")}finally{i(!1)}},X=V=>{const O=Math.floor(V/60),K=V%60;return`${O.toString().padStart(2,"0")}:${K.toString().padStart(2,"0")}`};return e.jsxDEV(e.Fragment,{children:[e.jsxDEV(qr,{...z},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:334,columnNumber:7},globalThis),e.jsxDEV("script",{src:"https://www.google.com/recaptcha/api.js?render=6LfViDErAAAAAMcOf3D-JxEhisMDhzLhEDYEahZb"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:337,columnNumber:7},globalThis),e.jsxDEV("section",{className:"auth-section",children:[e.jsxDEV("div",{className:"auth-background",children:[e.jsxDEV("div",{className:"auth-gradient-overlay"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:342,columnNumber:11},globalThis),e.jsxDEV("div",{className:"auth-shapes",children:[e.jsxDEV("div",{className:"auth-shape auth-shape-1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:344,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-shape auth-shape-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:345,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-shape auth-shape-3"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:346,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:343,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:341,columnNumber:9},globalThis),e.jsxDEV("div",{className:"container",children:e.jsxDEV("div",{className:"auth-content",children:[e.jsxDEV("div",{className:"auth-form-container",children:[e.jsxDEV("div",{className:"auth-header",children:[e.jsxDEV("div",{className:"auth-logo",children:[e.jsxDEV("div",{className:"logo-icon",children:e.jsxDEV("i",{className:"fas fa-rocket"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:358,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:357,columnNumber:19},globalThis),e.jsxDEV("span",{className:"logo-text",children:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:360,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:356,columnNumber:17},globalThis),e.jsxDEV("h1",{className:"auth-title",children:"새로운 여정을 시작하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:362,columnNumber:17},globalThis),e.jsxDEV("p",{className:"auth-subtitle",children:"글로벌 네트워크 마케팅 커뮤니티에 가입하여 성공을 함께 만들어가세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:363,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:355,columnNumber:15},globalThis),e.jsxDEV("form",{className:"auth-form",onSubmit:U,id:"signup-form",children:[e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"nickname",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-user"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:371,columnNumber:21},globalThis),"닉네임 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:372,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:370,columnNumber:19},globalThis),e.jsxDEV("input",{type:"text",id:"nickname",name:"nickname",className:`form-input ${o.nickname?"error":""}`,placeholder:"닉네임을 입력하세요 (2-20자)",value:r.nickname,onChange:B,required:!0,autoComplete:"username",maxLength:20,minLength:2},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:374,columnNumber:19},globalThis),o.nickname&&e.jsxDEV("div",{className:"error-message",children:o.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:387,columnNumber:39},globalThis),e.jsxDEV("small",{className:"form-help",children:"한글, 영문, 숫자를 사용하여 2-20자로 입력하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:388,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:369,columnNumber:17},globalThis),e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"phone",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-mobile-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:394,columnNumber:21},globalThis),"휴대폰 번호 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:395,columnNumber:28},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:393,columnNumber:19},globalThis),e.jsxDEV("div",{className:"phone-verification-group",children:[e.jsxDEV("input",{type:"tel",id:"phone",name:"phone",className:`form-input phone-input ${o.phone?"error":""}`,placeholder:"010-1234-5678",value:r.phone,onChange:B,required:!0,autoComplete:"tel",pattern:"010-[0-9]{3,4}-[0-9]{4}",maxLength:13},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:398,columnNumber:21},globalThis),e.jsxDEV("button",{type:"button",id:"send-verification-btn",className:"btn btn-outline-primary",onClick:J,disabled:!r.phone||!/^010-[0-9]{3,4}-[0-9]{4}$/.test(r.phone)||l,children:l?"발송됨":"인증번호 발송"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:411,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:397,columnNumber:19},globalThis),o.phone&&e.jsxDEV("div",{className:"error-message",children:o.phone},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:421,columnNumber:36},globalThis),e.jsxDEV("small",{className:"form-help",children:"010으로 시작하는 휴대폰 번호를 입력하세요 (로그인 시 사용됩니다)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:422,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:392,columnNumber:17},globalThis),l&&e.jsxDEV("div",{className:"form-group",id:"verification-group",children:[e.jsxDEV("label",{htmlFor:"verification_code",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-shield-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:429,columnNumber:23},globalThis),"인증번호 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:430,columnNumber:28},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:428,columnNumber:21},globalThis),e.jsxDEV("div",{className:"verification-input-group",children:[e.jsxDEV("input",{type:"text",id:"verification_code",name:"verification_code",className:`form-input verification-input ${o.verification_code?"error":""}`,placeholder:"4자리 인증번호 입력",value:r.verification_code,onChange:B,required:!0,maxLength:4},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:433,columnNumber:23},globalThis),e.jsxDEV("button",{type:"button",className:"btn btn-primary",onClick:S,disabled:!r.verification_code||r.verification_code.length!==4,children:"확인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:444,columnNumber:23},globalThis),e.jsxDEV("div",{className:`timer ${u<=30?"text-red-500":"text-gray-600"}`,children:X(u)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:452,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:432,columnNumber:21},globalThis),o.verification_code&&e.jsxDEV("div",{className:"error-message",children:o.verification_code},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:456,columnNumber:50},globalThis),e.jsxDEV("small",{className:"form-help",children:"휴대폰으로 전송된 4자리 인증번호를 입력하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:457,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:427,columnNumber:19},globalThis),C&&e.jsxDEV("div",{className:"text-green-600 text-sm flex items-center",children:[e.jsxDEV("i",{className:"fas fa-check mr-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:463,columnNumber:21},globalThis),"휴대폰 인증이 완료되었습니다."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:462,columnNumber:19},globalThis),e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"email",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-envelope"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:471,columnNumber:21},globalThis),"이메일 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:472,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:470,columnNumber:19},globalThis),e.jsxDEV("input",{type:"email",id:"email",name:"email",className:`form-input ${o.email?"error":""}`,placeholder:"example@email.com",value:r.email,onChange:B,required:!0,autoComplete:"email"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:474,columnNumber:19},globalThis),o.email&&e.jsxDEV("div",{className:"error-message",children:o.email},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:485,columnNumber:36},globalThis),e.jsxDEV("small",{className:"form-help",children:"계정 복구 및 중요한 알림을 받기 위해 사용됩니다 (필수)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:486,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:469,columnNumber:17},globalThis),e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"password",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-lock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:492,columnNumber:21},globalThis),"비밀번호 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:493,columnNumber:26},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:491,columnNumber:19},globalThis),e.jsxDEV("div",{className:"password-input-wrapper relative",children:[e.jsxDEV("input",{type:g?"text":"password",id:"password",name:"password",className:`form-input ${o.password?"error":""}`,placeholder:"비밀번호를 입력하세요 (8자 이상)",value:r.password,onChange:B,required:!0,autoComplete:"new-password",minLength:8},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:496,columnNumber:21},globalThis),e.jsxDEV("button",{type:"button",className:"password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600",onClick:()=>p(!g),children:e.jsxDEV("i",{className:`fas fa-${g?"eye-slash":"eye"}`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:513,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:508,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:495,columnNumber:19},globalThis),o.password&&e.jsxDEV("div",{className:"error-message",children:o.password},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:516,columnNumber:39},globalThis),e.jsxDEV("small",{className:"form-help",children:"영문, 숫자, 특수문자를 포함하여 8자 이상 입력하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:517,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:490,columnNumber:17},globalThis),e.jsxDEV("div",{className:"form-group",children:[e.jsxDEV("label",{htmlFor:"password_confirm",className:"form-label",children:[e.jsxDEV("i",{className:"fas fa-lock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:523,columnNumber:21},globalThis),"비밀번호 확인 ",e.jsxDEV("span",{className:"required",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:524,columnNumber:29},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:522,columnNumber:19},globalThis),e.jsxDEV("div",{className:"password-input-wrapper relative",children:[e.jsxDEV("input",{type:b?"text":"password",id:"password_confirm",name:"password_confirm",className:`form-input ${o.password_confirm?"error":""}`,placeholder:"비밀번호를 다시 입력하세요",value:r.password_confirm,onChange:B,required:!0,autoComplete:"new-password",minLength:8},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:527,columnNumber:21},globalThis),e.jsxDEV("button",{type:"button",className:"password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600",onClick:()=>w(!b),children:e.jsxDEV("i",{className:`fas fa-${b?"eye-slash":"eye"}`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:544,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:539,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:526,columnNumber:19},globalThis),o.password_confirm&&e.jsxDEV("div",{className:"error-message",children:o.password_confirm},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:547,columnNumber:47},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:521,columnNumber:17},globalThis),e.jsxDEV("div",{className:"terms-group",children:[e.jsxDEV("div",{className:"checkbox-item",children:[e.jsxDEV("input",{id:"terms",name:"terms",type:"checkbox",checked:r.terms,onChange:B,required:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:553,columnNumber:21},globalThis),e.jsxDEV("label",{htmlFor:"terms",className:"ml-2 text-sm text-gray-700",children:[e.jsxDEV(L,{to:"/terms",target:"_blank",className:"text-blue-600 hover:text-blue-500",children:"이용약관"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:562,columnNumber:23},globalThis)," ","및"," ",e.jsxDEV(L,{to:"/privacy",target:"_blank",className:"text-blue-600 hover:text-blue-500",children:"개인정보처리방침"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:566,columnNumber:23},globalThis),"에 동의합니다 ",e.jsxDEV("span",{className:"text-red-500",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:569,columnNumber:31},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:561,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:552,columnNumber:19},globalThis),e.jsxDEV("div",{className:"checkbox-item",children:[e.jsxDEV("input",{id:"marketing",name:"marketing",type:"checkbox",checked:r.marketing,onChange:B},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:574,columnNumber:21},globalThis),e.jsxDEV("label",{htmlFor:"marketing",className:"ml-2 text-sm text-gray-700",children:"마케팅 정보 수신에 동의합니다 (선택)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:581,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:573,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:551,columnNumber:17},globalThis),e.jsxDEV("input",{type:"hidden",name:"phone_verified",value:C?"1":"0"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:588,columnNumber:17},globalThis),e.jsxDEV("input",{type:"hidden",name:"csrf_token",value:r.csrf_token},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:589,columnNumber:17},globalThis),e.jsxDEV("input",{type:"hidden",name:"recaptcha_token",value:r.recaptcha_token},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:590,columnNumber:17},globalThis),e.jsxDEV("button",{type:"submit",className:"btn btn-primary-gradient btn-large btn-full",disabled:!k||n,children:[e.jsxDEV("i",{className:"fas fa-user-plus"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:597,columnNumber:19},globalThis),e.jsxDEV("span",{children:n?"가입 중...":"회원가입"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:598,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:592,columnNumber:17},globalThis),e.jsxDEV("div",{className:"text-xs text-gray-500 text-center bg-blue-50 p-3 rounded-lg",children:[e.jsxDEV("i",{className:"fas fa-shield-alt mr-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:602,columnNumber:19},globalThis),"이 사이트는 reCAPTCHA로 보호되며, Google의"," ",e.jsxDEV("a",{href:"https://policies.google.com/privacy",target:"_blank",rel:"noopener noreferrer",className:"text-blue-600 hover:text-blue-500",children:"개인정보처리방침"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:604,columnNumber:19},globalThis),"과"," ",e.jsxDEV("a",{href:"https://policies.google.com/terms",target:"_blank",rel:"noopener noreferrer",className:"text-blue-600 hover:text-blue-500",children:"서비스 약관"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:608,columnNumber:19},globalThis),"이 적용됩니다."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:601,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:367,columnNumber:15},globalThis),e.jsxDEV("div",{className:"auth-footer",children:e.jsxDEV("p",{className:"auth-switch",children:["이미 계정이 있으신가요?"," ",e.jsxDEV(L,{to:"/auth/login",className:"auth-link",children:"로그인하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:618,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:616,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:615,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:353,columnNumber:13},globalThis),e.jsxDEV("div",{className:"auth-side-info",children:e.jsxDEV("div",{className:"side-info-content",children:[e.jsxDEV("div",{className:"side-info-icon",children:e.jsxDEV("i",{className:"fas fa-rocket"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:632,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:631,columnNumber:17},globalThis),e.jsxDEV("h2",{children:"커뮤니티 가입의 혜택"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:634,columnNumber:17},globalThis),e.jsxDEV("p",{children:"글로벌 네트워크 마케팅 전문가들과 함께 성장하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:635,columnNumber:17},globalThis),e.jsxDEV("div",{className:"security-features",children:[e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-users"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:639,columnNumber:21},globalThis),e.jsxDEV("span",{children:"10,000+ 글로벌 멤버"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:640,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:638,columnNumber:19},globalThis),e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-clock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:643,columnNumber:21},globalThis),e.jsxDEV("span",{children:"24/7 언제든지 소통"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:644,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:642,columnNumber:19},globalThis),e.jsxDEV("div",{className:"security-feature",children:[e.jsxDEV("i",{className:"fas fa-graduation-cap"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:647,columnNumber:21},globalThis),e.jsxDEV("span",{children:"100+ 전문 콘텐츠"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:648,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:646,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:637,columnNumber:17},globalThis),e.jsxDEV("div",{className:"login-benefits",children:[e.jsxDEV("h3",{children:"가입 후 이용 가능한 서비스"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:653,columnNumber:19},globalThis),e.jsxDEV("ul",{children:[e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-comments"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:655,columnNumber:25},globalThis)," 커뮤니티 참여"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:655,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-bell"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:656,columnNumber:25},globalThis)," 실시간 알림"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:656,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-chart-line"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:657,columnNumber:25},globalThis)," 성과 분석 도구"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:657,columnNumber:21},globalThis),e.jsxDEV("li",{children:[e.jsxDEV("i",{className:"fas fa-graduation-cap"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:658,columnNumber:25},globalThis)," 전문가 강의"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:658,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:654,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:652,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:630,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:629,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:351,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:350,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:340,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/SignupPage.tsx",lineNumber:333,columnNumber:5},globalThis)},ae=d.forwardRef(({className:r,type:s="text",label:o,error:a,hint:n,leftIcon:i,rightIcon:l,fullWidth:c=!1,disabled:u,...f},g)=>{const p=["block w-full rounded-lg border transition-colors duration-200","text-gray-900 placeholder-gray-500","focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent","disabled:bg-gray-100 disabled:cursor-not-allowed"],b=i||l?"px-10 py-3":"px-4 py-3",w=a?"border-red-300 focus:ring-red-500":"border-gray-300 hover:border-gray-400",k=c?"w-full":"";return e.jsxDEV("div",{className:we("flex flex-col",k),children:[o&&e.jsxDEV("label",{className:"block text-sm font-medium text-gray-700 mb-2",children:[o,f.required&&e.jsxDEV("span",{className:"text-red-500 ml-1",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:49,columnNumber:32},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:47,columnNumber:11},globalThis),e.jsxDEV("div",{className:"relative",children:[i&&e.jsxDEV("div",{className:"absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none",children:e.jsxDEV("span",{className:"text-gray-400",children:i},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:56,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:55,columnNumber:13},globalThis),e.jsxDEV("input",{type:s,className:we(p.join(" "),b,w,r),disabled:u,ref:g,...f},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:62,columnNumber:11},globalThis),l&&e.jsxDEV("div",{className:"absolute inset-y-0 right-0 pr-3 flex items-center",children:e.jsxDEV("span",{className:"text-gray-400",children:l},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:77,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:76,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:53,columnNumber:9},globalThis),a&&e.jsxDEV("p",{className:"mt-1 text-sm text-red-600",children:a},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:85,columnNumber:11},globalThis),n&&!a&&e.jsxDEV("p",{className:"mt-1 text-sm text-gray-500",children:n},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:89,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/components/common/Input.tsx",lineNumber:45,columnNumber:7},globalThis)});ae.displayName="Input";const uo=()=>{const[r,s]=d.useState(1),[o,a]=d.useState({phone:"",verification_code:"",password:"",password_confirmation:""}),[n,i]=d.useState({}),[l,c]=d.useState(!1),[u,f]=d.useState(0),{success:g,error:p,info:b}=fe(),w=Ne();ue.useEffect(()=>{if(u>0){const P=setTimeout(()=>f(u-1),1e3);return()=>clearTimeout(P)}},[u]);const k=()=>{const P={};return o.phone.trim()?/^010-\d{4}-\d{4}$/.test(o.phone)||(P.phone="올바른 휴대폰 번호를 입력해주세요. (예: 010-1234-5678)"):P.phone="휴대폰 번호를 입력해주세요.",i(P),Object.keys(P).length===0},h=()=>{const P={};return o.verification_code.trim()?o.verification_code.length!==6&&(P.verification_code="인증번호는 6자리입니다."):P.verification_code="인증번호를 입력해주세요.",i(P),Object.keys(P).length===0},C=()=>{const P={};return o.password.trim()?o.password.length<8?P.password="비밀번호는 8자 이상 입력해주세요.":/(?=.*[a-zA-Z])(?=.*\d)/.test(o.password)||(P.password="비밀번호는 영문과 숫자를 포함해야 합니다."):P.password="새 비밀번호를 입력해주세요.",o.password_confirmation.trim()?o.password!==o.password_confirmation&&(P.password_confirmation="비밀번호가 일치하지 않습니다."):P.password_confirmation="비밀번호 확인을 입력해주세요.",i(P),Object.keys(P).length===0},_=P=>{const{name:H,value:q}=P.target;a(R=>({...R,[H]:q})),n[H]&&i(R=>({...R,[H]:""}))},v=P=>{const H=P.replace(/[^\d]/g,"");return H.length<=3?H:H.length<=7?`${H.slice(0,3)}-${H.slice(3)}`:`${H.slice(0,3)}-${H.slice(3,7)}-${H.slice(7,11)}`},N=P=>{const H=v(P.target.value);a(q=>({...q,phone:H})),n.phone&&i(q=>({...q,phone:""}))},m=async()=>{if(k()){c(!0);try{await de.sendVerificationCode(o.phone,"PASSWORD_RESET"),f(180),s(2),g("인증번호가 발송되었습니다.","확인 요청"),b("3분 이내에 인증번호를 입력해주세요.")}catch(P){const H=P instanceof Error?P.message:"인증번호 발송에 실패했습니다.";p(H)}finally{c(!1)}}},E=async()=>{if(h()){c(!0);try{await de.verifyCode(o.phone,o.verification_code,"PASSWORD_RESET"),g("휴대폰 인증이 완료되었습니다."),s(3)}catch(P){const H=P instanceof Error?P.message:"인증번호가 올바르지 않습니다.";p(H)}finally{c(!1)}}},D=async()=>{if(C()){c(!0);try{await de.resetPassword({phone:o.phone,code:o.verification_code,password:o.password,password_confirmation:o.password_confirmation}),g("비밀번호가 성공적으로 변경되었습니다!","완료"),w("/auth/login",{replace:!0})}catch(P){const H=P instanceof Error?P.message:"비밀번호 변경에 실패했습니다.";p(H)}finally{c(!1)}}},z=()=>{r>1&&s(r-1)},T=P=>{const H=Math.floor(P/60),q=P%60;return`${H}:${q.toString().padStart(2,"0")}`};return e.jsxDEV("div",{className:"min-h-screen flex",children:[e.jsxDEV("div",{className:"hidden lg:flex lg:flex-1 bg-gradient-to-br from-indigo-900 via-purple-800 to-pink-800 relative overflow-hidden",children:[e.jsxDEV("div",{className:"absolute inset-0 bg-black opacity-20"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:181,columnNumber:9},globalThis),e.jsxDEV("div",{className:"relative flex flex-col justify-center px-12 text-white",children:e.jsxDEV("div",{className:"max-w-md",children:[e.jsxDEV("h1",{className:"text-4xl font-bold mb-6",children:"비밀번호를 잊으셨나요?"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:184,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-xl text-indigo-100 mb-8 leading-relaxed",children:["걱정하지 마세요! 휴대폰 인증을 통해",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:189,columnNumber:15},globalThis),"새로운 비밀번호를 설정하실 수 있습니다."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:187,columnNumber:13},globalThis),e.jsxDEV("div",{className:"space-y-4",children:[e.jsxDEV("div",{className:`flex items-center ${r>=1?"text-white":"text-indigo-300"}`,children:[e.jsxDEV("div",{className:`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${r>=1?"bg-indigo-500":"bg-indigo-700"}`,children:r>1?"✓":"1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:196,columnNumber:17},globalThis),e.jsxDEV("span",{children:"휴대폰 번호 입력"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:201,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:195,columnNumber:15},globalThis),e.jsxDEV("div",{className:`flex items-center ${r>=2?"text-white":"text-indigo-300"}`,children:[e.jsxDEV("div",{className:`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${r>=2?"bg-indigo-500":"bg-indigo-700"}`,children:r>2?"✓":"2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:204,columnNumber:17},globalThis),e.jsxDEV("span",{children:"인증번호 확인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:209,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:203,columnNumber:15},globalThis),e.jsxDEV("div",{className:`flex items-center ${r>=3?"text-white":"text-indigo-300"}`,children:[e.jsxDEV("div",{className:`w-8 h-8 rounded-full flex items-center justify-center mr-4 ${r>=3?"bg-indigo-500":"bg-indigo-700"}`,children:"3"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:212,columnNumber:17},globalThis),e.jsxDEV("span",{children:"새 비밀번호 설정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:217,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:211,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:194,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:183,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:182,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:180,columnNumber:7},globalThis),e.jsxDEV("div",{className:"flex-1 flex flex-col justify-center px-6 sm:px-12 lg:px-16 bg-gray-50",children:e.jsxDEV("div",{className:"mx-auto w-full max-w-md",children:[e.jsxDEV("div",{className:"text-center mb-8",children:[e.jsxDEV(L,{to:"/",className:"inline-block mb-6",children:[e.jsxDEV("img",{className:"h-12 w-auto mx-auto",src:"/assets/images/topmkt-logo-og.svg",alt:"탑마케팅",onError:P=>{var H;P.currentTarget.style.display="none",(H=P.currentTarget.nextElementSibling)==null||H.classList.remove("hidden")}},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:229,columnNumber:15},globalThis),e.jsxDEV("span",{className:"hidden text-2xl font-bold text-indigo-600",children:"탑마케팅"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:238,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:228,columnNumber:13},globalThis),e.jsxDEV("h2",{className:"text-3xl font-bold text-gray-900 mb-2",children:"비밀번호 재설정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:242,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-gray-600",children:[r===1&&"등록된 휴대폰 번호를 입력해주세요.",r===2&&"SMS로 받은 인증번호를 입력해주세요.",r===3&&"새로운 비밀번호를 설정해주세요."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:245,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:227,columnNumber:11},globalThis),r===1&&e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV(ae,{label:"휴대폰 번호",type:"tel",name:"phone",value:o.phone,onChange:N,error:n.phone,placeholder:"010-1234-5678",required:!0,fullWidth:!0,leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:267,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:266,columnNumber:19},globalThis),hint:"회원가입 시 등록한 휴대폰 번호를 입력해주세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:255,columnNumber:15},globalThis),e.jsxDEV(A,{onClick:m,loading:l,fullWidth:!0,size:"lg",className:"bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700",children:"인증번호 발송"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:273,columnNumber:15},globalThis),e.jsxDEV("div",{className:"text-center",children:e.jsxDEV(L,{to:"/auth/login",className:"text-sm text-indigo-600 hover:text-indigo-500 font-medium",children:"로그인 페이지로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:284,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:283,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:254,columnNumber:13},globalThis),r===2&&e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV("div",{className:"text-center p-6 bg-indigo-50 rounded-lg",children:[e.jsxDEV("svg",{className:"w-12 h-12 text-indigo-600 mx-auto mb-4",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:299,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:298,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-indigo-800 font-medium",children:[o.phone,"로 인증번호를 발송했습니다."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:301,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-indigo-600 text-sm mt-1",children:"SMS로 받은 6자리 인증번호를 입력해주세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:304,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:297,columnNumber:15},globalThis),e.jsxDEV(ae,{label:"인증번호",name:"verification_code",value:o.verification_code,onChange:_,error:n.verification_code,placeholder:"6자리 인증번호",maxLength:6,required:!0,fullWidth:!0,leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:321,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:320,columnNumber:19},globalThis),rightIcon:u>0&&e.jsxDEV("span",{className:"text-red-500 font-medium",children:T(u)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:326,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:309,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex space-x-3",children:[e.jsxDEV(A,{onClick:E,loading:l,fullWidth:!0,size:"lg",className:"bg-gradient-to-r from-green-600 to-indigo-600 hover:from-green-700 hover:to-indigo-700",children:"인증 확인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:334,columnNumber:17},globalThis),e.jsxDEV(A,{onClick:m,disabled:u>0||l,variant:"outline",size:"lg",children:"재발송"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:343,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:333,columnNumber:15},globalThis),e.jsxDEV(A,{onClick:z,variant:"ghost",fullWidth:!0,children:"이전 단계"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:353,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:296,columnNumber:13},globalThis),r===3&&e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV(ae,{label:"새 비밀번호",type:"password",name:"password",value:o.password,onChange:_,error:n.password,placeholder:"8자 이상의 새 비밀번호",required:!0,fullWidth:!0,leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:378,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:377,columnNumber:19},globalThis),hint:"영문, 숫자를 포함하여 8자 이상 입력해주세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:366,columnNumber:15},globalThis),e.jsxDEV(ae,{label:"새 비밀번호 확인",type:"password",name:"password_confirmation",value:o.password_confirmation,onChange:_,error:n.password_confirmation,placeholder:"비밀번호를 다시 입력하세요",required:!0,fullWidth:!0,leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:396,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:395,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:384,columnNumber:15},globalThis),e.jsxDEV(A,{onClick:D,loading:l,fullWidth:!0,size:"lg",className:"bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700",children:"비밀번호 변경 완료"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:401,columnNumber:15},globalThis),e.jsxDEV(A,{onClick:z,variant:"ghost",fullWidth:!0,type:"button",children:"이전 단계"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:411,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:365,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:226,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:225,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/auth/ForgotPasswordPage.tsx",lineNumber:178,columnNumber:5},globalThis)};function he(r,s={}){const{showLoading:o=!0,showError:a=!0,showSuccess:n=!1,successMessage:i,loadingMessage:l}=s,[c,u]=d.useState({data:null,loading:!1,error:null}),{addToast:f}=fe(),{setLoading:g}=Br(),p=d.useCallback(async(...w)=>{try{u(h=>({...h,loading:!0,error:null})),o&&g(!0,l);const k=await r(...w);return u(h=>({...h,data:k,loading:!1})),n&&i&&f({type:"success",message:i}),k}catch(k){const h=k instanceof Error?k.message:"알 수 없는 오류가 발생했습니다.";return u(C=>({...C,error:h,loading:!1})),a&&f({type:"error",message:h}),null}finally{o&&g(!1)}},[r,f,g,o,a,n,i,l]),b=d.useCallback(()=>{u({data:null,loading:!1,error:null})},[]);return{...c,execute:p,reset:b}}const fo=()=>{const[r,s]=Cr(),[o,a]=d.useState([]),[n,i]=d.useState(!0),[l,c]=d.useState(r.get("search")||""),[u,f]=d.useState(r.get("category")||""),[g,p]=d.useState({min:r.get("min_price")||"",max:r.get("max_price")||""}),[b,w]=d.useState(r.get("sort")||"latest"),{execute:k}=he(async m=>m),h=async(m={})=>{i(!0);try{const E={query:l,category:u,min_price:g.min,max_price:g.max,sort_by:b==="latest"?"created_at":b,sort_direction:b==="latest"||b==="popular"?"desc":"asc",page:1,per_page:12,...m},D=await k({url:"/lectures",method:"GET",params:E});D.success&&D.data&&a(D.data.data)}catch(E){console.error("강의 목록 조회 실패:",E)}finally{i(!1)}};d.useEffect(()=>{h()},[l,u,g,b]);const C=m=>{m.preventDefault();const E=new URLSearchParams;l&&E.set("search",l),u&&E.set("category",u),g.min&&E.set("min_price",g.min),g.max&&E.set("max_price",g.max),b!=="latest"&&E.set("sort",b),s(E)},_=()=>{c(""),f(""),p({min:"",max:""}),w("latest"),s(new URLSearchParams)},v=m=>m===0?"무료":`${m.toLocaleString()}원`,N=[{value:"",label:"전체 카테고리"},{value:"marketing",label:"마케팅 기초"},{value:"sales",label:"영업 전략"},{value:"leadership",label:"리더십"},{value:"communication",label:"커뮤니케이션"},{value:"mindset",label:"마인드셋"},{value:"business",label:"사업 운영"}];return e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:[e.jsxDEV("div",{className:"bg-gradient-to-r from-blue-600 to-purple-600 text-white",children:e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h1",{className:"text-4xl font-bold mb-4",children:"전문가 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:104,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-xl text-blue-100 max-w-2xl mx-auto",children:"네트워크 마케팅 전문가들의 실전 노하우를 학습하고 성공을 향한 여정을 시작하세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:107,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:103,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:102,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:101,columnNumber:7},globalThis),e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-8",children:e.jsxDEV("form",{onSubmit:C,className:"space-y-4",children:[e.jsxDEV("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4",children:[e.jsxDEV(ae,{placeholder:"강의 제목이나 강사명으로 검색",value:l,onChange:m=>c(m.target.value),name:"search",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:127,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:126,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:120,columnNumber:15},globalThis),e.jsxDEV("select",{value:u,onChange:m=>f(m.target.value),className:"px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent",children:N.map(m=>e.jsxDEV("option",{value:m.value,children:m.label},m.value,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:139,columnNumber:19},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:133,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex space-x-2",children:[e.jsxDEV(ae,{placeholder:"최소 가격",type:"number",value:g.min,onChange:m=>p(E=>({...E,min:m.target.value})),name:"min_price"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:147,columnNumber:17},globalThis),e.jsxDEV(ae,{placeholder:"최대 가격",type:"number",value:g.max,onChange:m=>p(E=>({...E,max:m.target.value})),name:"max_price"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:154,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:146,columnNumber:15},globalThis),e.jsxDEV("select",{value:b,onChange:m=>w(m.target.value),className:"px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent",children:[e.jsxDEV("option",{value:"latest",children:"최신순"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:169,columnNumber:17},globalThis),e.jsxDEV("option",{value:"popular",children:"인기순"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:170,columnNumber:17},globalThis),e.jsxDEV("option",{value:"price",children:"가격 낮은 순"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:171,columnNumber:17},globalThis),e.jsxDEV("option",{value:"rating",children:"평점 높은 순"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:172,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:164,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:118,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex justify-between items-center",children:[e.jsxDEV(A,{type:"submit",className:"bg-blue-600 hover:bg-blue-700",children:"검색"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:177,columnNumber:15},globalThis),e.jsxDEV(A,{type:"button",variant:"ghost",onClick:_,children:"필터 초기화"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:180,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:176,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:117,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:116,columnNumber:9},globalThis),n?e.jsxDEV("div",{className:"flex justify-center py-16",children:e.jsxDEV(be,{size:"lg",message:"강의를 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:190,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:189,columnNumber:11},globalThis):e.jsxDEV("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6",children:o.length>0?o.map(m=>e.jsxDEV(L,{to:`/lectures/${m.id}`,className:"group",children:e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden",children:[e.jsxDEV("div",{className:"relative aspect-video bg-gradient-to-br from-blue-500 to-purple-600 overflow-hidden",children:[m.thumbnail?e.jsxDEV("img",{src:m.thumbnail,alt:m.title,className:"w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:205,columnNumber:25},globalThis):e.jsxDEV("div",{className:"flex items-center justify-center h-full",children:e.jsxDEV("svg",{className:"w-16 h-16 text-white opacity-50",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:213,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:212,columnNumber:27},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:211,columnNumber:25},globalThis),e.jsxDEV("div",{className:"absolute top-3 right-3",children:e.jsxDEV("span",{className:`px-2 py-1 text-xs font-semibold rounded-full ${m.price===0?"bg-green-500 text-white":"bg-white text-gray-900"}`,children:v(m.price)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:220,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:219,columnNumber:23},globalThis),m.duration&&e.jsxDEV("div",{className:"absolute bottom-3 right-3",children:e.jsxDEV("span",{className:"px-2 py-1 text-xs font-medium bg-black bg-opacity-70 text-white rounded",children:[Math.floor(m.duration/60),"분"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:232,columnNumber:27},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:231,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:203,columnNumber:21},globalThis),e.jsxDEV("div",{className:"p-5",children:[e.jsxDEV("h3",{className:"font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-blue-600 transition-colors",children:m.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:241,columnNumber:23},globalThis),e.jsxDEV("div",{className:"flex items-center mb-3",children:[e.jsxDEV("div",{className:"w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-2",children:m.instructor.profile_image?e.jsxDEV("img",{src:m.instructor.profile_image,alt:m.instructor.nickname,className:"w-8 h-8 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:249,columnNumber:29},globalThis):e.jsxDEV("svg",{className:"w-4 h-4 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:256,columnNumber:31},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:255,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:247,columnNumber:25},globalThis),e.jsxDEV("span",{className:"text-sm text-gray-600",children:m.instructor.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:260,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:246,columnNumber:23},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-600 mb-4 line-clamp-2",children:m.description},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:266,columnNumber:23},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between text-xs text-gray-500",children:[e.jsxDEV("div",{className:"flex items-center space-x-3",children:[e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 12a3 3 0 11-6 0 3 3 0 016 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:275,columnNumber:31},globalThis),e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:276,columnNumber:31},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:274,columnNumber:29},globalThis),m.views.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:273,columnNumber:27},globalThis),e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:282,columnNumber:31},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:281,columnNumber:29},globalThis),m.likes_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:280,columnNumber:27},globalThis),e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:288,columnNumber:31},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:287,columnNumber:29},globalThis),m.enrollment_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:286,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:272,columnNumber:25},globalThis),m.status==="ACTIVE"?e.jsxDEV("span",{className:"text-green-600 font-medium",children:"수강 가능"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:295,columnNumber:27},globalThis):e.jsxDEV("span",{className:"text-gray-400",children:"수강 불가"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:297,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:271,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:239,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:201,columnNumber:19},globalThis)},m.id,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:196,columnNumber:17},globalThis)):e.jsxDEV("div",{className:"col-span-full text-center py-16",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:308,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:307,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:306,columnNumber:17},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"등록된 강의가 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:311,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-gray-600",children:"검색 조건을 변경하거나 필터를 초기화해보세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:314,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:305,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:193,columnNumber:11},globalThis),!n&&o.length>0&&e.jsxDEV("div",{className:"mt-16",children:[e.jsxDEV("div",{className:"text-center mb-8",children:[e.jsxDEV("h2",{className:"text-3xl font-bold text-gray-900 mb-4",children:"지금 바로 시작할 수 있는 무료 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:326,columnNumber:15},globalThis),e.jsxDEV("p",{className:"text-xl text-gray-600",children:"부담 없이 시작해보세요. 품질 높은 무료 강의들을 만나보세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:329,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:325,columnNumber:13},globalThis),e.jsxDEV("div",{className:"text-center",children:e.jsxDEV(A,{onClick:()=>{f(""),p({min:"0",max:"0"}),w("popular")},size:"lg",className:"bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700",children:"무료 강의 둘러보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:335,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:334,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:324,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:114,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LecturesPage.tsx",lineNumber:99,columnNumber:5},globalThis)},go=()=>{const{id:r}=We(),s=Ne(),{isAuthenticated:o}=le(),{success:a,error:n}=fe(),{execute:i}=he(async D=>D),[l,c]=d.useState(null),[u,f]=d.useState(!0),[g,p]=d.useState(!1),[b,w]=d.useState(!1),[k,h]=d.useState(0),[C,_]=d.useState(!1);d.useEffect(()=>{(async()=>{if(r){f(!0);try{const z=await i({url:`/lectures/${r}`,method:"GET"});z.success&&z.data&&(c(z.data),w(z.data.is_liked||!1),h(z.data.likes_count))}catch(z){console.error("강의 조회 실패:",z),n("강의 정보를 불러올 수 없습니다."),s("/lectures")}finally{f(!1)}}})()},[r]);const v=async()=>{if(!o){n("로그인이 필요한 서비스입니다."),s("/auth/login",{state:{from:location.pathname}});return}if(l){p(!0);try{(await i({url:`/lectures/${l.id}/enroll`,method:"POST"})).success&&(a("강의 등록이 완료되었습니다!","수강 시작"),c(z=>z?{...z,is_enrolled:!0,enrollment_count:z.enrollment_count+1}:null))}catch(D){const z=D instanceof Error?D.message:"강의 등록에 실패했습니다.";n(z)}finally{p(!1)}}},N=async()=>{if(!o){n("로그인이 필요한 서비스입니다.");return}if(l)try{(await i({url:`/lectures/${l.id}/like`,method:b?"DELETE":"POST"})).success&&(w(!b),h(z=>b?z-1:z+1))}catch(D){console.error("좋아요 처리 실패:",D)}},m=D=>D===0?"무료":`${D.toLocaleString()}원`,E=D=>new Date(D).toLocaleDateString("ko-KR",{year:"numeric",month:"long",day:"numeric"});return u?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"강의 정보를 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:124,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:123,columnNumber:7},globalThis):l?e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:[e.jsxDEV("div",{className:"bg-black",children:e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8",children:e.jsxDEV("div",{className:"aspect-video bg-gray-900 relative rounded-b-xl overflow-hidden",children:l.video_url?e.jsxDEV("iframe",{src:l.video_url,title:l.title,className:"w-full h-full",allowFullScreen:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:149,columnNumber:15},globalThis):l.thumbnail?e.jsxDEV("div",{className:"relative w-full h-full",children:[e.jsxDEV("img",{src:l.thumbnail,alt:l.title,className:"w-full h-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:157,columnNumber:17},globalThis),e.jsxDEV("div",{className:"absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center",children:e.jsxDEV("div",{className:"text-center text-white",children:[e.jsxDEV("svg",{className:"w-20 h-20 mx-auto mb-4",fill:"currentColor",viewBox:"0 0 20 20",children:e.jsxDEV("path",{fillRule:"evenodd",d:"M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z",clipRule:"evenodd"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:165,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:164,columnNumber:21},globalThis),e.jsxDEV("p",{className:"text-lg",children:"미리보기 준비 중"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:167,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:163,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:162,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:156,columnNumber:15},globalThis):e.jsxDEV("div",{className:"flex items-center justify-center h-full text-white",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("svg",{className:"w-20 h-20 mx-auto mb-4",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:175,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:174,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-lg",children:"강의 영상 준비 중"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:177,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:173,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:172,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:147,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:146,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:145,columnNumber:7},globalThis),e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:e.jsxDEV("div",{className:"grid grid-cols-1 lg:grid-cols-3 gap-8",children:[e.jsxDEV("div",{className:"lg:col-span-2",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-6",children:[e.jsxDEV("div",{className:"mb-4",children:[e.jsxDEV("h1",{className:"text-3xl font-bold text-gray-900 mb-2",children:l.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:192,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-lg text-gray-600",children:l.description},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:195,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:191,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex items-center mb-6",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4",children:l.instructor.profile_image?e.jsxDEV("img",{src:l.instructor.profile_image,alt:l.instructor.nickname,className:"w-12 h-12 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:204,columnNumber:21},globalThis):e.jsxDEV("svg",{className:"w-6 h-6 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:211,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:210,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:202,columnNumber:17},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("h3",{className:"font-semibold text-gray-900",children:l.instructor.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:216,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-600",children:"강사"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:219,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:215,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:201,columnNumber:15},globalThis),e.jsxDEV("div",{className:"grid grid-cols-2 md:grid-cols-4 gap-4 mb-6",children:[e.jsxDEV("div",{className:"text-center p-3 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-blue-600",children:l.views.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:226,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"조회수"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:229,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:225,columnNumber:17},globalThis),e.jsxDEV("div",{className:"text-center p-3 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-green-600",children:l.enrollment_count.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:232,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"수강생"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:235,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:231,columnNumber:17},globalThis),e.jsxDEV("div",{className:"text-center p-3 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-red-600",children:k.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:238,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"좋아요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:241,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:237,columnNumber:17},globalThis),e.jsxDEV("div",{className:"text-center p-3 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-purple-600",children:l.duration?`${Math.floor(l.duration/60)}분`:"-"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:244,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"강의 시간"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:247,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:243,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:224,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex space-x-3",children:[e.jsxDEV(A,{onClick:N,variant:b?"primary":"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:b?"currentColor":"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:258,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:257,columnNumber:21},globalThis),children:b?"좋아요 취소":"좋아요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:253,columnNumber:17},globalThis),e.jsxDEV(A,{variant:"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:269,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:268,columnNumber:21},globalThis),children:"공유하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:265,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:252,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:190,columnNumber:13},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-4",children:"강의 소개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:280,columnNumber:15},globalThis),e.jsxDEV("div",{className:"prose max-w-none",children:[e.jsxDEV("div",{className:`text-gray-700 leading-relaxed ${C?"":"line-clamp-6"}`,dangerouslySetInnerHTML:{__html:l.content}},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:284,columnNumber:17},globalThis),l.content.length>500&&e.jsxDEV("button",{onClick:()=>_(!C),className:"mt-4 text-blue-600 hover:text-blue-700 font-medium",children:C?"접기":"더 보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:291,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:283,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:279,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:188,columnNumber:11},globalThis),e.jsxDEV("div",{className:"lg:col-span-1",children:e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 sticky top-8",children:[e.jsxDEV("div",{className:"text-center mb-6",children:[e.jsxDEV("div",{className:"text-3xl font-bold text-gray-900 mb-2",children:m(l.price)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:307,columnNumber:17},globalThis),l.price>0&&e.jsxDEV("p",{className:"text-sm text-gray-600",children:"일시불 결제"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:311,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:306,columnNumber:15},globalThis),l.status==="ACTIVE"?e.jsxDEV(e.Fragment,{children:l.is_enrolled?e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV(A,{fullWidth:!0,size:"lg",className:"bg-green-600 hover:bg-green-700",disabled:!0,children:"✓ 수강 중"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:321,columnNumber:23},globalThis),e.jsxDEV(A,{fullWidth:!0,variant:"outline",onClick:()=>s(`/lectures/${l.id}/learn`),children:"강의 시청하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:329,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:320,columnNumber:21},globalThis):e.jsxDEV(A,{onClick:v,loading:g,fullWidth:!0,size:"lg",className:"bg-blue-600 hover:bg-blue-700",children:l.price===0?"무료 수강 신청":"수강 신청"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:338,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:318,columnNumber:17},globalThis):e.jsxDEV(A,{fullWidth:!0,size:"lg",disabled:!0,className:"bg-gray-400",children:"수강 불가"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:350,columnNumber:17},globalThis),e.jsxDEV("div",{className:"mt-6 pt-6 border-t border-gray-200",children:e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"등록일"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:364,columnNumber:21},globalThis),e.jsxDEV("span",{className:"font-medium",children:E(l.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:365,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:363,columnNumber:19},globalThis),e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"마지막 업데이트"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:368,columnNumber:21},globalThis),e.jsxDEV("span",{className:"font-medium",children:E(l.updated_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:369,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:367,columnNumber:19},globalThis),l.duration&&e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"총 강의 시간"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:373,columnNumber:23},globalThis),e.jsxDEV("span",{className:"font-medium",children:[Math.floor(l.duration/60),"분"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:374,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:372,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"수강생 수"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:380,columnNumber:21},globalThis),e.jsxDEV("span",{className:"font-medium",children:[l.enrollment_count.toLocaleString(),"명"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:381,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:379,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:362,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:361,columnNumber:15},globalThis),e.jsxDEV("div",{className:"mt-6 pt-6 border-t border-gray-200",children:[e.jsxDEV("h3",{className:"font-semibold text-gray-900 mb-3",children:"강사 정보"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:390,columnNumber:17},globalThis),e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3",children:l.instructor.profile_image?e.jsxDEV("img",{src:l.instructor.profile_image,alt:l.instructor.nickname,className:"w-10 h-10 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:394,columnNumber:23},globalThis):e.jsxDEV("svg",{className:"w-5 h-5 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:401,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:400,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:392,columnNumber:19},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("div",{className:"font-medium text-gray-900",children:l.instructor.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:406,columnNumber:21},globalThis),l.instructor.introduction&&e.jsxDEV("div",{className:"text-sm text-gray-600 line-clamp-2",children:l.instructor.introduction},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:410,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:405,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:391,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:389,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:305,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:303,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:186,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:185,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:143,columnNumber:5},globalThis):e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-4",children:"강의를 찾을 수 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:133,columnNumber:11},globalThis),e.jsxDEV(L,{to:"/lectures",children:e.jsxDEV(A,{children:"강의 목록으로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:135,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:134,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:132,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/lectures/LectureDetailPage.tsx",lineNumber:131,columnNumber:7},globalThis)},po=()=>{const[r,s]=Cr(),[o,a]=d.useState([]),[n,i]=d.useState(!0),[l,c]=d.useState(r.get("search")||""),[u,f]=d.useState(r.get("filter")||"all"),[g,p]=d.useState(parseInt(r.get("page")||"1")),{isAuthenticated:b}=le(),{execute:w}=he(async v=>v),k=async(v={})=>{i(!0);try{const N={query:l,filter:u,sort_by:"created_at",sort_direction:"desc",page:g,per_page:10,status:"PUBLISHED",...v},m=await w({url:"/posts",method:"GET",params:N});m.success&&m.data&&a(m.data.data)}catch(N){console.error("게시글 목록 조회 실패:",N)}finally{i(!1)}};d.useEffect(()=>{k()},[l,u,g]);const h=v=>{v.preventDefault(),p(1);const N=new URLSearchParams;l&&N.set("search",l),u!=="all"&&N.set("filter",u),N.set("page","1"),s(N)},C=v=>{const N=new Date(v),E=Math.abs(new Date().getTime()-N.getTime()),D=Math.ceil(E/(1e3*60*60*24));return D===1?"오늘":D===2?"어제":D<=7?`${D-1}일 전`:N.toLocaleDateString("ko-KR",{month:"short",day:"numeric"})},_=(v,N=150)=>{const m=v.replace(/<[^>]*>/g,"");return m.length>N?m.substring(0,N)+"...":m};return e.jsxDEV(e.Fragment,{children:[e.jsxDEV("style",{children:`
          .community-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            min-height: calc(100vh - 200px);
          }
          
          .community-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 12px;
          }
          
          .community-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
          }
          
          .community-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
          }
          
          .board-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
          }
          
          .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
          }
          
          .search-filter {
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            background: #fff;
            color: #4a5568;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 100px;
          }
          
          .search-filter:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
          }
          
          .search-input {
            padding: 12px 45px 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            width: 250px;
            transition: all 0.3s ease;
            background: #fff;
            position: relative;
          }
          
          .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-1px);
          }
          
          .search-btn {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            color: #ffffff;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
            transition: all 0.2s ease;
          }
          
          .search-btn:hover {
            transform: translateY(-50%) scale(1.05);
            box-shadow: 0 4px 12px rgba(55, 65, 81, 0.4);
          }
          
          .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
          }
          
          .btn-write {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: white;
            font-weight: 700;
          }
          
          .btn-write:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(55, 65, 81, 0.4);
            text-decoration: none;
          }
          
          .btn-secondary {
            background: #718096;
            color: white;
          }
          
          .btn-secondary:hover {
            background: #4a5568;
          }
          
          .board-stats {
            background: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
          }
          
          .stats-text {
            color: #4a5568;
            font-size: 14px;
            margin: 0;
          }
          
          .post-list {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
          }
          
          .post-item {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            transition: background-color 0.2s ease;
            cursor: pointer;
            display: flex;
            gap: 15px;
            align-items: flex-start;
          }
          
          .post-item:hover {
            background-color: #f8fafc;
            transform: translateX(4px);
          }
          
          .post-item:last-child {
            border-bottom: none;
          }
          
          .post-author-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            flex-shrink: 0;
            overflow: hidden;
            position: relative;
          }
          
          .post-author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
          }
          
          .post-content-wrapper {
            flex: 1;
            min-width: 0;
          }
          
          .post-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            line-height: 1.4;
          }
          
          .post-title:hover {
            color: #667eea;
          }
          
          .post-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
            color: #718096;
            margin-bottom: 10px;
          }
          
          .post-author {
            font-weight: 600;
            color: #4a5568;
          }
          
          .post-date {
            color: #a0aec0;
          }
          
          .post-content-preview {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-top: 8px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
          }
          
          .post-stats {
            display: flex;
            gap: 15px;
            font-size: 0.85rem;
            color: #a0aec0;
            margin-top: 10px;
          }
          
          .stat-item {
            display: flex;
            align-items: center;
            gap: 4px;
          }
          
          .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 30px;
          }
          
          .page-link {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #4a5568;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            transition: all 0.2s ease;
          }
          
          .page-link:hover {
            background: #f8fafc;
            border-color: #667eea;
            color: #667eea;
          }
          
          .page-link.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
          }
          
          .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
          }
          
          .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
          }
          
          .empty-state h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #4a5568;
          }
          
          .empty-state p {
            font-size: 0.9rem;
            margin-bottom: 20px;
          }
          
          @media (max-width: 768px) {
            .community-container {
              padding: 15px;
            }
            
            .community-header {
              padding: 30px 20px;
            }
            
            .community-header h1 {
              font-size: 2rem;
            }
            
            .board-controls {
              flex-direction: column;
              align-items: stretch;
            }
            
            .search-form {
              justify-content: center;
              margin-bottom: 15px;
            }
            
            .search-input {
              width: 100%;
              max-width: 300px;
            }
            
            .post-item {
              padding: 15px;
            }
            
            .post-meta {
              flex-direction: column;
              align-items: flex-start;
              gap: 5px;
            }
            
            .pagination {
              flex-wrap: wrap;
            }
          }
        `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:96,columnNumber:7},globalThis),e.jsxDEV("div",{className:"community-container",children:[e.jsxDEV("div",{className:"community-header",children:[e.jsxDEV("h1",{children:"💬 커뮤니티 게시판"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:459,columnNumber:11},globalThis),e.jsxDEV("p",{children:"탑마케팅 커뮤니티에서 정보를 공유하고 함께 성장하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:460,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:458,columnNumber:9},globalThis),e.jsxDEV("div",{className:"board-controls",children:[e.jsxDEV("div",{className:"search-wrapper",children:e.jsxDEV("form",{onSubmit:h,className:"search-form",children:[e.jsxDEV("select",{value:u,onChange:v=>f(v.target.value),className:"search-filter",children:[e.jsxDEV("option",{value:"all",children:"전체"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:474,columnNumber:17},globalThis),e.jsxDEV("option",{value:"title",children:"제목만"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:475,columnNumber:17},globalThis),e.jsxDEV("option",{value:"content",children:"내용만"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:476,columnNumber:17},globalThis),e.jsxDEV("option",{value:"author",children:"작성자"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:477,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:469,columnNumber:15},globalThis),e.jsxDEV("div",{style:{position:"relative",flex:"1",minWidth:"250px"},children:[e.jsxDEV("input",{type:"text",value:l,onChange:v=>c(v.target.value),placeholder:"검색어를 입력하세요...",className:"search-input",maxLength:100,autoComplete:"off"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:481,columnNumber:17},globalThis),e.jsxDEV("button",{type:"submit",className:"search-btn",children:e.jsxDEV("i",{className:"fas fa-search"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:491,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:490,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:480,columnNumber:15},globalThis),l&&e.jsxDEV(L,{to:"/community",className:"btn btn-secondary",children:"✖️ 검색 해제"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:496,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:467,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:466,columnNumber:11},globalThis),b?e.jsxDEV(L,{to:"/community/write",className:"btn btn-write",children:[e.jsxDEV("i",{className:"fas fa-pen"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:506,columnNumber:15},globalThis)," 글쓰기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:505,columnNumber:13},globalThis):e.jsxDEV(L,{to:`/auth/login?redirect=${encodeURIComponent(window.location.pathname)}`,className:"btn btn-primary",children:"🔑 로그인 후 글쓰기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:509,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:464,columnNumber:9},globalThis),e.jsxDEV("div",{className:"board-stats",children:e.jsxDEV("p",{className:"stats-text",children:["📊 총 ",e.jsxDEV("strong",{children:o.length},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:518,columnNumber:18},globalThis),"개의 게시글이 있습니다",l&&" (검색 결과)"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:517,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:516,columnNumber:9},globalThis),n?e.jsxDEV("div",{style:{display:"flex",justifyContent:"center",padding:"60px 0"},children:e.jsxDEV(be,{size:"lg",message:"게시글을 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:526,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:525,columnNumber:11},globalThis):e.jsxDEV("div",{className:"post-list",children:o.length>0?o.map(v=>{var N,m,E,D,z,T;return e.jsxDEV("div",{className:"post-item",onClick:()=>window.location.href=`/community/${v.id}`,children:[e.jsxDEV("div",{className:"post-author-avatar profile-image-clickable","data-user-id":(N=v.user)==null?void 0:N.id,"data-user-name":((m=v.user)==null?void 0:m.nickname)||"익명",title:"프로필 이미지 크게 보기",onClick:P=>{P.stopPropagation()},children:(E=v.user)!=null&&E.profile_image?e.jsxDEV("img",{src:v.user.profile_image,alt:((D=v.user)==null?void 0:D.nickname)||"익명",loading:"lazy",width:"50",height:"50",style:{objectFit:"cover",borderRadius:"50%"}},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:549,columnNumber:23},globalThis):(((z=v.user)==null?void 0:z.nickname)||"?").charAt(0)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:538,columnNumber:19},globalThis),e.jsxDEV("div",{className:"post-content-wrapper",children:[e.jsxDEV("div",{className:"post-title",children:[v.title,(v.comments_count||0)>0&&e.jsxDEV("span",{style:{color:"#e53e3e",fontSize:"0.9rem"},children:["[",v.comments_count,"]"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:567,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:564,columnNumber:21},globalThis),e.jsxDEV("div",{className:"post-meta",children:[e.jsxDEV("span",{className:"post-author",children:["👤 ",((T=v.user)==null?void 0:T.nickname)||"익명"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:574,columnNumber:23},globalThis),e.jsxDEV("span",{className:"post-date",children:["📅 ",C(v.created_at)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:575,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:573,columnNumber:21},globalThis),e.jsxDEV("div",{className:"post-content-preview",children:_(v.content)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:578,columnNumber:21},globalThis),e.jsxDEV("div",{className:"post-stats",children:[e.jsxDEV("span",{className:"stat-item",children:["👁️ ",(v.views||0).toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:583,columnNumber:23},globalThis),e.jsxDEV("span",{className:"stat-item",children:["💬 ",(v.comments_count||0).toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:586,columnNumber:23},globalThis),e.jsxDEV("span",{className:"stat-item",children:["❤️ ",(v.likes_count||0).toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:589,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:582,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:563,columnNumber:19},globalThis)]},v.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:532,columnNumber:17},globalThis)}):e.jsxDEV("div",{className:"empty-state",children:[e.jsxDEV("div",{style:{fontSize:"3rem",marginBottom:"20px",color:"#cbd5e0"},children:"📝"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:598,columnNumber:17},globalThis),e.jsxDEV("h3",{children:l?`"${l}" 검색 결과가 없습니다`:"첫 번째 게시글을 작성해보세요!"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:599,columnNumber:17},globalThis),e.jsxDEV("p",{children:l?e.jsxDEV(e.Fragment,{children:["💡 검색 팁:",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:604,columnNumber:31},globalThis),"• 검색어의 철자를 확인해보세요",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:605,columnNumber:38},globalThis),"• 더 간단한 키워드로 다시 검색해보세요",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:606,columnNumber:43},globalThis),"• 관련된 다른 단어로 검색해보세요"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:604,columnNumber:21},globalThis):"탑마케팅 커뮤니티의 첫 번째 이야기를 시작해보세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:602,columnNumber:17},globalThis),b&&e.jsxDEV(L,{to:"/community/write",className:"btn btn-primary",children:[e.jsxDEV("i",{className:"fas fa-pen"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:614,columnNumber:21},globalThis)," 글쓰기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:613,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:597,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:529,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:456,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/CommunityPage.tsx",lineNumber:95,columnNumber:5},globalThis)},bo=()=>{const{id:r}=We(),s=Ne(),{user:o,isAuthenticated:a}=le(),{success:n,error:i}=fe(),{execute:l}=he(async j=>j),[c,u]=d.useState(null),[f,g]=d.useState([]),[p,b]=d.useState(!0),[w,k]=d.useState(!1),[h,C]=d.useState(0),[_,v]=d.useState(""),[N,m]=d.useState(!1),[E,D]=d.useState(null),[z,T]=d.useState("");d.useEffect(()=>{(async()=>{if(r){b(!0);try{const M=await l({url:`/posts/${r}`,method:"GET"});M.success&&M.data&&(u(M.data),k(M.data.is_liked||!1),C(M.data.likes_count),g(M.data.comments||[]))}catch(M){console.error("게시글 조회 실패:",M),i("게시글을 불러올 수 없습니다."),s("/community")}finally{b(!1)}}})()},[r]);const P=async()=>{if(!a){i("로그인이 필요한 서비스입니다.");return}if(c)try{(await l({url:`/posts/${c.id}/like`,method:w?"DELETE":"POST"})).success&&(k(!w),C(M=>w?M-1:M+1))}catch(j){console.error("좋아요 처리 실패:",j)}},H=async j=>{if(j.preventDefault(),!a){i("로그인이 필요한 서비스입니다.");return}if(!(!c||!_.trim())){m(!0);try{const M={post_id:c.id,content:_.trim()},B=await l({url:"/comments",method:"POST",data:M});B.success&&B.data&&(g(J=>[...J,B.data]),v(""),n("댓글이 등록되었습니다."),u(J=>J?{...J,comments_count:J.comments_count+1}:null))}catch(M){const B=M instanceof Error?M.message:"댓글 등록에 실패했습니다.";i(B)}finally{m(!1)}}},q=async j=>{if(!a){i("로그인이 필요한 서비스입니다.");return}if(!(!c||!z.trim())){m(!0);try{const M={post_id:c.id,parent_id:j,content:z.trim()},B=await l({url:"/comments",method:"POST",data:M});B.success&&B.data&&(g(J=>J.map(S=>S.id===j?{...S,replies:[...S.replies||[],B.data]}:S)),T(""),D(null),n("답글이 등록되었습니다."))}catch(M){const B=M instanceof Error?M.message:"답글 등록에 실패했습니다.";i(B)}finally{m(!1)}}},R=async()=>{if(!(!c||!window.confirm("정말로 이 게시글을 삭제하시겠습니까?")))try{(await l({url:`/posts/${c.id}`,method:"DELETE"})).success&&(n("게시글이 삭제되었습니다."),s("/community"))}catch(j){const M=j instanceof Error?j.message:"게시글 삭제에 실패했습니다.";i(M)}},W=j=>new Date(j).toLocaleDateString("ko-KR",{year:"numeric",month:"long",day:"numeric",hour:"2-digit",minute:"2-digit"});return p?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"게시글을 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:202,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:201,columnNumber:7},globalThis):c?e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:e.jsxDEV("div",{className:"max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"mb-6",children:e.jsxDEV(L,{to:"/community",className:"inline-flex items-center text-blue-600 hover:text-blue-700",children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 19l-7-7 7-7"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:230,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:229,columnNumber:13},globalThis),"커뮤니티로 돌아가기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:225,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:224,columnNumber:9},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-8 mb-6",children:[e.jsxDEV("div",{className:"border-b border-gray-200 pb-6 mb-6",children:[e.jsxDEV("h1",{className:"text-3xl font-bold text-gray-900 mb-4",children:c.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:240,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4",children:c.user.profile_image?e.jsxDEV("img",{src:c.user.profile_image,alt:c.user.nickname,className:"w-12 h-12 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:248,columnNumber:21},globalThis):e.jsxDEV("svg",{className:"w-6 h-6 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:255,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:254,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:246,columnNumber:17},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("div",{className:"font-semibold text-gray-900",children:c.user.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:260,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:W(c.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:263,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:259,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:245,columnNumber:15},globalThis),o&&o.id===c.user.id&&e.jsxDEV("div",{className:"flex space-x-2",children:[e.jsxDEV(L,{to:`/community/${c.id}/edit`,children:e.jsxDEV(A,{variant:"outline",size:"sm",children:"수정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:273,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:272,columnNumber:19},globalThis),e.jsxDEV(A,{variant:"outline",size:"sm",onClick:R,className:"text-red-600 border-red-200 hover:bg-red-50",children:"삭제"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:277,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:271,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:244,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:239,columnNumber:11},globalThis),e.jsxDEV("div",{className:"prose max-w-none mb-6",dangerouslySetInnerHTML:{__html:c.content}},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:291,columnNumber:11},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between pt-6 border-t border-gray-200",children:[e.jsxDEV("div",{className:"flex items-center space-x-6 text-sm text-gray-600",children:[e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 12a3 3 0 11-6 0 3 3 0 016 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:301,columnNumber:19},globalThis),e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:302,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:300,columnNumber:17},globalThis),"조회 ",c.views.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:299,columnNumber:15},globalThis),e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:308,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:307,columnNumber:17},globalThis),"좋아요 ",h.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:306,columnNumber:15},globalThis),e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:314,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:313,columnNumber:17},globalThis),"댓글 ",c.comments_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:312,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:298,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex space-x-3",children:[e.jsxDEV(A,{onClick:P,variant:w?"primary":"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:w?"currentColor":"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:326,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:325,columnNumber:19},globalThis),children:w?"좋아요 취소":"좋아요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:321,columnNumber:15},globalThis),e.jsxDEV(A,{variant:"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:337,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:336,columnNumber:19},globalThis),children:"공유하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:333,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:320,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:297,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:237,columnNumber:9},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:["댓글 ",f.length.toLocaleString(),"개"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:349,columnNumber:11},globalThis),a?e.jsxDEV("form",{onSubmit:H,className:"mb-8",children:[e.jsxDEV("div",{className:"mb-4",children:e.jsxDEV("textarea",{value:_,onChange:j=>v(j.target.value),placeholder:"댓글을 작성해주세요...",rows:4,className:"w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:357,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:356,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex justify-between items-center",children:[e.jsxDEV("span",{className:"text-sm text-gray-500",children:[_.length,"/500"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:366,columnNumber:17},globalThis),e.jsxDEV(A,{type:"submit",loading:N,disabled:!_.trim()||_.length>500,children:"댓글 등록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:369,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:365,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:355,columnNumber:13},globalThis):e.jsxDEV("div",{className:"mb-8 p-4 bg-gray-50 rounded-lg text-center",children:[e.jsxDEV("p",{className:"text-gray-600 mb-4",children:"댓글을 작성하려면 로그인이 필요합니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:380,columnNumber:15},globalThis),e.jsxDEV(L,{to:"/auth/login",children:e.jsxDEV(A,{children:"로그인하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:382,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:381,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:379,columnNumber:13},globalThis),e.jsxDEV("div",{className:"space-y-6",children:f.map(j=>e.jsxDEV("div",{className:"border-b border-gray-100 pb-6 last:border-b-0",children:e.jsxDEV("div",{className:"flex items-start space-x-3",children:[e.jsxDEV("div",{className:"w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center",children:j.user.profile_image?e.jsxDEV("img",{src:j.user.profile_image,alt:j.user.nickname,className:"w-10 h-10 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:395,columnNumber:23},globalThis):e.jsxDEV("svg",{className:"w-5 h-5 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:402,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:401,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:393,columnNumber:19},globalThis),e.jsxDEV("div",{className:"flex-1",children:[e.jsxDEV("div",{className:"flex items-center space-x-2 mb-1",children:[e.jsxDEV("span",{className:"font-medium text-gray-900",children:j.user.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:408,columnNumber:23},globalThis),e.jsxDEV("span",{className:"text-sm text-gray-500",children:W(j.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:411,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:407,columnNumber:21},globalThis),e.jsxDEV("p",{className:"text-gray-700 mb-2",children:j.content},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:415,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex items-center space-x-4",children:[e.jsxDEV("button",{onClick:()=>D(E===j.id?null:j.id),className:"text-sm text-blue-600 hover:text-blue-700",children:"답글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:419,columnNumber:23},globalThis),j.replies&&j.replies.length>0&&e.jsxDEV("span",{className:"text-sm text-gray-500",children:["답글 ",j.replies.length,"개"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:426,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:418,columnNumber:21},globalThis),E===j.id&&a&&e.jsxDEV("div",{className:"mt-4 pl-4 border-l-2 border-gray-200",children:[e.jsxDEV("textarea",{value:z,onChange:M=>T(M.target.value),placeholder:"답글을 작성해주세요...",rows:3,className:"w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none mb-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:435,columnNumber:25},globalThis),e.jsxDEV("div",{className:"flex justify-end space-x-2",children:[e.jsxDEV(A,{variant:"ghost",size:"sm",onClick:()=>{D(null),T("")},children:"취소"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:443,columnNumber:27},globalThis),e.jsxDEV(A,{size:"sm",onClick:()=>q(j.id),loading:N,disabled:!z.trim(),children:"답글 등록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:453,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:442,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:434,columnNumber:23},globalThis),j.replies&&j.replies.length>0&&e.jsxDEV("div",{className:"mt-4 pl-4 border-l-2 border-gray-200 space-y-4",children:j.replies.map(M=>e.jsxDEV("div",{className:"flex items-start space-x-3",children:[e.jsxDEV("div",{className:"w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center",children:M.user.profile_image?e.jsxDEV("img",{src:M.user.profile_image,alt:M.user.nickname,className:"w-8 h-8 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:472,columnNumber:33},globalThis):e.jsxDEV("svg",{className:"w-4 h-4 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:479,columnNumber:35},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:478,columnNumber:33},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:470,columnNumber:29},globalThis),e.jsxDEV("div",{className:"flex-1",children:[e.jsxDEV("div",{className:"flex items-center space-x-2 mb-1",children:[e.jsxDEV("span",{className:"font-medium text-gray-900",children:M.user.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:485,columnNumber:33},globalThis),e.jsxDEV("span",{className:"text-sm text-gray-500",children:W(M.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:488,columnNumber:33},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:484,columnNumber:31},globalThis),e.jsxDEV("p",{className:"text-gray-700",children:M.content},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:492,columnNumber:31},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:483,columnNumber:29},globalThis)]},M.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:469,columnNumber:27},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:467,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:406,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:392,columnNumber:17},globalThis)},j.id,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:390,columnNumber:15},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:388,columnNumber:11},globalThis),f.length===0&&e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:510,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:509,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:508,columnNumber:15},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"아직 댓글이 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:513,columnNumber:15},globalThis),e.jsxDEV("p",{className:"text-gray-600",children:"첫 번째 댓글을 작성해보세요!"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:516,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:507,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:348,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:222,columnNumber:7},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:221,columnNumber:5},globalThis):e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-4",children:"게시글을 찾을 수 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:211,columnNumber:11},globalThis),e.jsxDEV(L,{to:"/community",children:e.jsxDEV(A,{children:"커뮤니티로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:213,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:212,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:210,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostDetailPage.tsx",lineNumber:209,columnNumber:7},globalThis)},No=()=>{const{id:r}=We(),s=Ne(),{user:o,isAuthenticated:a}=le(),{success:n,error:i}=fe(),{execute:l}=he(async m=>m),[c,u]=d.useState(!!r),[f,g]=d.useState(!1),[p,b]=d.useState({title:"",content:""}),[w,k]=d.useState({}),h=!!r;d.useEffect(()=>{a||(i("로그인이 필요한 서비스입니다."),s("/auth/login",{state:{from:location.pathname}}))},[a]),d.useEffect(()=>{h&&r&&(async()=>{u(!0);try{const E=await l({url:`/posts/${r}`,method:"GET"});if(E.success&&E.data){const D=E.data;if(o&&o.id!==D.user.id){i("게시글을 수정할 권한이 없습니다."),s("/community");return}b({title:D.title,content:D.content})}}catch(E){console.error("게시글 조회 실패:",E),i("게시글을 불러올 수 없습니다."),s("/community")}finally{u(!1)}})()},[h,r,o]);const C=()=>{const m={};return p.title.trim()?p.title.length<2?m.title="제목은 2자 이상 입력해주세요.":p.title.length>200&&(m.title="제목은 200자 이하로 입력해주세요."):m.title="제목을 입력해주세요.",p.content.trim()?p.content.length<10?m.content="내용은 10자 이상 입력해주세요.":p.content.length>1e4&&(m.content="내용은 10,000자 이하로 입력해주세요."):m.content="내용을 입력해주세요.",k(m),Object.keys(m).length===0},_=m=>{const{name:E,value:D}=m.target;b(z=>({...z,[E]:D})),w[E]&&k(z=>({...z,[E]:""}))},v=async m=>{if(m.preventDefault(),!!C()){g(!0);try{if(h&&r){const E={title:p.title.trim(),content:p.content.trim()};(await l({url:`/posts/${r}`,method:"PUT",data:E})).success&&(n("게시글이 수정되었습니다."),s(`/community/${r}`))}else{const E={title:p.title.trim(),content:p.content.trim()},D=await l({url:"/posts",method:"POST",data:E});D.success&&D.data&&(n("게시글이 등록되었습니다."),s(`/community/${D.data.id}`))}}catch(E){const D=E instanceof Error?E.message:h?"게시글 수정에 실패했습니다.":"게시글 등록에 실패했습니다.";i(D)}finally{g(!1)}}},N=async()=>{if(!p.title.trim()&&!p.content.trim()){i("저장할 내용이 없습니다.");return}n("임시저장되었습니다.")};return c?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"게시글 정보를 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:184,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:183,columnNumber:7},globalThis):e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:e.jsxDEV("div",{className:"max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"mb-6",children:e.jsxDEV(L,{to:"/community",className:"inline-flex items-center text-blue-600 hover:text-blue-700",children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 19l-7-7 7-7"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:199,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:198,columnNumber:13},globalThis),"커뮤니티로 돌아가기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:194,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:193,columnNumber:9},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-6",children:[e.jsxDEV("h1",{className:"text-3xl font-bold text-gray-900",children:h?"게시글 수정":"새 게시글 작성"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:207,columnNumber:11},globalThis),e.jsxDEV("p",{className:"text-gray-600 mt-2",children:h?"게시글을 수정하여 더 나은 내용으로 업데이트하세요.":"다른 회원들과 소중한 경험과 지식을 공유해보세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:210,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:206,columnNumber:9},globalThis),e.jsxDEV("form",{onSubmit:v,className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV("div",{children:e.jsxDEV(ae,{label:"제목",name:"title",value:p.title,onChange:_,error:w.title,placeholder:"게시글 제목을 입력해주세요",required:!0,fullWidth:!0,maxLength:200,hint:`${p.title.length}/200`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:223,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:222,columnNumber:13},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"block text-sm font-medium text-gray-700 mb-2",children:["내용 ",e.jsxDEV("span",{className:"text-red-500",children:"*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:240,columnNumber:20},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:239,columnNumber:15},globalThis),e.jsxDEV("textarea",{name:"content",value:p.content,onChange:_,placeholder:"내용을 입력해주세요. 마크다운 문법을 사용할 수 있습니다.",rows:15,className:`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${w.content?"border-red-300":"border-gray-300"}`,maxLength:1e4},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:242,columnNumber:15},globalThis),e.jsxDEV("div",{className:"flex justify-between items-center mt-2",children:[w.content&&e.jsxDEV("p",{className:"text-red-600 text-sm",children:w.content},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:255,columnNumber:19},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-500 ml-auto",children:[p.content.length.toLocaleString(),"/10,000"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:257,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:253,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:238,columnNumber:13},globalThis),e.jsxDEV("div",{className:"bg-blue-50 rounded-lg p-4",children:[e.jsxDEV("h3",{className:"text-sm font-semibold text-blue-900 mb-2",children:"📝 작성 가이드"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:265,columnNumber:15},globalThis),e.jsxDEV("ul",{className:"text-sm text-blue-800 space-y-1",children:[e.jsxDEV("li",{children:"• 다른 회원들에게 도움이 되는 내용을 작성해주세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:269,columnNumber:17},globalThis),e.jsxDEV("li",{children:"• 구체적이고 명확한 제목을 사용해주세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:270,columnNumber:17},globalThis),e.jsxDEV("li",{children:"• 마크다운 문법을 사용하여 내용을 꾸밀 수 있습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:271,columnNumber:17},globalThis),e.jsxDEV("li",{children:"• 개인정보나 민감한 정보는 포함하지 마세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:272,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:268,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:264,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex justify-between items-center pt-6 border-t border-gray-200",children:[e.jsxDEV("div",{className:"flex space-x-3",children:[e.jsxDEV(A,{type:"button",variant:"ghost",onClick:()=>s("/community"),children:"취소"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:279,columnNumber:17},globalThis),e.jsxDEV(A,{type:"button",variant:"outline",onClick:N,disabled:f,children:"임시저장"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:286,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:278,columnNumber:15},globalThis),e.jsxDEV(A,{type:"submit",loading:f,disabled:!p.title.trim()||!p.content.trim(),className:"bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700",children:h?"수정 완료":"게시글 등록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:296,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:277,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:220,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:219,columnNumber:9},globalThis),e.jsxDEV("div",{className:"mt-6 bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("details",{className:"group",children:[e.jsxDEV("summary",{className:"cursor-pointer text-lg font-semibold text-gray-900 flex items-center",children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2 transform group-open:rotate-90 transition-transform",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 5l7 7-7 7"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:313,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:312,columnNumber:15},globalThis),"마크다운 사용법"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:311,columnNumber:13},globalThis),e.jsxDEV("div",{className:"mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm",children:[e.jsxDEV("div",{children:[e.jsxDEV("h4",{className:"font-semibold text-gray-900 mb-2",children:"기본 문법"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:319,columnNumber:17},globalThis),e.jsxDEV("div",{className:"space-y-2 text-gray-600",children:[e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"# 제목 1"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:321,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:321,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"## 제목 2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:322,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:322,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"**굵은 글씨**"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:323,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:323,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"*기울임 글씨*"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:324,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:324,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"`코드`"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:325,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:325,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:320,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:318,columnNumber:15},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("h4",{className:"font-semibold text-gray-900 mb-2",children:"고급 기능"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:329,columnNumber:17},globalThis),e.jsxDEV("div",{className:"space-y-2 text-gray-600",children:[e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"[링크](URL)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:331,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:331,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"![이미지](URL)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:332,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:332,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"- 목록 항목"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:333,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:333,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"1. 번호 목록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:334,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:334,columnNumber:19},globalThis),e.jsxDEV("div",{children:e.jsxDEV("code",{className:"bg-gray-100 px-1 rounded",children:"> 인용문"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:335,columnNumber:24},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:335,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:330,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:328,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:317,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:310,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:309,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:191,columnNumber:7},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/community/PostWritePage.tsx",lineNumber:190,columnNumber:5},globalThis)},ho=()=>{const[r,s]=d.useState(new Date().getFullYear()),[o,a]=d.useState(new Date().getMonth()+1),[n,i]=d.useState("calendar"),[l]=d.useState([]),[c,u]=d.useState([]),[f]=d.useState(!1),[g,p]=d.useState(!1),[b,w]=d.useState([]),[k,h]=d.useState(""),C=["","1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"];d.useEffect(()=>{_()},[r,o]);const _=()=>{const R=new Date(r,o-1,1),W=new Date(R);W.setDate(W.getDate()-R.getDay());const j=[];let M=[];for(let B=0;B<42;B++){const J=new Date(W);J.setDate(W.getDate()+B);const S=J.getMonth()===o-1,U=J.toDateString()===new Date().toDateString();let X="";S||(X="other-month"),U&&(X+=" today"),M.push({date:J.toISOString().split("T")[0],day:J.getDate(),class:X.trim()}),M.length===7&&(j.push(M),M=[])}u(j)},v=(R,W)=>{s(R),a(W)},N=()=>{const R=o===1?12:o-1;return{year:o===1?r-1:r,month:R}},m=()=>{const R=o===12?1:o+1;return{year:o===12?r+1:r,month:R}},E=R=>{const W=l.filter(j=>j.start_date===R);W.length!==0&&(w(W),h(R),p(!0))},D=()=>{p(!1),w([]),h("")},z=R=>l.filter(W=>W.start_date===R),T=R=>{switch(R){case"large":return"scale-large";case"medium":return"scale-medium";case"small":return"scale-small";default:return""}},P=R=>{switch(R){case"large":return"대규모";case"medium":return"중규모";case"small":return"소규모";default:return""}},H=N(),q=m();return e.jsxDEV(dr,{children:[e.jsxDEV("div",{className:"events-container",children:[e.jsxDEV("div",{className:"events-header",children:[e.jsxDEV("h1",{children:"🎉 행사 일정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:140,columnNumber:11},globalThis),e.jsxDEV("p",{children:"다양한 마케팅 행사와 네트워킹 행사에 참여하세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:141,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:139,columnNumber:9},globalThis),e.jsxDEV("div",{className:"events-controls",children:[e.jsxDEV("div",{className:"events-navigation",children:[e.jsxDEV("div",{className:"month-nav",children:[e.jsxDEV("button",{className:"nav-btn",onClick:()=>v(H.year,H.month),children:e.jsxDEV("i",{className:"fas fa-chevron-left"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:153,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:149,columnNumber:15},globalThis),e.jsxDEV("div",{className:"current-month",children:[r,"년 ",C[o]]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:155,columnNumber:15},globalThis),e.jsxDEV("button",{className:"nav-btn",onClick:()=>v(q.year,q.month),children:e.jsxDEV("i",{className:"fas fa-chevron-right"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:162,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:158,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:148,columnNumber:13},globalThis),e.jsxDEV("div",{className:"view-toggle",children:[e.jsxDEV("button",{className:`view-btn ${n==="calendar"?"active":""}`,onClick:()=>i("calendar"),children:[e.jsxDEV("i",{className:"fas fa-calendar-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:172,columnNumber:17},globalThis)," 캘린더"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:168,columnNumber:15},globalThis),e.jsxDEV("button",{className:`view-btn ${n==="list"?"active":""}`,onClick:()=>i("list"),children:[e.jsxDEV("i",{className:"fas fa-list"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:178,columnNumber:17},globalThis)," 목록"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:174,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:167,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:146,columnNumber:11},globalThis),f?e.jsxDEV("a",{href:"/events/create",className:"create-event-btn",children:[e.jsxDEV("i",{className:"fas fa-plus"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:186,columnNumber:15},globalThis),"새 행사 등록"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:185,columnNumber:13},globalThis):e.jsxDEV("a",{href:"/auth/login",className:"create-event-btn",children:[e.jsxDEV("i",{className:"fas fa-sign-in-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:191,columnNumber:15},globalThis),"로그인 후 등록"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:190,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:145,columnNumber:9},globalThis),e.jsxDEV("div",{className:"calendar-container",children:[e.jsxDEV("div",{className:"calendar-header",children:["일","월","화","수","목","금","토"].map(R=>e.jsxDEV("div",{className:"calendar-day-header",children:R},R,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:202,columnNumber:15},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:200,columnNumber:11},globalThis),e.jsxDEV("div",{className:"calendar-grid",children:c.map((R,W)=>R.map((j,M)=>{const B=z(j.date),J=B.slice(0,3),S=B.length-3;return e.jsxDEV("div",{className:`calendar-day ${j.class}`,"data-date":j.date,children:[e.jsxDEV("div",{className:"calendar-day-number",children:j.day},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:220,columnNumber:21},globalThis),J.map((U,X)=>e.jsxDEV("div",{className:`event-item ${T(U.event_scale)}`,onClick:()=>window.location.href=`/events/detail?id=${U.id}`,title:U.title,children:[U.title.length>15?U.title.substring(0,15)+"...":U.title,U.has_networking&&e.jsxDEV("i",{className:"fas fa-users networking-icon",title:"네트워킹 포함"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:231,columnNumber:27},globalThis)]},X,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:223,columnNumber:23},globalThis)),S>0&&e.jsxDEV("div",{className:"more-events",onClick:()=>E(j.date),children:["+",S,"개 더보기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:237,columnNumber:23},globalThis)]},`${W}-${M}`,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:215,columnNumber:19},globalThis)}))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:207,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:198,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:137,columnNumber:7},globalThis),g&&e.jsxDEV("div",{className:"event-modal",style:{display:"flex"},children:e.jsxDEV("div",{className:"event-modal-content",children:[e.jsxDEV("div",{className:"event-modal-header",children:[e.jsxDEV("h3",{className:"event-modal-title",children:[k," 행사 목록"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:257,columnNumber:15},globalThis),e.jsxDEV("button",{className:"modal-close",onClick:D,children:"×"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:258,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:256,columnNumber:13},globalThis),e.jsxDEV("ul",{className:"event-list",children:b.map((R,W)=>e.jsxDEV("li",{className:"event-list-item",onClick:()=>window.location.href=`/events/detail?id=${R.id}`,style:{cursor:"pointer"},children:[e.jsxDEV("div",{className:"event-title",children:[R.title,R.event_scale&&e.jsxDEV("span",{className:`event-scale-badge ${R.event_scale}`,children:P(R.event_scale)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:271,columnNumber:23},globalThis),R.has_networking&&e.jsxDEV("i",{className:"fas fa-users networking-icon",title:"네트워킹 포함"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:276,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:268,columnNumber:19},globalThis),e.jsxDEV("div",{className:"event-details",children:[e.jsxDEV("div",{children:[e.jsxDEV("i",{className:"fas fa-clock"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:280,columnNumber:26},globalThis)," ",R.start_time]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:280,columnNumber:21},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("i",{className:"fas fa-map-marker-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:282,columnNumber:23},globalThis),R.location_type==="online"?"온라인":R.venue_name||"오프라인"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:281,columnNumber:21},globalThis),R.registration_fee&&e.jsxDEV("div",{children:[e.jsxDEV("i",{className:"fas fa-won-sign"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:287,columnNumber:25},globalThis),R.registration_fee.toLocaleString(),"원"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:286,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:279,columnNumber:19},globalThis)]},W,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:262,columnNumber:17},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:260,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:255,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:254,columnNumber:9},globalThis),e.jsxDEV("style",{children:`
        /* 행사 일정 페이지 스타일 (파란색 테마) */
        .events-container {
          max-width: 1600px;
          margin: 0 auto;
          padding: 30px 15px 20px 15px;
          min-height: calc(100vh - 200px);
          overflow-x: auto;
        }

        .events-header {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          padding: 40px 20px;
          text-align: center;
          margin-top: 60px;
          margin-bottom: 30px;
          border-radius: 12px;
          max-width: 1600px;
          margin-left: auto;
          margin-right: auto;
        }

        .events-header h1 {
          font-size: 2.5rem;
          margin-bottom: 10px;
          font-weight: 700;
        }

        .events-header p {
          font-size: 1.1rem;
          opacity: 0.9;
          margin-bottom: 20px;
        }

        .events-controls {
          display: flex;
          flex-direction: column;
          gap: 20px;
          margin-bottom: 30px;
          align-items: center;
        }

        .events-navigation {
          display: flex;
          align-items: center;
          gap: 20px;
          flex-wrap: wrap;
          justify-content: center;
        }

        .month-nav {
          display: flex;
          align-items: center;
          gap: 15px;
          background: white;
          padding: 10px 20px;
          border-radius: 50px;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .nav-btn {
          background: #4A90E2;
          color: white;
          border: none;
          padding: 8px 12px;
          border-radius: 50%;
          cursor: pointer;
          transition: background 0.3s;
          display: flex;
          align-items: center;
          justify-content: center;
          width: 40px;
          height: 40px;
        }

        .nav-btn:hover {
          background: #357ABD;
        }

        .current-month {
          font-size: 1.3rem;
          font-weight: 600;
          color: #2E86AB;
          min-width: 120px;
          text-align: center;
        }

        .view-toggle {
          display: flex;
          background: white;
          border-radius: 50px;
          overflow: hidden;
          box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .view-btn {
          padding: 10px 20px;
          border: none;
          background: white;
          color: #666;
          cursor: pointer;
          transition: all 0.3s;
          font-weight: 500;
        }

        .view-btn.active {
          background: #4A90E2;
          color: white;
        }

        .create-event-btn {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          border: none;
          padding: 12px 24px;
          border-radius: 50px;
          text-decoration: none !important;
          font-weight: 600;
          box-shadow: 0 4px 15px rgba(74, 144, 226, 0.3);
          transition: all 0.3s;
          display: inline-flex;
          align-items: center;
          gap: 8px;
        }

        .create-event-btn:hover {
          transform: translateY(-2px);
          box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
          color: white;
          text-decoration: none !important;
        }

        /* 캘린더 스타일 */
        .calendar-container {
          background: white;
          border-radius: 12px;
          box-shadow: 0 4px 20px rgba(0,0,0,0.1);
          overflow: hidden;
          margin-bottom: 30px;
        }

        .calendar-grid {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          gap: 1px;
          background: #f1f5f9;
        }

        .calendar-header {
          display: grid;
          grid-template-columns: repeat(7, 1fr);
          background: #2E86AB;
          color: white;
        }

        .calendar-day-header {
          padding: 15px;
          text-align: center;
          font-weight: 600;
          font-size: 0.9rem;
        }

        .calendar-day {
          background: white;
          min-height: 120px;
          padding: 8px;
          position: relative;
          transition: background 0.2s;
        }

        .calendar-day:hover {
          background: #f8fafc;
        }

        .calendar-day.other-month {
          background: #f8fafc;
          color: #94a3b8;
        }

        .calendar-day.today {
          background: #e0f2fe;
        }

        .calendar-day-number {
          font-weight: 600;
          margin-bottom: 5px;
          color: #1e293b;
        }

        .calendar-day.other-month .calendar-day-number {
          color: #94a3b8;
        }

        .event-item {
          background: linear-gradient(135deg, #4A90E2 0%, #2E86AB 100%);
          color: white;
          padding: 4px 8px;
          margin-bottom: 2px;
          border-radius: 4px;
          font-size: 0.75rem;
          cursor: pointer;
          transition: transform 0.2s;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .event-item:hover {
          transform: scale(1.02);
        }

        .event-item.scale-large {
          background: linear-gradient(135deg, #FF6B6B 0%, #EE5A24 100%);
        }

        .event-item.scale-medium {
          background: linear-gradient(135deg, #FFA726 0%, #FF7043 100%);
        }

        .event-item.scale-small {
          background: linear-gradient(135deg, #66BB6A 0%, #43A047 100%);
        }

        .more-events {
          color: #4A90E2;
          font-size: 0.7rem;
          cursor: pointer;
          text-align: center;
          padding: 2px;
          border-radius: 3px;
          background: #e0f2fe;
        }

        .more-events:hover {
          background: #b3e5fc;
        }

        /* 이벤트 모달 */
        .event-modal {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0,0,0,0.5);
          z-index: 1000;
          justify-content: center;
          align-items: center;
        }

        .event-modal-content {
          background: white;
          border-radius: 12px;
          padding: 30px;
          max-width: 500px;
          width: 90%;
          max-height: 80vh;
          overflow-y: auto;
        }

        .event-modal-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 20px;
          padding-bottom: 15px;
          border-bottom: 2px solid #e2e8f0;
        }

        .event-modal-title {
          color: #2E86AB;
          font-size: 1.5rem;
          font-weight: 700;
        }

        .modal-close {
          background: none;
          border: none;
          font-size: 1.5rem;
          cursor: pointer;
          color: #64748b;
          padding: 5px;
        }

        .modal-close:hover {
          color: #2E86AB;
        }

        .event-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .event-list-item {
          padding: 15px;
          border-bottom: 1px solid #e2e8f0;
          transition: background 0.2s;
        }

        .event-list-item:hover {
          background: #f8fafc;
        }

        .event-list-item:last-child {
          border-bottom: none;
        }

        .event-title {
          font-weight: 600;
          color: #1e293b;
          margin-bottom: 5px;
        }

        .event-details {
          color: #64748b;
          font-size: 0.9rem;
          display: flex;
          flex-direction: column;
          gap: 3px;
        }

        .event-scale-badge {
          display: inline-block;
          padding: 2px 8px;
          border-radius: 12px;
          font-size: 0.7rem;
          font-weight: 500;
          margin-left: 8px;
        }

        .event-scale-badge.large { background: #FFEBEE; color: #C62828; }
        .event-scale-badge.medium { background: #FFF3E0; color: #E65100; }
        .event-scale-badge.small { background: #E8F5E8; color: #2E7D32; }

        .networking-icon {
          color: #4A90E2;
          margin-left: 5px;
        }

        /* 반응형 */
        @media (max-width: 768px) {
          .events-container {
            padding: 20px 10px;
          }
          
          .events-header {
            margin-top: 20px;
            padding: 30px 20px;
          }
          
          .events-header h1 {
            font-size: 2rem;
          }
          
          .events-controls {
            flex-direction: column;
            gap: 15px;
          }
          
          .events-navigation {
            flex-direction: column;
            gap: 15px;
          }
          
          .calendar-day {
            min-height: 80px;
            padding: 5px;
          }
          
          .calendar-day-number {
            font-size: 0.9rem;
          }
          
          .event-item {
            font-size: 0.7rem;
            padding: 3px 6px;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:299,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/events/EventsPage.tsx",lineNumber:136,columnNumber:5},globalThis)},xo=()=>{const{userId:r}=We(),{user:s,isAuthenticated:o}=le(),{execute:a}=he(async N=>N),[n,i]=d.useState(null),[l,c]=d.useState([]),[u,f]=d.useState([]),[g,p]=d.useState(!0),[b,w]=d.useState("posts"),[k,h]=d.useState(!1),C=!r||s&&s.id.toString()===r;d.useEffect(()=>{(async()=>{p(!0);try{let m=r;if(!r&&s&&(m=s.id.toString()),m){const E=await a({url:`/users/${m}`,method:"GET"});E.success&&E.data&&(i(E.data),h(E.data.is_following||!1));const D=await a({url:"/posts",method:"GET",params:{user_id:m,status:"PUBLISHED",per_page:10}});D.success&&D.data&&c(D.data.data||[]);const z=await a({url:"/lectures",method:"GET",params:{instructor_id:m,status:"ACTIVE",per_page:10}});z.success&&z.data&&f(z.data.data||[])}}catch(m){console.error("프로필 조회 실패:",m)}finally{p(!1)}})()},[r,s]);const _=async()=>{if(!(!o||!n))try{(await a({url:`/users/${n.id}/follow`,method:k?"DELETE":"POST"})).success&&h(!k)}catch(N){console.error("팔로우 처리 실패:",N)}},v=N=>new Date(N).toLocaleDateString("ko-KR",{year:"numeric",month:"long",day:"numeric"});return g?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"프로필을 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:117,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:116,columnNumber:7},globalThis):n?e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:[e.jsxDEV("div",{className:"bg-gradient-to-r from-blue-600 to-purple-600 text-white",children:e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16",children:e.jsxDEV("div",{className:"flex flex-col md:flex-row items-center md:items-end space-y-4 md:space-y-0 md:space-x-6",children:[e.jsxDEV("div",{className:"w-32 h-32 bg-white bg-opacity-20 rounded-full flex items-center justify-center",children:n.profile_image?e.jsxDEV("img",{src:n.profile_image,alt:n.nickname,className:"w-32 h-32 rounded-full object-cover border-4 border-white"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:144,columnNumber:17},globalThis):e.jsxDEV("svg",{className:"w-16 h-16 text-white",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:151,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:150,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:142,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex-1 text-center md:text-left",children:[e.jsxDEV("h1",{className:"text-4xl font-bold mb-2",children:n.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:158,columnNumber:15},globalThis),n.introduction&&e.jsxDEV("p",{className:"text-xl text-blue-100 mb-4",children:n.introduction},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:162,columnNumber:17},globalThis),e.jsxDEV("div",{className:"flex flex-wrap justify-center md:justify-start items-center space-x-6 text-sm text-blue-100",children:[e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:169,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:168,columnNumber:19},globalThis),n.role==="ROLE_ADMIN"?"관리자":n.role==="ROLE_CORP"?"기업회원":"일반회원"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:167,columnNumber:17},globalThis),e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:176,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:175,columnNumber:19},globalThis),v(n.created_at)," 가입"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:174,columnNumber:17},globalThis),n.phone_verified&&e.jsxDEV("span",{className:"flex items-center",children:[e.jsxDEV("svg",{className:"w-4 h-4 mr-1 text-green-300",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:183,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:182,columnNumber:21},globalThis),"휴대폰 인증"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:181,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:166,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:157,columnNumber:13},globalThis),e.jsxDEV("div",{className:"flex space-x-3",children:C?e.jsxDEV(L,{to:"/profile/edit",children:e.jsxDEV(A,{variant:"outline",className:"border-white text-white hover:bg-white hover:text-blue-600",children:"프로필 수정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:195,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:194,columnNumber:17},globalThis):e.jsxDEV(e.Fragment,{children:[o&&e.jsxDEV(A,{onClick:_,variant:k?"outline":"primary",className:k?"border-white text-white hover:bg-white hover:text-blue-600":"bg-white text-blue-600 hover:bg-gray-100",children:k?"팔로우 취소":"팔로우"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:205,columnNumber:21},globalThis),e.jsxDEV(A,{variant:"outline",className:"border-white text-white hover:bg-white hover:text-blue-600",children:"메시지 보내기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:216,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:203,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:192,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:140,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:139,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:138,columnNumber:7},globalThis),e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-6",children:e.jsxDEV("div",{className:"border-b border-gray-200",children:e.jsxDEV("nav",{className:"-mb-px flex space-x-8",children:[{key:"posts",label:`게시글 (${l.length})`,icon:"document-text"},{key:"lectures",label:`강의 (${u.length})`,icon:"academic-cap"},{key:"about",label:"소개",icon:"information-circle"}].map(N=>e.jsxDEV("button",{onClick:()=>w(N.key),className:`flex items-center py-2 px-1 border-b-2 font-medium text-sm ${b===N.key?"border-blue-500 text-blue-600":"border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"}`,children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[N.icon==="document-text"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:250,columnNumber:23},globalThis),N.icon==="academic-cap"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 14l9-5-9-5-9 5 9 5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:253,columnNumber:23},globalThis),N.icon==="information-circle"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:256,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:248,columnNumber:19},globalThis),N.label]},N.key,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:239,columnNumber:17},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:233,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:232,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:231,columnNumber:9},globalThis),e.jsxDEV("div",{className:"space-y-6",children:[b==="posts"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:"최근 게시글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:271,columnNumber:15},globalThis),l.length>0?e.jsxDEV("div",{className:"space-y-4",children:l.map(N=>e.jsxDEV(L,{to:`/community/${N.id}`,className:"block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all",children:[e.jsxDEV("h3",{className:"font-semibold text-gray-900 mb-2 hover:text-blue-600",children:N.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:282,columnNumber:23},globalThis),e.jsxDEV("p",{className:"text-gray-600 text-sm mb-3 line-clamp-2",children:[N.content.replace(/<[^>]*>/g,"").substring(0,150),"..."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:285,columnNumber:23},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between text-xs text-gray-500",children:[e.jsxDEV("span",{children:v(N.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:289,columnNumber:25},globalThis),e.jsxDEV("div",{className:"flex space-x-4",children:[e.jsxDEV("span",{children:["조회 ",N.views.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:291,columnNumber:27},globalThis),e.jsxDEV("span",{children:["좋아요 ",N.likes_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:292,columnNumber:27},globalThis),e.jsxDEV("span",{children:["댓글 ",N.comments_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:293,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:290,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:288,columnNumber:23},globalThis)]},N.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:277,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:275,columnNumber:17},globalThis):e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:303,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:302,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:301,columnNumber:19},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"아직 작성한 게시글이 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:306,columnNumber:19},globalThis),C&&e.jsxDEV(L,{to:"/community/write",children:e.jsxDEV(A,{className:"mt-4",children:"첫 게시글 작성하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:311,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:310,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:300,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:270,columnNumber:13},globalThis),b==="lectures"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:"강의 목록"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:322,columnNumber:15},globalThis),u.length>0?e.jsxDEV("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6",children:u.map(N=>e.jsxDEV(L,{to:`/lectures/${N.id}`,className:"group",children:e.jsxDEV("div",{className:"border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all",children:[e.jsxDEV("div",{className:"aspect-video bg-gradient-to-br from-blue-500 to-purple-600",children:N.thumbnail?e.jsxDEV("img",{src:N.thumbnail,alt:N.title,className:"w-full h-full object-cover group-hover:scale-105 transition-transform"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:336,columnNumber:29},globalThis):e.jsxDEV("div",{className:"flex items-center justify-center h-full",children:e.jsxDEV("svg",{className:"w-12 h-12 text-white opacity-50",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:344,columnNumber:33},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:343,columnNumber:31},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:342,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:334,columnNumber:25},globalThis),e.jsxDEV("div",{className:"p-4",children:[e.jsxDEV("h3",{className:"font-semibold text-gray-900 mb-2 group-hover:text-blue-600",children:N.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:350,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-600 mb-3 line-clamp-2",children:N.description},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:353,columnNumber:27},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between text-xs text-gray-500",children:[e.jsxDEV("span",{children:N.price===0?"무료":`${N.price.toLocaleString()}원`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:357,columnNumber:29},globalThis),e.jsxDEV("span",{children:["수강생 ",N.enrollment_count.toLocaleString(),"명"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:358,columnNumber:29},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:356,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:349,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:333,columnNumber:23},globalThis)},N.id,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:328,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:326,columnNumber:17},globalThis):e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 14l9-5-9-5-9 5 9 5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:369,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:368,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:367,columnNumber:19},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"아직 등록한 강의가 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:372,columnNumber:19},globalThis),C&&n.role!=="ROLE_USER"&&e.jsxDEV(A,{className:"mt-4",children:"강의 등록하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:376,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:366,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:321,columnNumber:13},globalThis),b==="about"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-6",children:"소개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:386,columnNumber:15},globalThis),e.jsxDEV("div",{className:"space-y-6",children:[n.introduction?e.jsxDEV("div",{children:[e.jsxDEV("h3",{className:"text-lg font-semibold text-gray-900 mb-3",children:"자기소개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:392,columnNumber:21},globalThis),e.jsxDEV("p",{className:"text-gray-700 leading-relaxed",children:n.introduction},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:393,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:391,columnNumber:19},globalThis):e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("p",{className:"text-gray-500",children:"아직 자기소개가 없습니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:399,columnNumber:21},globalThis),C&&e.jsxDEV(L,{to:"/profile/edit",children:e.jsxDEV(A,{className:"mt-4",children:"프로필 수정하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:402,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:401,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:398,columnNumber:19},globalThis),e.jsxDEV("div",{className:"border-t border-gray-200 pt-6",children:[e.jsxDEV("h3",{className:"text-lg font-semibold text-gray-900 mb-3",children:"활동 정보"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:409,columnNumber:19},globalThis),e.jsxDEV("div",{className:"grid grid-cols-2 md:grid-cols-4 gap-4",children:[e.jsxDEV("div",{className:"text-center p-4 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-blue-600",children:l.length},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:412,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"작성한 게시글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:415,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:411,columnNumber:21},globalThis),e.jsxDEV("div",{className:"text-center p-4 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-green-600",children:u.length},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:418,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"등록한 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:421,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:417,columnNumber:21},globalThis),e.jsxDEV("div",{className:"text-center p-4 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-purple-600",children:"0"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:424,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"팔로워"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:427,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:423,columnNumber:21},globalThis),e.jsxDEV("div",{className:"text-center p-4 bg-gray-50 rounded-lg",children:[e.jsxDEV("div",{className:"text-2xl font-bold text-orange-600",children:"0"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:430,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-600",children:"팔로잉"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:433,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:429,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:410,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:408,columnNumber:17},globalThis),e.jsxDEV("div",{className:"border-t border-gray-200 pt-6",children:[e.jsxDEV("h3",{className:"text-lg font-semibold text-gray-900 mb-3",children:"계정 정보"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:439,columnNumber:19},globalThis),e.jsxDEV("div",{className:"space-y-3 text-sm",children:[e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"회원 등급"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:442,columnNumber:23},globalThis),e.jsxDEV("span",{className:"font-medium",children:n.role==="ROLE_ADMIN"?"관리자":n.role==="ROLE_CORP"?"기업회원":"일반회원"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:443,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:441,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"가입일"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:449,columnNumber:23},globalThis),e.jsxDEV("span",{className:"font-medium",children:v(n.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:450,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:448,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"마지막 로그인"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:453,columnNumber:23},globalThis),e.jsxDEV("span",{className:"font-medium",children:n.last_login_at?v(n.last_login_at):"정보 없음"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:454,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:452,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex justify-between",children:[e.jsxDEV("span",{className:"text-gray-600",children:"인증 상태"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:459,columnNumber:23},globalThis),e.jsxDEV("div",{className:"flex space-x-2",children:[n.phone_verified&&e.jsxDEV("span",{className:"inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800",children:"휴대폰 인증"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:462,columnNumber:27},globalThis),n.email_verified&&e.jsxDEV("span",{className:"inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800",children:"이메일 인증"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:467,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:460,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:458,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:440,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:438,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:389,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:385,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:267,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:229,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:136,columnNumber:5},globalThis):e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-4",children:"사용자를 찾을 수 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:126,columnNumber:11},globalThis),e.jsxDEV(L,{to:"/",children:e.jsxDEV(A,{children:"홈으로 돌아가기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:128,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:127,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:125,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/ProfilePage.tsx",lineNumber:124,columnNumber:7},globalThis)},vo=()=>{const{user:r,isAuthenticated:s}=le(),{execute:o}=he(async h=>h),[a,n]=d.useState(!0),[i,l]=d.useState([]),[c,u]=d.useState([]),[f,g]=d.useState([]),[p,b]=d.useState({totalPosts:0,totalLikes:0,totalViews:0,totalEnrollments:0});d.useEffect(()=>{if(!s||!r)return;(async()=>{var C,_,v;n(!0);try{const N=await o({url:"/posts",method:"GET",params:{user_id:r.id,per_page:5}});N.success&&l(((C=N.data)==null?void 0:C.data)||[]);const m=await o({url:"/my/enrollments",method:"GET",params:{per_page:5}});m.success&&u(((_=m.data)==null?void 0:_.data)||[]);const E=await o({url:"/notifications",method:"GET",params:{per_page:5}});E.success&&g(((v=E.data)==null?void 0:v.data)||[]);const D=await o({url:"/my/stats",method:"GET"});D.success&&b(D.data||p)}catch(N){console.error("마이페이지 데이터 로드 실패:",N)}finally{n(!1)}})()},[s,r]);const w=h=>{const C=new Date(h),v=Math.abs(new Date().getTime()-C.getTime()),N=Math.ceil(v/(1e3*60*60*24));return N===1?"오늘":N===2?"어제":N<=7?`${N-1}일 전`:C.toLocaleDateString("ko-KR")},k=h=>{switch(h){case"POST_COMMENT":return e.jsxDEV("svg",{className:"w-5 h-5 text-blue-500",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:111,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:110,columnNumber:11},globalThis);case"LECTURE_ENROLLMENT":return e.jsxDEV("svg",{className:"w-5 h-5 text-green-500",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 14l9-5-9-5-9 5 9 5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:117,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:116,columnNumber:11},globalThis);case"EVENT_REGISTRATION":return e.jsxDEV("svg",{className:"w-5 h-5 text-purple-500",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h6m-6 0a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2V9a2 2 0 00-2-2"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:123,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:122,columnNumber:11},globalThis);default:return e.jsxDEV("svg",{className:"w-5 h-5 text-gray-500",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:129,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:128,columnNumber:11},globalThis)}};return!s||!r?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h2",{className:"text-2xl font-bold text-gray-900 mb-4",children:"로그인이 필요합니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:139,columnNumber:11},globalThis),e.jsxDEV(L,{to:"/auth/login",children:e.jsxDEV(A,{children:"로그인하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:141,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:140,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:138,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:137,columnNumber:7},globalThis):a?e.jsxDEV("div",{className:"min-h-screen flex items-center justify-center",children:e.jsxDEV(be,{size:"lg",message:"마이페이지를 불러오는 중..."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:151,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:150,columnNumber:7},globalThis):e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:[e.jsxDEV("div",{className:"bg-gradient-to-r from-indigo-600 to-purple-600 text-white",children:e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16",children:e.jsxDEV("div",{className:"text-center",children:[e.jsxDEV("h1",{className:"text-4xl font-bold mb-4",children:["안녕하세요, ",r.nickname,"님! 👋"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:162,columnNumber:13},globalThis),e.jsxDEV("p",{className:"text-xl text-indigo-100",children:"오늘도 탑마케팅과 함께 성장해보세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:165,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:161,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:160,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:159,columnNumber:7},globalThis),e.jsxDEV("div",{className:"max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center",children:e.jsxDEV("svg",{className:"w-6 h-6 text-blue-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:179,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:178,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:177,columnNumber:15},globalThis),e.jsxDEV("div",{className:"ml-4",children:[e.jsxDEV("p",{className:"text-sm text-gray-600",children:"작성한 게시글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:183,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-2xl font-bold text-gray-900",children:p.totalPosts.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:184,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:182,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:176,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:175,columnNumber:11},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center",children:e.jsxDEV("svg",{className:"w-6 h-6 text-green-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:193,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:192,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:191,columnNumber:15},globalThis),e.jsxDEV("div",{className:"ml-4",children:[e.jsxDEV("p",{className:"text-sm text-gray-600",children:"받은 좋아요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:197,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-2xl font-bold text-gray-900",children:p.totalLikes.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:198,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:196,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:190,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:189,columnNumber:11},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center",children:e.jsxDEV("svg",{className:"w-6 h-6 text-purple-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 12a3 3 0 11-6 0 3 3 0 016 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:207,columnNumber:19},globalThis),e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:208,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:206,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:205,columnNumber:15},globalThis),e.jsxDEV("div",{className:"ml-4",children:[e.jsxDEV("p",{className:"text-sm text-gray-600",children:"총 조회수"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:212,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-2xl font-bold text-gray-900",children:p.totalViews.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:213,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:211,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:204,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:203,columnNumber:11},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center",children:e.jsxDEV("svg",{className:"w-6 h-6 text-orange-600",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 14l9-5-9-5-9 5 9 5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:222,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:221,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:220,columnNumber:15},globalThis),e.jsxDEV("div",{className:"ml-4",children:[e.jsxDEV("p",{className:"text-sm text-gray-600",children:"수강 중인 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:226,columnNumber:17},globalThis),e.jsxDEV("p",{className:"text-2xl font-bold text-gray-900",children:c.length.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:227,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:225,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:219,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:218,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:174,columnNumber:9},globalThis),e.jsxDEV("div",{className:"grid grid-cols-1 lg:grid-cols-3 gap-8",children:[e.jsxDEV("div",{className:"lg:col-span-2 space-y-6",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("div",{className:"flex items-center justify-between mb-6",children:[e.jsxDEV("h2",{className:"text-xl font-bold text-gray-900",children:"최근 활동"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:239,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/profile",className:"text-blue-600 hover:text-blue-700 text-sm font-medium",children:"전체 보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:240,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:238,columnNumber:15},globalThis),i.length>0?e.jsxDEV("div",{className:"space-y-4",children:i.slice(0,3).map(h=>e.jsxDEV(L,{to:`/community/${h.id}`,className:"block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all",children:[e.jsxDEV("h3",{className:"font-medium text-gray-900 mb-2 hover:text-blue-600",children:h.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:253,columnNumber:23},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between text-sm text-gray-500",children:[e.jsxDEV("span",{children:w(h.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:257,columnNumber:25},globalThis),e.jsxDEV("div",{className:"flex space-x-3",children:[e.jsxDEV("span",{children:["조회 ",h.views]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:259,columnNumber:27},globalThis),e.jsxDEV("span",{children:["좋아요 ",h.likes_count]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:260,columnNumber:27},globalThis),e.jsxDEV("span",{children:["댓글 ",h.comments_count]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:261,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:258,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:256,columnNumber:23},globalThis)]},h.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:248,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:246,columnNumber:17},globalThis):e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:271,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:270,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:269,columnNumber:19},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"아직 작성한 게시글이 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:274,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-gray-600 mb-4",children:"커뮤니티에 첫 게시글을 작성해보세요!"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:277,columnNumber:19},globalThis),e.jsxDEV(L,{to:"/community/write",children:e.jsxDEV(A,{children:"첫 게시글 작성하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:281,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:280,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:268,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:237,columnNumber:13},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("div",{className:"flex items-center justify-between mb-6",children:[e.jsxDEV("h2",{className:"text-xl font-bold text-gray-900",children:"수강 중인 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:290,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/my/lectures",className:"text-blue-600 hover:text-blue-700 text-sm font-medium",children:"전체 보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:291,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:289,columnNumber:15},globalThis),c.length>0?e.jsxDEV("div",{className:"space-y-4",children:c.slice(0,3).map(h=>e.jsxDEV(L,{to:`/lectures/${h.lecture.id}`,className:"block p-4 border border-gray-200 rounded-lg hover:border-gray-300 hover:shadow-sm transition-all",children:e.jsxDEV("div",{className:"flex items-center space-x-4",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center",children:h.lecture.thumbnail?e.jsxDEV("img",{src:h.lecture.thumbnail,alt:h.lecture.title,className:"w-16 h-16 rounded-lg object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:307,columnNumber:29},globalThis):e.jsxDEV("svg",{className:"w-8 h-8 text-white",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:314,columnNumber:31},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:313,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:305,columnNumber:25},globalThis),e.jsxDEV("div",{className:"flex-1",children:[e.jsxDEV("h3",{className:"font-medium text-gray-900 mb-1 hover:text-blue-600",children:h.lecture.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:319,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-600 mb-2",children:h.lecture.instructor.nickname},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:322,columnNumber:27},globalThis),e.jsxDEV("div",{className:"w-full bg-gray-200 rounded-full h-2",children:e.jsxDEV("div",{className:"bg-blue-600 h-2 rounded-full",style:{width:`${h.progress}%`}},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:326,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:325,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500 mt-1",children:["진행률 ",h.progress,"%"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:331,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:318,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:304,columnNumber:23},globalThis)},h.id,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:299,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:297,columnNumber:17},globalThis):e.jsxDEV("div",{className:"text-center py-8",children:[e.jsxDEV("div",{className:"w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4",children:e.jsxDEV("svg",{className:"w-8 h-8 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 14l9-5-9-5-9 5 9 5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:343,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:342,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:341,columnNumber:19},globalThis),e.jsxDEV("h3",{className:"text-lg font-medium text-gray-900 mb-2",children:"아직 수강 중인 강의가 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:346,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-gray-600 mb-4",children:"다양한 강의를 둘러보고 학습을 시작해보세요!"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:349,columnNumber:19},globalThis),e.jsxDEV(L,{to:"/lectures",children:e.jsxDEV(A,{children:"강의 둘러보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:353,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:352,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:340,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:288,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:235,columnNumber:11},globalThis),e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("h2",{className:"text-xl font-bold text-gray-900 mb-4",children:"빠른 액션"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:364,columnNumber:15},globalThis),e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV(L,{to:"/community/write",children:e.jsxDEV(A,{fullWidth:!0,variant:"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 4v16m8-8H4"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:369,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:368,columnNumber:21},globalThis),children:"새 게시글 작성"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:367,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:366,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/lectures",children:e.jsxDEV(A,{fullWidth:!0,variant:"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:378,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:377,columnNumber:21},globalThis),children:"강의 찾아보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:376,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:375,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/profile/edit",children:e.jsxDEV(A,{fullWidth:!0,variant:"outline",leftIcon:e.jsxDEV("svg",{className:"w-5 h-5",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:387,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:386,columnNumber:21},globalThis),children:"프로필 수정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:385,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:384,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:365,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:363,columnNumber:13},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:[e.jsxDEV("div",{className:"flex items-center justify-between mb-4",children:[e.jsxDEV("h2",{className:"text-xl font-bold text-gray-900",children:"최근 알림"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:399,columnNumber:17},globalThis),e.jsxDEV(L,{to:"/notifications",className:"text-blue-600 hover:text-blue-700 text-sm font-medium",children:"전체 보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:400,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:398,columnNumber:15},globalThis),f.length>0?e.jsxDEV("div",{className:"space-y-3",children:f.slice(0,5).map(h=>e.jsxDEV("div",{className:`p-3 rounded-lg transition-colors ${h.read_at?"bg-gray-50":"bg-blue-50 border-l-4 border-blue-400"}`,children:e.jsxDEV("div",{className:"flex items-start space-x-3",children:[e.jsxDEV("div",{className:"flex-shrink-0",children:k(h.type)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:415,columnNumber:25},globalThis),e.jsxDEV("div",{className:"flex-1 min-w-0",children:[e.jsxDEV("p",{className:"text-sm font-medium text-gray-900",children:h.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:419,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-600 mt-1",children:h.message},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:422,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500 mt-1",children:w(h.created_at)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:425,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:418,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:414,columnNumber:23},globalThis)},h.id,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:408,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:406,columnNumber:17},globalThis):e.jsxDEV("div",{className:"text-center py-6",children:[e.jsxDEV("div",{className:"w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3",children:e.jsxDEV("svg",{className:"w-6 h-6 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 17h5l-5 5v-5z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:437,columnNumber:23},globalThis),e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:438,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:436,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:435,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-500",children:"새로운 알림이 없습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:441,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:434,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:397,columnNumber:13},globalThis),e.jsxDEV("div",{className:"bg-gradient-to-br from-purple-500 to-blue-600 rounded-xl p-6 text-white",children:[e.jsxDEV("h2",{className:"text-xl font-bold mb-3",children:"추천 강의"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:448,columnNumber:15},globalThis),e.jsxDEV("p",{className:"text-purple-100 mb-4",children:"당신에게 맞는 강의를 찾아보세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:449,columnNumber:15},globalThis),e.jsxDEV(L,{to:"/lectures",children:e.jsxDEV(A,{variant:"outline",className:"border-white text-white hover:bg-white hover:text-purple-600",fullWidth:!0,children:"강의 둘러보기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:453,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:452,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:447,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:361,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:233,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:172,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/MyPage.tsx",lineNumber:157,columnNumber:5},globalThis)},wo=()=>{const r=Ne(),{user:s,updateUser:o,isAuthenticated:a}=le(),{success:n,error:i}=fe(),{execute:l}=he(async S=>S),[c,u]=d.useState("profile"),[f,g]=d.useState({nickname:"",email:"",introduction:"",marketing_agreed:!1}),[p,b]=d.useState({}),[w,k]=d.useState(!1),[h,C]=d.useState({current_password:"",new_password:"",new_password_confirmation:""}),[_,v]=d.useState({}),[N,m]=d.useState(!1),[E,D]=d.useState(null),[z,T]=d.useState(""),[P,H]=d.useState(!1);d.useEffect(()=>{if(!a||!s){i("로그인이 필요한 서비스입니다."),r("/auth/login",{state:{from:location.pathname}});return}g({nickname:s.nickname||"",email:s.email||"",introduction:s.introduction||"",marketing_agreed:s.marketing_agreed||!1}),s.profile_image&&T(s.profile_image)},[a,s]);const q=S=>{const{name:U,value:X,type:V}=S.target,O=S.target.checked;g(K=>({...K,[U]:V==="checkbox"?O:X})),p[U]&&b(K=>({...K,[U]:""}))},R=S=>{const{name:U,value:X}=S.target;C(V=>({...V,[U]:X})),_[U]&&v(V=>({...V,[U]:""}))},W=S=>{var V;const U=(V=S.target.files)==null?void 0:V[0];if(!U)return;if(!U.type.startsWith("image/")){i("이미지 파일만 업로드할 수 있습니다.");return}if(U.size>5*1024*1024){i("파일 크기는 5MB를 초과할 수 없습니다.");return}D(U);const X=new FileReader;X.onload=O=>{var K;T((K=O.target)==null?void 0:K.result)},X.readAsDataURL(U)},j=()=>{const S={};return f.nickname.trim()?f.nickname.length<2?S.nickname="닉네임은 2자 이상 입력해주세요.":f.nickname.length>20&&(S.nickname="닉네임은 20자 이하로 입력해주세요."):S.nickname="닉네임을 입력해주세요.",f.email.trim()?/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(f.email)||(S.email="올바른 이메일 주소를 입력해주세요."):S.email="이메일을 입력해주세요.",f.introduction&&f.introduction.length>500&&(S.introduction="자기소개는 500자 이하로 입력해주세요."),b(S),Object.keys(S).length===0},M=()=>{const S={};return h.current_password.trim()||(S.current_password="현재 비밀번호를 입력해주세요."),h.new_password.trim()?h.new_password.length<8?S.new_password="새 비밀번호는 8자 이상 입력해주세요.":/(?=.*[a-zA-Z])(?=.*\d)/.test(h.new_password)||(S.new_password="새 비밀번호는 영문과 숫자를 포함해야 합니다."):S.new_password="새 비밀번호를 입력해주세요.",h.new_password_confirmation.trim()?h.new_password!==h.new_password_confirmation&&(S.new_password_confirmation="새 비밀번호가 일치하지 않습니다."):S.new_password_confirmation="새 비밀번호 확인을 입력해주세요.",v(S),Object.keys(S).length===0},B=async S=>{if(S.preventDefault(),!!j()){k(!0);try{let U="";if(E){H(!0);try{const O=await l({url:"/upload/profile-image",method:"POST",data:{file:E},headers:{"Content-Type":"multipart/form-data"}});O.success&&O.data&&(U=O.data.url)}catch(O){console.error("이미지 업로드 실패:",O)}finally{H(!1)}}const X={nickname:f.nickname.trim(),email:f.email.trim(),introduction:f.introduction.trim(),marketing_agreed:f.marketing_agreed};U&&(X.profile_image=U);const V=await l({url:"/users/profile",method:"PUT",data:X});V.success&&V.data&&(o(V.data),n("프로필이 성공적으로 업데이트되었습니다."))}catch(U){const X=U instanceof Error?U.message:"프로필 업데이트에 실패했습니다.";i(X)}finally{k(!1)}}},J=async S=>{if(S.preventDefault(),!!M()){m(!0);try{const U={current_password:h.current_password,new_password:h.new_password,new_password_confirmation:h.new_password_confirmation};(await l({url:"/auth/password/change",method:"POST",data:U})).success&&(n("비밀번호가 성공적으로 변경되었습니다."),C({current_password:"",new_password:"",new_password_confirmation:""}))}catch(U){const X=U instanceof Error?U.message:"비밀번호 변경에 실패했습니다.";i(X)}finally{m(!1)}}};return e.jsxDEV("div",{className:"min-h-screen bg-gray-50",children:e.jsxDEV("div",{className:"max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8",children:[e.jsxDEV("div",{className:"mb-6",children:e.jsxDEV(L,{to:"/profile",className:"inline-flex items-center text-blue-600 hover:text-blue-700",children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M15 19l-7-7 7-7"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:285,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:284,columnNumber:13},globalThis),"프로필로 돌아가기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:280,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:279,columnNumber:9},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-6",children:[e.jsxDEV("h1",{className:"text-3xl font-bold text-gray-900",children:"프로필 편집"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:293,columnNumber:11},globalThis),e.jsxDEV("p",{className:"text-gray-600 mt-2",children:"프로필 정보를 수정하고 계정 설정을 관리하세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:296,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:292,columnNumber:9},globalThis),e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6 mb-6",children:e.jsxDEV("div",{className:"border-b border-gray-200",children:e.jsxDEV("nav",{className:"-mb-px flex space-x-8",children:[{key:"profile",label:"기본 정보",icon:"user"},{key:"password",label:"비밀번호 변경",icon:"lock-closed"},{key:"settings",label:"계정 설정",icon:"cog"}].map(S=>e.jsxDEV("button",{onClick:()=>u(S.key),className:`flex items-center py-2 px-1 border-b-2 font-medium text-sm ${c===S.key?"border-blue-500 text-blue-600":"border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"}`,children:[e.jsxDEV("svg",{className:"w-5 h-5 mr-2",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:[S.icon==="user"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:321,columnNumber:23},globalThis),S.icon==="lock-closed"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:324,columnNumber:23},globalThis),S.icon==="cog"&&e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:327,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:319,columnNumber:19},globalThis),S.label]},S.key,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:310,columnNumber:17},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:304,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:303,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:302,columnNumber:9},globalThis),e.jsxDEV("div",{children:[c==="profile"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("form",{onSubmit:B,className:"space-y-6",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"block text-sm font-medium text-gray-700 mb-4",children:"프로필 이미지"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:345,columnNumber:19},globalThis),e.jsxDEV("div",{className:"flex items-center space-x-6",children:[e.jsxDEV("div",{className:"w-24 h-24 bg-gray-200 rounded-full flex items-center justify-center overflow-hidden",children:z?e.jsxDEV("img",{src:z,alt:"프로필 미리보기",className:"w-24 h-24 rounded-full object-cover"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:351,columnNumber:25},globalThis):e.jsxDEV("svg",{className:"w-12 h-12 text-gray-400",fill:"none",viewBox:"0 0 24 24",stroke:"currentColor",children:e.jsxDEV("path",{strokeLinecap:"round",strokeLinejoin:"round",strokeWidth:2,d:"M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:358,columnNumber:27},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:357,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:349,columnNumber:21},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("input",{type:"file",id:"profile-image",accept:"image/*",onChange:W,className:"hidden"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:363,columnNumber:23},globalThis),e.jsxDEV("label",{htmlFor:"profile-image",children:e.jsxDEV(A,{type:"button",variant:"outline",loading:P,onClick:()=>{var S;return(S=document.getElementById("profile-image"))==null?void 0:S.click()},children:"이미지 선택"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:371,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:370,columnNumber:23},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500 mt-2",children:"JPG, PNG 파일 (최대 5MB)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:380,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:362,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:348,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:344,columnNumber:17},globalThis),e.jsxDEV(ae,{label:"닉네임",name:"nickname",value:f.nickname,onChange:q,error:p.nickname,placeholder:"사용할 닉네임을 입력하세요",required:!0,fullWidth:!0,maxLength:20,hint:`${f.nickname.length}/20`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:388,columnNumber:17},globalThis),e.jsxDEV(ae,{label:"이메일",type:"email",name:"email",value:f.email,onChange:q,error:p.email,placeholder:"example@email.com",required:!0,fullWidth:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:402,columnNumber:17},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"block text-sm font-medium text-gray-700 mb-2",children:"자기소개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:416,columnNumber:19},globalThis),e.jsxDEV("textarea",{name:"introduction",value:f.introduction,onChange:q,placeholder:"자신을 간단히 소개해주세요",rows:4,maxLength:500,className:`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${p.introduction?"border-red-300":"border-gray-300"}`},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:419,columnNumber:19},globalThis),e.jsxDEV("div",{className:"flex justify-between items-center mt-2",children:[p.introduction&&e.jsxDEV("p",{className:"text-red-600 text-sm",children:p.introduction},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:432,columnNumber:23},globalThis),e.jsxDEV("div",{className:"text-sm text-gray-500 ml-auto",children:[f.introduction.length,"/500"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:434,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:430,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:415,columnNumber:17},globalThis),e.jsxDEV("div",{className:"flex items-center",children:[e.jsxDEV("input",{id:"marketing_agreed",name:"marketing_agreed",type:"checkbox",checked:f.marketing_agreed,onChange:q,className:"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:442,columnNumber:19},globalThis),e.jsxDEV("label",{htmlFor:"marketing_agreed",className:"ml-2 block text-sm text-gray-700",children:"마케팅 정보 수신에 동의합니다 (선택)"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:450,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:441,columnNumber:17},globalThis),e.jsxDEV("div",{className:"pt-6 border-t border-gray-200",children:e.jsxDEV(A,{type:"submit",loading:w,className:"bg-blue-600 hover:bg-blue-700",children:"프로필 저장"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:457,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:456,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:342,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:341,columnNumber:13},globalThis),c==="password"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("form",{onSubmit:J,className:"space-y-6",children:[e.jsxDEV("div",{className:"mb-6",children:[e.jsxDEV("h2",{className:"text-lg font-semibold text-gray-900 mb-2",children:"비밀번호 변경"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:474,columnNumber:19},globalThis),e.jsxDEV("p",{className:"text-sm text-gray-600",children:"계정 보안을 위해 정기적으로 비밀번호를 변경해주세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:477,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:473,columnNumber:17},globalThis),e.jsxDEV(ae,{label:"현재 비밀번호",type:"password",name:"current_password",value:h.current_password,onChange:R,error:_.current_password,placeholder:"현재 비밀번호를 입력하세요",required:!0,fullWidth:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:482,columnNumber:17},globalThis),e.jsxDEV(ae,{label:"새 비밀번호",type:"password",name:"new_password",value:h.new_password,onChange:R,error:_.new_password,placeholder:"새 비밀번호를 입력하세요",required:!0,fullWidth:!0,hint:"영문, 숫자를 포함하여 8자 이상 입력해주세요."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:494,columnNumber:17},globalThis),e.jsxDEV(ae,{label:"새 비밀번호 확인",type:"password",name:"new_password_confirmation",value:h.new_password_confirmation,onChange:R,error:_.new_password_confirmation,placeholder:"새 비밀번호를 다시 입력하세요",required:!0,fullWidth:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:507,columnNumber:17},globalThis),e.jsxDEV("div",{className:"pt-6 border-t border-gray-200",children:e.jsxDEV(A,{type:"submit",loading:N,className:"bg-red-600 hover:bg-red-700",children:"비밀번호 변경"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:520,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:519,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:472,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:471,columnNumber:13},globalThis),c==="settings"&&e.jsxDEV("div",{className:"bg-white rounded-xl shadow-sm p-6",children:e.jsxDEV("div",{className:"space-y-6",children:[e.jsxDEV("div",{children:e.jsxDEV("h2",{className:"text-lg font-semibold text-gray-900 mb-4",children:"계정 설정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:537,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:536,columnNumber:17},globalThis),e.jsxDEV("div",{className:"border-b border-gray-200 pb-6",children:[e.jsxDEV("h3",{className:"text-md font-medium text-gray-900 mb-3",children:"알림 설정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:544,columnNumber:19},globalThis),e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"text-sm font-medium text-gray-700",children:"이메일 알림"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:550,columnNumber:25},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500",children:"새로운 댓글, 좋아요 등의 알림을 이메일로 받습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:553,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:549,columnNumber:23},globalThis),e.jsxDEV("input",{type:"checkbox",className:"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded",defaultChecked:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:557,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:548,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"text-sm font-medium text-gray-700",children:"SMS 알림"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:565,columnNumber:25},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500",children:"중요한 알림을 SMS로 받습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:568,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:564,columnNumber:23},globalThis),e.jsxDEV("input",{type:"checkbox",className:"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:572,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:563,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:547,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:543,columnNumber:17},globalThis),e.jsxDEV("div",{className:"border-b border-gray-200 pb-6",children:[e.jsxDEV("h3",{className:"text-md font-medium text-gray-900 mb-3",children:"개인정보 설정"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:582,columnNumber:19},globalThis),e.jsxDEV("div",{className:"space-y-3",children:[e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"text-sm font-medium text-gray-700",children:"프로필 공개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:588,columnNumber:25},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500",children:"다른 사용자가 내 프로필을 볼 수 있습니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:591,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:587,columnNumber:23},globalThis),e.jsxDEV("input",{type:"checkbox",className:"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded",defaultChecked:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:595,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:586,columnNumber:21},globalThis),e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"text-sm font-medium text-gray-700",children:"이메일 주소 공개"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:603,columnNumber:25},globalThis),e.jsxDEV("p",{className:"text-xs text-gray-500",children:"프로필에서 이메일 주소를 공개합니다"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:606,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:602,columnNumber:23},globalThis),e.jsxDEV("input",{type:"checkbox",className:"h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:610,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:601,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:585,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:581,columnNumber:17},globalThis),e.jsxDEV("div",{children:[e.jsxDEV("h3",{className:"text-md font-medium text-red-900 mb-3",children:"위험한 작업"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:620,columnNumber:19},globalThis),e.jsxDEV("div",{className:"space-y-3",children:e.jsxDEV("div",{className:"p-4 bg-red-50 rounded-lg",children:e.jsxDEV("div",{className:"flex items-center justify-between",children:[e.jsxDEV("div",{children:[e.jsxDEV("label",{className:"text-sm font-medium text-red-900",children:"계정 탈퇴"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:627,columnNumber:27},globalThis),e.jsxDEV("p",{className:"text-xs text-red-700 mt-1",children:"계정을 영구적으로 삭제합니다. 이 작업은 되돌릴 수 없습니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:630,columnNumber:27},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:626,columnNumber:25},globalThis),e.jsxDEV(A,{variant:"outline",className:"text-red-600 border-red-200 hover:bg-red-50",onClick:()=>{window.confirm("정말로 계정을 탈퇴하시겠습니까? 이 작업은 되돌릴 수 없습니다.")&&i("계정 탈퇴 기능은 준비 중입니다.")},children:"계정 탈퇴"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:634,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:625,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:624,columnNumber:21},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:623,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:619,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:535,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:534,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:338,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:277,columnNumber:7},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/profile/EditProfilePage.tsx",lineNumber:276,columnNumber:5},globalThis)},ko=()=>{var z;const[r]=d.useState(null),[s]=d.useState({post_count:0,comment_count:0,like_count:0,join_days:0}),[o]=d.useState([]),[a]=d.useState([]),[n]=d.useState(!1),[i,l]=d.useState(!1),[c,u]=d.useState("");d.useEffect(()=>{f()},[]);const f=async()=>{},g=()=>r!=null&&r.profile_image_profile?r.profile_image_profile:r!=null&&r.profile_image_thumb?r.profile_image_thumb:"/assets/images/default-avatar.png",p=()=>{if(!(r!=null&&r.birth_date))return null;const T=new Date(r.birth_date),P=new Date,H=P.getFullYear()-T.getFullYear(),q=P.getMonth()-T.getMonth();return q<0||q===0&&P.getDate()<T.getDate()?H-1:H},b=()=>{if(!(r!=null&&r.created_at))return"알 수 없음";const T=new Date(r.created_at);return`${T.getFullYear()}년 ${T.getMonth()+1}월 ${T.getDate()}일`},w=()=>{if(!(r!=null&&r.last_login))return"정보 없음";const T=new Date(r.last_login).getTime(),P=Date.now()-T;return P<6e4?"방금 전":P<36e5?`${Math.floor(P/6e4)}분 전`:P<864e5?`${Math.floor(P/36e5)}시간 전`:P<2592e6?`${Math.floor(P/864e5)}일 전`:new Date(r.last_login).toLocaleDateString()},k=T=>{switch(T){case"M":return"남성";case"F":return"여성";case"OTHER":return"기타";default:return"알 수 없음"}},h=T=>{u(T),l(!0),document.body.style.overflow="hidden"},C=()=>{l(!1),u(""),document.body.style.overflow=""},_=()=>{const T=`${r==null?void 0:r.nickname}님의 프로필 - 탑마케팅`,P=`https://${window.location.host}/profile/${encodeURIComponent((r==null?void 0:r.nickname)||"")}`,H=`탑마케팅에서 ${r==null?void 0:r.nickname}님의 프로필을 확인해보세요!`;navigator.share?navigator.share({title:T,text:H,url:P}).catch(()=>{v(T,P)}):v(T,P)},v=(T,P)=>{navigator.clipboard?navigator.clipboard.writeText(P).then(()=>{alert(`🔗 링크가 클립보드에 복사되었습니다!
다른 곳에 붙여넣기하여 공유하세요.`)}).catch(()=>{N(T,P)}):N(T,P)},N=(T,P)=>{alert(`공유 링크: ${P}`)},m={website:{icon:"fas fa-globe",name:"웹사이트",color:"#6366f1"},kakao:{icon:"fas fa-comment",name:"카카오톡",color:"#FEE500"},instagram:{icon:"fab fa-instagram",name:"인스타그램",color:"#E4405F"},facebook:{icon:"fab fa-facebook",name:"페이스북",color:"#1877F2"},youtube:{icon:"fab fa-youtube",name:"유튜브",color:"#FF0000"},tiktok:{icon:"fab fa-tiktok",name:"틱톡",color:"#000000"}},E=["website","kakao","instagram","facebook","youtube","tiktok"],D=p();return e.jsxDEV(dr,{children:[e.jsxDEV("div",{className:"profile-container",children:[e.jsxDEV("div",{className:"profile-header-section",children:e.jsxDEV("div",{className:"profile-main-info",children:[e.jsxDEV("div",{className:"profile-image-container",children:r!=null&&r.profile_image_profile?e.jsxDEV("img",{src:g(),alt:`${r.nickname}님의 프로필 이미지`,className:"profile-image",onClick:()=>h(r.profile_image_original||g())},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:186,columnNumber:17},globalThis):e.jsxDEV("div",{className:"profile-image-fallback",children:((z=r==null?void 0:r.nickname)==null?void 0:z.charAt(0))||"?"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:193,columnNumber:17},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:184,columnNumber:13},globalThis),e.jsxDEV("div",{className:"profile-details",children:[e.jsxDEV("h1",{className:"profile-name",children:(r==null?void 0:r.nickname)||"사용자"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:200,columnNumber:15},globalThis),e.jsxDEV("div",{className:"profile-meta",children:[e.jsxDEV("span",{children:["🗓️ 가입일: ",b()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:202,columnNumber:17},globalThis),s.join_days>0&&e.jsxDEV("span",{children:["⏰ 활동 ",s.join_days,"일째"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:204,columnNumber:19},globalThis),!n&&e.jsxDEV("span",{children:["👀 최근 접속: ",w()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:207,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:201,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:199,columnNumber:13},globalThis),e.jsxDEV("div",{className:"profile-actions",children:[n&&e.jsxDEV("a",{href:"/profile/edit",className:"btn btn-secondary",children:"✏️ 프로필 편집"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:214,columnNumber:17},globalThis),e.jsxDEV("button",{className:"btn btn-secondary",onClick:_,children:"🔗 공유하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:219,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:212,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:183,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:182,columnNumber:9},globalThis),e.jsxDEV("div",{className:"profile-content",children:[e.jsxDEV("div",{className:"profile-main",children:[e.jsxDEV("div",{className:"profile-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-user"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:233,columnNumber:17},globalThis)," 자기소개"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:232,columnNumber:15},globalThis),r!=null&&r.bio?e.jsxDEV("div",{className:"bio-content",children:r.bio},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:236,columnNumber:17},globalThis):e.jsxDEV("div",{className:"bio-empty",children:n?"자기소개를 작성해보세요!":"아직 자기소개가 없습니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:238,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:231,columnNumber:13},globalThis),e.jsxDEV("div",{className:"profile-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-newspaper"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:247,columnNumber:17},globalThis)," 최근 게시글"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:246,columnNumber:15},globalThis),o.length>0?e.jsxDEV("ul",{className:"activity-list",children:o.map(T=>e.jsxDEV("li",{className:"activity-item",children:[e.jsxDEV("div",{className:"activity-title",children:e.jsxDEV("a",{href:`/community/posts/${T.id}`,children:T.title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:254,columnNumber:25},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:253,columnNumber:23},globalThis),e.jsxDEV("div",{className:"activity-meta",children:[e.jsxDEV("span",{children:["📅 ",new Date(T.created_at).toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:259,columnNumber:25},globalThis),e.jsxDEV("span",{children:["👁️ ",T.view_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:260,columnNumber:25},globalThis),e.jsxDEV("span",{children:["💬 ",T.comment_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:261,columnNumber:25},globalThis),e.jsxDEV("span",{children:["❤️ ",T.like_count.toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:262,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:258,columnNumber:23},globalThis)]},T.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:252,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:250,columnNumber:17},globalThis):e.jsxDEV("div",{className:"activity-empty",children:n?"아직 작성한 게시글이 없습니다. 첫 번째 글을 작성해보세요!":"아직 작성한 게시글이 없습니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:268,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:245,columnNumber:13},globalThis),e.jsxDEV("div",{className:"profile-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-comments"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:277,columnNumber:17},globalThis)," 최근 댓글"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:276,columnNumber:15},globalThis),a.length>0?e.jsxDEV("ul",{className:"activity-list",children:a.map(T=>e.jsxDEV("li",{className:"activity-item",children:[e.jsxDEV("div",{className:"activity-title",children:[e.jsxDEV("a",{href:`/community/posts/${T.post_id}#comment-${T.id}`,children:T.post_title},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:284,columnNumber:25},globalThis),"에 댓글"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:283,columnNumber:23},globalThis),e.jsxDEV("div",{className:"activity-meta",children:[e.jsxDEV("span",{children:["📅 ",new Date(T.created_at).toLocaleString()]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:289,columnNumber:25},globalThis),e.jsxDEV("span",{children:["💬 ",T.content.length>50?T.content.substring(0,50)+"...":T.content]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:290,columnNumber:25},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:288,columnNumber:23},globalThis)]},T.id,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:282,columnNumber:21},globalThis))},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:280,columnNumber:17},globalThis):e.jsxDEV("div",{className:"activity-empty",children:"아직 작성한 댓글이 없습니다."},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:296,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:275,columnNumber:13},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:229,columnNumber:11},globalThis),e.jsxDEV("div",{className:"profile-sidebar",children:[e.jsxDEV("div",{className:"profile-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-chart-bar"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:308,columnNumber:17},globalThis)," 활동 통계"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:307,columnNumber:15},globalThis),e.jsxDEV("div",{className:"stats-grid",children:[e.jsxDEV("div",{className:"stat-item",children:[e.jsxDEV("span",{className:"stat-value",children:s.post_count.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:312,columnNumber:19},globalThis),e.jsxDEV("span",{className:"stat-label",children:"게시글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:313,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:311,columnNumber:17},globalThis),e.jsxDEV("div",{className:"stat-item",children:[e.jsxDEV("span",{className:"stat-value",children:s.comment_count.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:316,columnNumber:19},globalThis),e.jsxDEV("span",{className:"stat-label",children:"댓글"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:317,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:315,columnNumber:17},globalThis),e.jsxDEV("div",{className:"stat-item",children:[e.jsxDEV("span",{className:"stat-value",children:s.like_count.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:320,columnNumber:19},globalThis),e.jsxDEV("span",{className:"stat-label",children:"좋아요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:321,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:319,columnNumber:17},globalThis),e.jsxDEV("div",{className:"stat-item",children:[e.jsxDEV("span",{className:"stat-value",children:s.join_days.toLocaleString()},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:324,columnNumber:19},globalThis),e.jsxDEV("span",{className:"stat-label",children:"활동일"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:325,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:323,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:310,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:306,columnNumber:13},globalThis),e.jsxDEV("div",{className:"profile-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-info-circle"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:333,columnNumber:17},globalThis)," 기본 정보"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:332,columnNumber:15},globalThis),e.jsxDEV("ul",{className:"info-list",children:[(r==null?void 0:r.email)&&e.jsxDEV("li",{className:"info-item",children:[e.jsxDEV("i",{className:"info-icon fas fa-envelope"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:338,columnNumber:21},globalThis),e.jsxDEV("div",{className:"info-content",children:[e.jsxDEV("div",{className:"info-label",children:"이메일"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:340,columnNumber:23},globalThis),e.jsxDEV("div",{className:"info-value",children:r.email},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:341,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:339,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:337,columnNumber:19},globalThis),(r==null?void 0:r.birth_date)&&D!==null&&e.jsxDEV("li",{className:"info-item",children:[e.jsxDEV("i",{className:"info-icon fas fa-birthday-cake"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:348,columnNumber:21},globalThis),e.jsxDEV("div",{className:"info-content",children:[e.jsxDEV("div",{className:"info-label",children:"나이"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:350,columnNumber:23},globalThis),e.jsxDEV("div",{className:"info-value",children:[D,"세"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:351,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:349,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:347,columnNumber:19},globalThis),(r==null?void 0:r.gender)&&e.jsxDEV("li",{className:"info-item",children:[e.jsxDEV("i",{className:"info-icon fas fa-venus-mars"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:358,columnNumber:21},globalThis),e.jsxDEV("div",{className:"info-content",children:[e.jsxDEV("div",{className:"info-label",children:"성별"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:360,columnNumber:23},globalThis),e.jsxDEV("div",{className:"info-value",children:k(r.gender)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:361,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:359,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:357,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:335,columnNumber:15},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:331,columnNumber:13},globalThis),r!=null&&r.social_links&&Object.keys(r.social_links).length>0?e.jsxDEV("div",{className:"profile-card social-connections-card",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-globe-americas"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:372,columnNumber:19},globalThis)," 소셜 & 웹사이트"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:371,columnNumber:17},globalThis),e.jsxDEV("div",{className:"social-section",children:e.jsxDEV("div",{className:"social-grid",children:E.map(T=>{var q;const P=(q=r.social_links)==null?void 0:q[T];if(!P||!m[T])return null;const H=m[T];return e.jsxDEV("a",{href:P,target:"_blank",rel:"noopener noreferrer",className:"social-connection-item",style:{"--social-color":H.color},title:`${H.name}에서 만나요`,children:[e.jsxDEV("div",{className:"social-connection-icon",children:e.jsxDEV("i",{className:H.icon},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:394,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:393,columnNumber:27},globalThis),e.jsxDEV("div",{className:"social-connection-content",children:[e.jsxDEV("div",{className:"social-connection-name",children:H.name},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:397,columnNumber:29},globalThis),e.jsxDEV("div",{className:"social-connection-action",children:"방문하기"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:398,columnNumber:29},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:396,columnNumber:27},globalThis),e.jsxDEV("div",{className:"social-connection-arrow",children:e.jsxDEV("i",{className:"fas fa-chevron-right"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:401,columnNumber:29},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:400,columnNumber:27},globalThis)]},T,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:384,columnNumber:25},globalThis)})},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:376,columnNumber:19},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:375,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:370,columnNumber:15},globalThis):n&&e.jsxDEV("div",{className:"profile-card social-connections-card empty-social",children:[e.jsxDEV("h2",{className:"card-title",children:[e.jsxDEV("i",{className:"fas fa-globe-americas"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:413,columnNumber:21},globalThis)," 소셜 & 웹사이트"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:412,columnNumber:19},globalThis),e.jsxDEV("div",{className:"empty-social-content",children:[e.jsxDEV("div",{className:"empty-social-icon",children:e.jsxDEV("i",{className:"fas fa-share-alt"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:417,columnNumber:23},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:416,columnNumber:21},globalThis),e.jsxDEV("div",{className:"empty-social-text",children:[e.jsxDEV("h3",{children:"소셜 프로필을 연결해보세요"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:420,columnNumber:23},globalThis),e.jsxDEV("p",{children:["인스타그램, 유튜브, 개인 웹사이트 등을",e.jsxDEV("br",{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:421,columnNumber:48},globalThis),"프로필에 추가하여 더 많은 사람들과 소통하세요."]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:421,columnNumber:23},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:419,columnNumber:21},globalThis),e.jsxDEV("a",{href:"/profile/edit",className:"btn-add-social",children:[e.jsxDEV("i",{className:"fas fa-plus"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:424,columnNumber:23},globalThis)," 소셜 링크 추가하기"]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:423,columnNumber:21},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:415,columnNumber:19},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:411,columnNumber:17},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:304,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:227,columnNumber:9},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:180,columnNumber:7},globalThis),i&&e.jsxDEV("div",{className:"image-modal",onClick:C,children:[e.jsxDEV("span",{className:"modal-close",onClick:C,children:"×"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:437,columnNumber:11},globalThis),e.jsxDEV("div",{className:"image-modal-content",children:e.jsxDEV("img",{src:c,alt:"프로필 이미지"},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:439,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:438,columnNumber:11},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:436,columnNumber:9},globalThis),e.jsxDEV("style",{children:`
        /* 프로필 페이지 전용 스타일 */
        .profile-container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 20px;
          min-height: calc(100vh - 200px);
        }

        .profile-header-section {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          border-radius: 16px;
          padding: 40px;
          margin-top: 60px;
          margin-bottom: 30px;
          position: relative;
          overflow: hidden;
        }

        .profile-main-info {
          display: flex;
          align-items: center;
          gap: 30px;
          position: relative;
          z-index: 2;
        }

        .profile-image-container {
          position: relative;
          flex-shrink: 0;
        }

        .profile-image {
          width: 120px;
          height: 120px;
          border-radius: 50%;
          border: 4px solid rgba(255, 255, 255, 0.3);
          object-fit: cover;
          cursor: pointer;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-image:hover {
          transform: scale(1.05);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .profile-image-fallback {
          width: 120px;
          height: 120px;
          border-radius: 50%;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          border: 4px solid rgba(255, 255, 255, 0.3);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 3rem;
          font-weight: bold;
          color: white;
          cursor: pointer;
          transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-details {
          flex: 1;
        }

        .profile-name {
          font-size: 2.5rem;
          font-weight: 700;
          margin-bottom: 10px;
          text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .profile-meta {
          display: flex;
          gap: 20px;
          font-size: 0.9rem;
          opacity: 0.8;
        }

        .profile-actions {
          text-align: right;
          position: relative;
        }

        .btn {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 12px 24px;
          border: none;
          border-radius: 8px;
          font-size: 14px;
          font-weight: 600;
          text-decoration: none;
          cursor: pointer;
          transition: all 0.3s ease;
          margin-left: 10px;
        }

        .btn-secondary {
          background: rgba(255, 255, 255, 0.2);
          color: white;
          border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .btn-secondary:hover {
          background: rgba(255, 255, 255, 0.3);
        }

        /* 콘텐츠 그리드 */
        .profile-content {
          display: grid;
          grid-template-columns: 1fr 320px;
          gap: 30px;
        }

        .profile-main {
          display: flex;
          flex-direction: column;
          gap: 25px;
        }

        .profile-sidebar {
          display: flex;
          flex-direction: column;
          gap: 25px;
        }

        /* 카드 공통 스타일 */
        .profile-card {
          background: white;
          border-radius: 16px;
          padding: 25px;
          box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
          border: 1px solid #e2e8f0;
        }

        .card-title {
          font-size: 1.2rem;
          font-weight: 600;
          color: #2d3748;
          margin-bottom: 20px;
          display: flex;
          align-items: center;
          gap: 8px;
        }

        /* 자기소개 카드 */
        .bio-content {
          color: #4a5568;
          line-height: 1.6;
          white-space: pre-wrap;
        }

        .bio-empty {
          color: #a0aec0;
          font-style: italic;
          text-align: center;
          padding: 20px;
        }

        /* 통계 카드 */
        .stats-grid {
          display: grid;
          grid-template-columns: repeat(2, 1fr);
          gap: 15px;
        }

        .stat-item {
          text-align: center;
          padding: 12px 8px;
          background: #f8fafc;
          border-radius: 12px;
          border: 1px solid #e2e8f0;
        }

        .stat-value {
          display: block;
          font-size: 1.2rem;
          font-weight: 700;
          color: #667eea;
          margin-bottom: 4px;
        }

        .stat-label {
          font-size: 0.8rem;
          color: #718096;
          font-weight: 500;
        }

        /* 기본 정보 카드 */
        .info-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .info-item {
          display: flex;
          align-items: center;
          padding: 12px 0;
          border-bottom: 1px solid #e2e8f0;
        }

        .info-item:last-child {
          border-bottom: none;
        }

        .info-icon {
          width: 20px;
          text-align: center;
          color: #667eea;
          margin-right: 12px;
        }

        .info-content {
          flex: 1;
        }

        .info-label {
          font-size: 0.85rem;
          color: #718096;
          margin-bottom: 2px;
        }

        .info-value {
          font-size: 0.95rem;
          color: #2d3748;
          font-weight: 500;
        }

        /* 소셜 연결 카드 */
        .social-section {
          margin-bottom: 20px;
        }

        .social-grid {
          display: grid;
          gap: 12px;
        }

        .social-connection-item {
          display: flex;
          align-items: center;
          gap: 15px;
          padding: 16px;
          background: white;
          border: 2px solid #f1f5f9;
          border-radius: 16px;
          text-decoration: none !important;
          color: #374151;
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          position: relative;
          overflow: hidden;
        }

        .social-connection-item:hover {
          transform: translateY(-4px);
          box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
          border-color: var(--social-color);
        }

        .social-connection-icon {
          width: 48px;
          height: 48px;
          border-radius: 12px;
          background: #f8fafc;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 20px;
          color: #6b7280;
          transition: all 0.3s ease;
        }

        .social-connection-item:hover .social-connection-icon {
          background: var(--social-color);
          color: white;
          transform: scale(1.1);
        }

        .social-connection-content {
          flex: 1;
        }

        .social-connection-name {
          font-weight: 600;
          font-size: 1rem;
          margin-bottom: 2px;
          transition: color 0.3s ease;
        }

        .social-connection-item:hover .social-connection-name {
          color: var(--social-color);
        }

        .social-connection-action {
          font-size: 0.85rem;
          color: #9ca3af;
          font-weight: 500;
        }

        .social-connection-arrow {
          color: #d1d5db;
          font-size: 14px;
          transition: all 0.3s ease;
        }

        .social-connection-item:hover .social-connection-arrow {
          transform: translateX(4px);
          color: var(--social-color);
        }

        /* 빈 소셜 상태 */
        .empty-social .empty-social-content {
          text-align: center;
          padding: 40px 20px;
        }

        .empty-social-icon {
          width: 80px;
          height: 80px;
          border-radius: 50%;
          background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 32px;
          color: #9ca3af;
          margin: 0 auto 20px;
        }

        .empty-social-text h3 {
          font-size: 1.25rem;
          font-weight: 600;
          color: #374151;
          margin-bottom: 12px;
        }

        .empty-social-text p {
          color: #6b7280;
          line-height: 1.6;
          margin-bottom: 24px;
        }

        .btn-add-social {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 12px 24px;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          border-radius: 8px;
          text-decoration: none;
          font-weight: 600;
          font-size: 14px;
          transition: all 0.3s ease;
          box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .btn-add-social:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* 최근 활동 */
        .activity-list {
          list-style: none;
          padding: 0;
          margin: 0;
        }

        .activity-item {
          padding: 15px 0;
          border-bottom: 1px solid #e2e8f0;
        }

        .activity-item:last-child {
          border-bottom: none;
        }

        .activity-title {
          font-size: 0.95rem;
          font-weight: 500;
          color: #2d3748;
          margin-bottom: 5px;
          line-height: 1.4;
        }

        .activity-title a {
          color: #667eea;
          text-decoration: none;
        }

        .activity-title a:hover {
          text-decoration: underline;
        }

        .activity-meta {
          font-size: 0.8rem;
          color: #a0aec0;
          display: flex;
          gap: 15px;
        }

        .activity-empty {
          text-align: center;
          color: #a0aec0;
          font-style: italic;
          padding: 20px;
        }

        /* 이미지 모달 */
        .image-modal {
          display: flex;
          position: fixed;
          z-index: 10000;
          left: 0;
          top: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.9);
          backdrop-filter: blur(5px);
          justify-content: center;
          align-items: center;
        }

        .image-modal-content {
          position: relative;
          max-width: 90%;
          max-height: 90%;
        }

        .image-modal img {
          width: 100%;
          height: auto;
          border-radius: 8px;
          box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        }

        .modal-close {
          position: absolute;
          top: 20px;
          right: 30px;
          color: white;
          font-size: 40px;
          font-weight: bold;
          cursor: pointer;
          z-index: 10001;
        }

        .modal-close:hover {
          opacity: 0.7;
        }

        /* 반응형 디자인 */
        @media (max-width: 768px) {
          .profile-container {
            padding: 15px;
          }
          
          .profile-header-section {
            padding: 25px 20px;
            margin-bottom: 20px;
          }
          
          .profile-main-info {
            flex-direction: column;
            text-align: center;
            gap: 20px;
          }
          
          .profile-name {
            font-size: 2rem;
          }
          
          .profile-meta {
            justify-content: center;
            flex-wrap: wrap;
          }
          
          .profile-content {
            grid-template-columns: 1fr;
            gap: 20px;
          }
          
          .profile-sidebar {
            order: -1;
          }
          
          .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
          }
          
          .stat-item {
            padding: 8px 4px;
          }
          
          .stat-value {
            font-size: 1.2rem;
          }
        }

        @media (max-width: 480px) {
          .profile-image,
          .profile-image-fallback {
            width: 100px;
            height: 100px;
          }
          
          .profile-image-fallback {
            font-size: 2.5rem;
          }
          
          .profile-card {
            padding: 20px 15px;
          }
        }
      `},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:444,columnNumber:7},globalThis)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/pages/user/UserProfilePage.tsx",lineNumber:179,columnNumber:5},globalThis)};function Po(){return e.jsxDEV(e.Fragment,{children:[e.jsxDEV(ws,{children:[e.jsxDEV(ee,{path:"/",element:e.jsxDEV(dr,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:27,columnNumber:34},this),children:[e.jsxDEV(ee,{index:!0,element:e.jsxDEV(io,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:29,columnNumber:33},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:29,columnNumber:11},this),e.jsxDEV(ee,{path:"login",element:e.jsxDEV(co,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:30,columnNumber:40},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:30,columnNumber:11},this),e.jsxDEV(ee,{path:"signup",element:e.jsxDEV(mo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:31,columnNumber:41},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:31,columnNumber:11},this),e.jsxDEV(ee,{path:"forgot-password",element:e.jsxDEV(uo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:32,columnNumber:50},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:32,columnNumber:11},this),e.jsxDEV(ee,{path:"lectures",element:e.jsxDEV(fo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:35,columnNumber:43},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:35,columnNumber:11},this),e.jsxDEV(ee,{path:"lectures/:id",element:e.jsxDEV(go,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:36,columnNumber:47},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:36,columnNumber:11},this),e.jsxDEV(ee,{path:"community",element:e.jsxDEV(po,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:37,columnNumber:44},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:37,columnNumber:11},this),e.jsxDEV(ee,{path:"community/post/:id",element:e.jsxDEV(bo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:38,columnNumber:53},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:38,columnNumber:11},this),e.jsxDEV(ee,{path:"events",element:e.jsxDEV(ho,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:39,columnNumber:41},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:39,columnNumber:11},this),e.jsxDEV(ee,{path:"profile/:id",element:e.jsxDEV(xo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:40,columnNumber:46},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:40,columnNumber:11},this),e.jsxDEV(ee,{path:"user/:nickname",element:e.jsxDEV(ko,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:41,columnNumber:49},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:41,columnNumber:11},this),e.jsxDEV(ee,{path:"community/write",element:e.jsxDEV(Qe,{children:e.jsxDEV(No,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:48,columnNumber:17},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:47,columnNumber:15},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:44,columnNumber:11},this),e.jsxDEV(ee,{path:"my",element:e.jsxDEV(Qe,{children:e.jsxDEV(vo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:54,columnNumber:15},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:53,columnNumber:13},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:52,columnNumber:11},this),e.jsxDEV(ee,{path:"profile/edit",element:e.jsxDEV(Qe,{children:e.jsxDEV(wo,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:59,columnNumber:15},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:58,columnNumber:13},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:57,columnNumber:11},this)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:27,columnNumber:9},this),e.jsxDEV(ee,{path:"*",element:e.jsxDEV(Sr,{to:"/",replace:!0},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:65,columnNumber:34},this)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:65,columnNumber:9},this)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:25,columnNumber:7},this),e.jsxDEV(Ir,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:68,columnNumber:7},this)]},void 0,!0,{fileName:"/workspace/frontend/src/frontend/src/App.tsx",lineNumber:24,columnNumber:5},this)}sr.createRoot(document.getElementById("root")).render(e.jsxDEV(ue.StrictMode,{children:e.jsxDEV(Fr,{children:e.jsxDEV(ks,{basename:"/frontend",future:{v7_startTransition:!0,v7_relativeSplatPath:!0},children:e.jsxDEV(lo,{children:e.jsxDEV(no,{children:e.jsxDEV(oo,{children:e.jsxDEV(Po,{},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:39,columnNumber:15},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:38,columnNumber:13},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:37,columnNumber:11},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:36,columnNumber:9},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:29,columnNumber:7},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:28,columnNumber:5},globalThis)},void 0,!1,{fileName:"/workspace/frontend/src/frontend/src/main.tsx",lineNumber:27,columnNumber:3},globalThis));
