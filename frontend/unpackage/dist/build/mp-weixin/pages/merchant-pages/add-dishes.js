(global["webpackJsonp"]=global["webpackJsonp"]||[]).push([["pages/merchant-pages/add-dishes"],{"0551":function(t,e,i){"use strict";var a=i("695f"),n=i.n(a);n.a},"0d9a":function(t,e,i){"use strict";i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return s})),i.d(e,"a",(function(){return a}));var a={uniForms:function(){return Promise.all([i.e("common/vendor"),i.e("uni_modules/uni-forms/components/uni-forms/uni-forms")]).then(i.bind(null,"9d30"))},uniSection:function(){return i.e("components/uni-section/uni-section").then(i.bind(null,"b506"))},uniFormsItem:function(){return Promise.all([i.e("common/vendor"),i.e("uni_modules/uni-forms/components/uni-forms-item/uni-forms-item")]).then(i.bind(null,"b4d1"))},uniEasyinput:function(){return i.e("uni_modules/uni-easyinput/components/uni-easyinput/uni-easyinput").then(i.bind(null,"8b0a"))},uniFilePicker:function(){return Promise.all([i.e("common/vendor"),i.e("uni_modules/uni-file-picker/components/uni-file-picker/uni-file-picker")]).then(i.bind(null,"cc85"))}},n=function(){var t=this,e=t.$createElement;t._self._c},s=[]},"2de4":function(t,e,i){"use strict";(function(t){Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{id:"",userInfo:"",status:"",dishData:{dishId:"",name:"",price:"",intro:"",images:[],tmp_images:[]},customRules:{name:{rules:[{required:!0,errorMessage:"菜品名称不能为空"}]},price:{rules:[{required:!0,errorMessage:"菜品价格不能为空"}]},intro:{rules:[{required:!0,errorMessage:"菜品介绍不能为空"}]}}}},onLoad:function(e){var i=this,a=this;t.getStorage({key:"userInfo",success:function(n){a.userInfo=n.data,a.status=e.status,1==e.status&&t.request({url:"http://124.71.170.100/index.php/index/getSingleDishInfo",method:"GET",data:{dishId:e.id,isCanteen:"0"},success:function(t){console.log(t),i.dishData.dishId=t.data.data.dishId,i.dishData.name=t.data.data.dishName,i.dishData.intro=t.data.data.intro,i.dishData.price=t.data.data.price,i.dishData.tmp_images=t.data.data.pictureList;for(var e=0;e<t.data.data.pictureList.length;e++){var a={url:t.data.data.pictureList[e]};i.dishData.images.push(a)}}})}})},methods:{submit:function(e,i){var a=this,n=this;this.$refs[e].validate().then((function(e){1==n.status?t.request({url:"http://124.71.170.100/index.php/index/editDish",method:"POST",header:{"content-type":"application/json"},data:{dishName:a.dishData.name,intro:a.dishData.intro,price:a.dishData.price.toString(),pictureList:a.dishData.tmp_images,dishId:a.dishData.dishId,isCanteen:"0"},success:function(e){console.log(e),t.navigateBack(),t.showToast({title:"提交成功",mask:!0})}}):t.request({url:"http://124.71.170.100/index.php/index/addDish",method:"POST",header:{"content-type":"application/json"},data:{dishName:a.dishData.name,intro:a.dishData.intro,price:a.dishData.price,pictureList:a.dishData.tmp_images,merchantId:a.userInfo.id,isCanteen:"0"},success:function(e){console.log(e),t.navigateBack(),t.showToast({title:"提交成功",mask:!0})}})})).catch((function(t){console.log("err",t)}))},selectImage:function(t){var e=this;console.log("选择图片",t),e.dishData.images=[];for(var i=0;i<e.dishData.tmp_images.length;i++){var a={url:e.dishData.tmp_images[i]};e.dishData.images.push(a)}for(var n=0;n<t.tempFilePaths.length;n++)e.$OBS.myUploadFile((new Date).getTime()+t.tempFiles[n].name,t.tempFilePaths[n],t.tempFiles[n].fileType,"se-store-dish").then((function(t){console.log("success",t),e.dishData.tmp_images.push(t),e.dishData.images=[];for(var i=0;i<e.dishData.tmp_images.length;i++){var a={url:e.dishData.tmp_images[i]};e.dishData.images.push(a)}}))},deleteImage:function(t){console.log(t),console.log(this.dishData.images);for(var e=0;e<this.dishData.tmp_images.length;e++)this.dishData.tmp_images[e]==t.tempFilePath&&this.dishData.tmp_images.splice(e,1)}}};e.default=i}).call(this,i("543d")["default"])},"44d9":function(t,e,i){"use strict";i.r(e);var a=i("2de4"),n=i.n(a);for(var s in a)"default"!==s&&function(t){i.d(e,t,(function(){return a[t]}))}(s);e["default"]=n.a},"695f":function(t,e,i){},cdf2:function(t,e,i){"use strict";i.r(e);var a=i("0d9a"),n=i("44d9");for(var s in n)"default"!==s&&function(t){i.d(e,t,(function(){return n[t]}))}(s);i("0551");var o,r=i("f0c5"),u=Object(r["a"])(n["default"],a["b"],a["c"],!1,null,null,null,!1,a["a"],o);e["default"]=u.exports},db9a:function(t,e,i){"use strict";(function(t){i("4a2a");a(i("66fd"));var e=a(i("cdf2"));function a(t){return t&&t.__esModule?t:{default:t}}wx.__webpack_require_UNI_MP_PLUGIN__=i,t(e.default)}).call(this,i("543d")["createPage"])}},[["db9a","common/runtime","common/vendor"]]]);