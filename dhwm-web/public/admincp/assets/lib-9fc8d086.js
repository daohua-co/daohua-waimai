var S=Object.defineProperty;var q=(t,s,e)=>s in t?S(t,s,{enumerable:!0,configurable:!0,writable:!0,value:e}):t[s]=e;var n=(t,s,e)=>(q(t,typeof s!="symbol"?s+"":s,e),e);import{ay as w,A as b,Z as u,e as C,ax as j,a6 as R,az as B,ae as N,aA as $}from"./vendor-8025917f.js";import{E as O,a as E,b as P}from"./vendor.ep-ac3077ed.js";const I=(t,s="info")=>{E({type:s,message:t,customClass:"app-message",dangerouslyUseHTMLString:!0,offset:100,duration:4e3})},D=t=>{I(t,"success")},_=(t,s="提示信息",e={})=>{O.alert(t,s,e)},g=(t,s="错误提示")=>{_(t,s,{type:"error"})},k=t=>{E({message:t,customClass:"app-toast",center:!0,offset:100,duration:3e3})},p=t=>{const s=t.errors;let e=t.message;if(s&&s.constructor.name==="Object"){e="<ul>";for(const i in s)for(const a in s[i])e+="<li>"+s[i][a]+"</li>";e+="</ul>"}if(e||(t.success?e="操作成功":e="开小差了"),t.success)return D(e);g(e,"错误提示")},v=t=>{if(!t.success)return p(t);D("删除成功")},F=(t,s)=>O.confirm(t,{cancelButtonText:s,confirmButtonText:"返回列表",type:"success"}),A=({title:t,tip:s,inputPattern:e,inputErrorMessage:i})=>O.prompt(s,t,{confirmButtonText:"确定",cancelButtonText:"取消",inputPattern:e,inputErrorMessage:i}),Y={show:I,toast:k,success:D,error:g,alert:_,responseMessage:p,deleteMessage:v,confirmSaveSuccess:F,prompt:A},d={empty(t){if(w(t)){Object.keys(t).forEach(s=>{delete t[s]});return}if(b(t)){t.value=null;return}t={}},cloneDeep(t,s){if(t==null)throw new Error("target 不能为 null 或 undefined");return b(t)?(t.value=JSON.parse(JSON.stringify(s)),t):w(t)?(this.empty(t),Object.assign(t,JSON.parse(JSON.stringify(s??{}))),t):(t=JSON.parse(JSON.stringify(s)),t)}},L=(t,s)=>{s.forEach(e=>{t[e]=t[e].bind(t)})},U=t=>{window.location.hash="#"+t},y={_isLoading:null,show:function(t={}){this._isLoading=P.service(t)},hide:function(){this._isLoading&&this._isLoading.close()}},Q=()=>decodeURIComponent(window.document.cookie.replace(/.*?XSRF-TOKEN=(.*?)(;|$).*/,"$1"));class tt{constructor(s,e,i=null){n(this,"item",u({}));n(this,"is",u({edit:!1,show:!1,submitting:!1,loading:!1,showValidateError:!0}));n(this,"_stashForCreate",null);n(this,"_stashForEdit",null);n(this,"api",null);n(this,"listBindings",null);n(this,"formRef",null);L(this,["create","edit","onHide","haltValidateCall","save","reset","initStashForCreate","initFormPage"]),this.api=s.api,this.listBindings=s,this.formRef=e,i?d.cloneDeep(this.item,i):i=d.cloneDeep({},this.item),this.initStashForCreate(i)}create(){this.is.edit=!1,this.is.show=!0}edit(s,e){return this.is.edit=!0,this.is.show=!0,this.is.loading=!0,this.api.fetchItem(s,e).then(i=>(Object.assign(this.item,i.item),this._stashForEdit=i.item,i)).finally(()=>{this.is.loading=!1})}onHide(){this.haltValidateCall(()=>{if(this.is.edit){this._stashForEdit=null,this._stashForCreate.restore();return}this._stashForCreate.store()})}haltValidateCall(s){this.is.showValidateError=!1,s.call(),setTimeout(()=>{var e,i;(i=(e=this.formRef)==null?void 0:e.value)==null||i.clearValidate(),this.is.showValidateError=!0},200)}save(){return this.formRef.value.validate().then(async()=>{this.is.submitting=!0;const s=this.is.edit?"put":"post";return await this.api[s](this.item).then(e=>{const i=`${this.is.edit?"编辑":"新建"}成功`,a=this.is.edit?"继续编辑":"继续新建";return F(i,a).then(()=>{this.is.show=!1}).catch(()=>{console.log("catch...")}),this.is.edit?this._stashForEdit={...this.item}:this.haltValidateCall(()=>this._stashForCreate.reset()),this.listBindings?e.items?(this.listBindings.listData.items=e.items,e):(this.listBindings.reload(),e):e}).finally(()=>{this.is.submitting=!1})})}reset(){this.haltValidateCall(()=>{this.is.edit?d.cloneDeep(this.item,this._stashForEdit):this._stashForCreate.reset()})}initStashForCreate(s){let e=null;this._stashForCreate={reset:()=>{d.cloneDeep(this.item,s),e=null},restore:()=>{d.cloneDeep(this.item,e||s)},store:()=>{Object.keys(this.item).length!==0&&(e={...this.item})}}}initFormPage(s){const e=window.location.hash.replace(/#(\/[\w-/]+?)\/(create|(\d+\/edit)).*/,"$1");C(()=>this.is.show,i=>{i||U(e)}),j(()=>{s.id>0?this.edit(s.id):this.create()}),R(()=>this.onHide())}}class J{constructor(s){this.path=s}fetchItems(s={}){return h.get(this.path,s)}fetchItem(s,e={}){return h.get(`${this.path}/${s}`,e)}delete(s,e={}){return h.delete(`${this.path}/${s}`,e)}patch(s,e){return h.patch(`${this.path}/${s}`,e)}post(s){return h.post(this.path,s)}put(s){return h.put(`${this.path}/${s.id}`,s)}batOperate(s,e){return h[s==="delete"?"delete":"patch"](`${this.path}/bat/${s}`,e)}}class st{constructor(s){n(this,"api",null);n(this,"listData",u({items:[],path:"",total:0,per_page:15,current_page:0,query:{},batOperateIds:[],isLoading:!1,isLoaded:!1}));this.api=new J(s),L(this,["load","reload","search","selectChange","batOperate","destroy"]),C(()=>this.listData.items,()=>{this.listData.isLoaded=!0})}load(s={},e={}){const i=Object.assign({},this.listData.query,s);return this.listData.isLoading=!0,this.api.fetchItems(i).then(a=>{var r,c;if((r=a.listData)!=null&&r.items){if(a.listData.items.length===0&&((c=this.listData.query)!=null&&c.page)&&parseInt(this.listData.query.page)>1)return--this.listData.query.page,this.load(e,s);Object.assign(this.listData,a.listData)}a.items&&(this.listData.items=a.items);for(const o in e){if(typeof a[o]>"u")continue;const l=e[o];if(b(l)){l.value=a[o];continue}if(B(l)){Object.assign(l,a[o]);continue}e[o]=a[o]}return a}).finally(()=>{this.listData.isLoading=!1,this.listData.isLoaded=!0})}reload(s=0){return s||(s=this.listData.current_page),s>1?this.listData.query.page=s:delete this.listData.query.page,this.load()}checkLoaded(){this.listData.isLoaded||this.load()}search(){this.reload(1)}selectChange(s){const e=[];for(const i of s)e.push(i.id);this.listData.batOperateIds=e}batOperate(s="delete"){return y.show(),this.api.batOperate(s,{ids:this.listData.batOperateIds}).then(e=>{p(e),this.listData.batOperateIds=[],this.reload()})}destroy(s){return y.show({title:"正在删除..."}),this.api.delete(s).then(e=>(v(e),e.success?e.items?(this.listData.items=e.items,e):(this.reload(),e):e))}}class et{constructor(s){n(this,"item",u({}));n(this,"is",u({show:!1,loading:!0}));this.api=s,this.show=this.show.bind(this)}show(s,e={}){return this.is.show=!0,this.is.loading=!0,this.api.fetchItem(s,e).then(i=>(Object.assign(this.item,i.item),i)).finally(()=>{this.is.loading=!1})}toEdit(s,e){this.is.show=!1,s.edit(e)}}const h={request:function(t){return typeof t.data>"u"&&(t.data={}),w(t.data)&&(t.data=N(t.data)),t.url=H(t.url),t.withCredentials=!0,T(t)},get:function(t,s={}){return this.request({method:"GET",url:t,params:s})},post:function(t,s){return this.request({method:"POST",url:t,data:s})},option:function(t,s={}){return this.request({method:"OPTION",url:t,params:s})},put:function(t,s){return this.request({method:"PUT",url:t,data:s})},patch:function(t,s){return this.request({method:"PATCH",url:t,data:s})},delete:function(t,s={}){return this.request({method:"DELETE",url:t,data:s})}},T=(t={})=>$(t).then(s=>s.data).catch(s=>M(s,t)).finally(()=>{y.hide()}),H=t=>{const s="/";return t.startsWith("/")?s+t.substring(1):s+t},M=(t,s)=>{const e=t.response||{},i=e.status,a=e.data||{};if(t.code==="ERR_NETWORK")throw g("API 请求失败"),t;if(i===401)throw x(),t;if(i===419){if(a.token_cookie_sent)return T(s);throw g("状态已过期，请刷新页面后再执行该操作"),t}if(!a.message){const r={403:"无权限访问该资源",404:"请求资源未找到",429:"你请求太频繁了，情稍等一会再来",500:"服务器异常"};a.message=r[i]||"未知错误"}throw a.success=!1,p(a),t};let m;const K=t=>{if(m)return m;const s={},e={};for(const i in t){const a=t[i].children;t[i].module&&(e[t[i].module]=parseInt(i));for(const r in a){const c=a[r];s[c.path]=[parseInt(i),parseInt(r)]}}return m={paths:s,modules:e},m},it=(t,s)=>{const e=K(s),i=e.paths[t];if(i)return i;if(t.match(/^\/[\w-]+$/))return[-1,-1];const a=t.replace(/^\/([\w-/]+)(\/.*)/,"$1").split("/");let r=null;for(let l=a.length;l>0;l--){if(r=e.paths["/"+a.join("/")],r)return r;a.pop()}const c=t.replace(/^\/([\w-]+).*/,"$1"),o=e.modules[c];return o?[o,-1]:[-1,-1]},f=u({id:0,name:"",realname:"",showName:"",avatar:"",email:"",mobile:"",enabled:!0,permissions:[]}),at=async t=>await h.get(t).then(s=>{V(s.userInfo??{}),z()||x()}),V=t=>{Object.assign(f,t),f.showName=(t.realname?t.realname:t.name)||""},W=t=>{if(!f.enabled)return!1;const s=f.permissions||[];return s[0]==="*"?!0:s.includes(t)},nt=(t,s)=>{const e={};return s.forEach(i=>{e[i]=W(t+"."+i)}),e},x=()=>{const t=window.location.hash.substring(1)||"/";t.startsWith("/login")||(window.location.hash="#/login?forward="+encodeURIComponent(t))},z=()=>f.id>0;export{tt as F,st as L,et as S,D as a,d as b,H as c,x as d,g as e,W as f,it as g,h,z as i,at as j,U as k,y as l,nt as m,Y as n,p as r,V as s,k as t,f as u,Q as x};
