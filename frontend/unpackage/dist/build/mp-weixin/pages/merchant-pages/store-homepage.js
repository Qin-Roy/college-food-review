(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/merchant-pages/store-homepage"],{"15ce":function(e,t,n){},"39e7":function(e,t,n){"use strict";(function(e){n("4a2a");a(n("66fd"));var t=a(n("542b"));function a(e){return e&&e.__esModule?e:{default:e}}wx.__webpack_require_UNI_MP_PLUGIN__=n,e(t.default)}).call(this,n("543d")["createPage"])},"403e":function(e,t,n){"use strict";n.r(t);var a=n("64d6"),o=n.n(a);for(var i in a)"default"!==i&&function(e){n.d(t,e,(function(){return a[e]}))}(i);t["default"]=o.a},"542b":function(e,t,n){"use strict";n.r(t);var a=n("c2d6"),o=n("403e");for(var i in o)"default"!==i&&function(e){n.d(t,e,(function(){return o[e]}))}(i);n("bba4");var r,s=n("f0c5"),u=Object(s["a"])(o["default"],a["b"],a["c"],!1,null,null,null,!1,a["a"],r);t["default"]=u.exports},"64d6":function(e,t,n){"use strict";(function(e){Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n={data:function(){return{current:0,id:"",userInfo:"",imageList:[],avatarUrl:"",storeName:"",address:"",telephone:"",intro:"",result:"",active:"",animate:"zoomIn",height:"",tabbarHeight:"",tabbar_merchant:[{name:"index",text:"首页",icon:"../../static/icons/rank.png"},{name:"message",text:"消息",icon:"../../static/icons/message.png"},{name:"homepage",text:"我的",icon:"../../static/icons/homepage-active.png"}]}},onLoad:function(){var t=this;e.getStorage({key:"userInfo",success:function(e){t.id=e.data.id,t.userInfo=e.data}})},onShow:function(){wx.hideHomeButton();var t=this;e.getStorage({key:"userInfo",success:function(n){t.id=n.data.id,t.userInfo=n.data,e.request({url:"http://124.71.170.100/index.php/index/getStoreInfo",data:{id:n.data.id},method:"GET",success:function(n){t.result=n,console.log("onShow",n),t.storeName=n.data.data.nickName,t.address=n.data.data.address,t.avatarUrl=n.data.data.avatarUrl,t.telephone=n.data.data.tel,t.intro=n.data.data.intro,t.imageList=n.data.data.pictureList,""==n.data.data.nickName&&""==n.data.data.address&&""==n.data.data.tel&&""==n.data.data.intro&&(e.showToast({title:"请完善商家信息",icon:"error",duration:1e3,mask:!0}),setTimeout((function(){e.redirectTo({url:"edit-store-info"})}),1e3))}})}})},methods:{handleChange:function(t){if(console.log("change::",t),2==this.userInfo.identity)switch(t.name){case"index":e.redirectTo({url:"/pages/index/index"});break;case"message":e.redirectTo({url:"/pages/message/message"});break;default:break}},reload:function(){var e=getCurrentPages(),t=e[e.length-1];t.onLoad(t.options),t.onShow(),t.onReady()},navigateTo:function(t){"edit-store-info"==t?e.navigateTo({url:"edit-store-info?id="+this.id}):e.navigateTo({url:t})},progressImgClick:function(t){var n=this;e.previewImage({urls:t,longPressActions:{itemList:["保存图片"],success:function(t){e.downloadFile({url:n.progress_imgs[t.index],success:function(t){200===t.statusCode&&e.saveImageToPhotosAlbum({filePath:t.tempFilePath,success:function(){e.showToast({title:"保存成功",icon:"success"})},fail:function(){e.showToast({title:"保存失败，请稍后重试",icon:"none"})}})}})},fail:function(e){console.log(e.errMsg)}}})}}};t.default=n}).call(this,n("543d")["default"])},bba4:function(e,t,n){"use strict";var a=n("15ce"),o=n.n(a);o.a},c2d6:function(e,t,n){"use strict";n.d(t,"b",(function(){return o})),n.d(t,"c",(function(){return i})),n.d(t,"a",(function(){return a}));var a={uniSection:function(){return n.e("components/uni-section/uni-section").then(n.bind(null,"b506"))},uniGroup:function(){return n.e("uni_modules/uni-group/components/uni-group/uni-group").then(n.bind(null,"74d5"))},lbTabbar:function(){return Promise.all([n.e("common/vendor"),n.e("components/lb-tabbar/lb-tabbar")]).then(n.bind(null,"b226"))},lbTabbarItem:function(){return Promise.all([n.e("common/vendor"),n.e("components/lb-tabbar/lb-tabbar-item")]).then(n.bind(null,"26a2"))}},o=function(){var e=this,t=e.$createElement;e._self._c},i=[]}},[["39e7","common/runtime","common/vendor"]]]);