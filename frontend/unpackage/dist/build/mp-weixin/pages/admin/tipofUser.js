(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/admin/tipofUser"],{"358f":function(n,t,e){"use strict";e.r(t);var i=e("3c61"),u=e.n(i);for(var o in i)"default"!==o&&function(n){e.d(t,n,(function(){return i[n]}))}(o);t["default"]=u.a},"3c61":function(n,t,e){"use strict";(function(n){Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var e={data:function(){return{listNum:"",users:[],contenDown:{contentnomore:"没有需要处理的用户举报"}}},onShow:function(){this.getTipofUserList()},onLoad:function(){this.getTipofUserList()},methods:{getTipofUserList:function(){var t=this;n.request({url:"http://124.71.170.100/index.php/index/getUserReport",method:"GET",success:function(n){var e=n.data.data.reportList;t.users=e,t.listNum=e.length}})},chooseUser:function(t){n.navigateTo({url:"setTipofUser?index="+t})}}};t.default=e}).call(this,e("543d")["default"])},"86db":function(n,t,e){"use strict";e.r(t);var i=e("fdc6"),u=e("358f");for(var o in u)"default"!==o&&function(n){e.d(t,n,(function(){return u[n]}))}(o);e("d1b2");var r,s=e("f0c5"),c=Object(s["a"])(u["default"],i["b"],i["c"],!1,null,null,null,!1,i["a"],r);t["default"]=c.exports},d1b2:function(n,t,e){"use strict";var i=e("d9be"),u=e.n(i);u.a},d9be:function(n,t,e){},f34b:function(n,t,e){"use strict";(function(n){e("4a2a");i(e("66fd"));var t=i(e("86db"));function i(n){return n&&n.__esModule?n:{default:n}}wx.__webpack_require_UNI_MP_PLUGIN__=e,n(t.default)}).call(this,e("543d")["createPage"])},fdc6:function(n,t,e){"use strict";e.d(t,"b",(function(){return u})),e.d(t,"c",(function(){return o})),e.d(t,"a",(function(){return i}));var i={uniSection:function(){return e.e("components/uni-section/uni-section").then(e.bind(null,"b506"))},uniList:function(){return e.e("uni_modules/uni-list/components/uni-list/uni-list").then(e.bind(null,"7154"))},uniListItem:function(){return e.e("uni_modules/uni-list/components/uni-list-item/uni-list-item").then(e.bind(null,"81b3"))},uniLoadMore:function(){return Promise.all([e.e("common/vendor"),e.e("uni_modules/uni-load-more/components/uni-load-more/uni-load-more")]).then(e.bind(null,"b4b9"))}},u=function(){var n=this,t=n.$createElement;n._self._c},o=[]}},[["f34b","common/runtime","common/vendor"]]]);