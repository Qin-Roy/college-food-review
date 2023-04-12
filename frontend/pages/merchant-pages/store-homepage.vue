<template>
	<view class="store-homepage">
		<view class="header">
			<view v-if="imageList.length != 0">
				<swiper class="swiper" indicator-dots="true" autoplay="true" interval="3000" circular="true"
					indicator-active-color="white">
					<swiper-item v-for="item in imageList">
						<!--No more than 3 photos-->
						<image class="headImage" mode="aspectFill" :src="item" @click="progressImgClick(imageList)"></image>
					</swiper-item>
				</swiper>
			</view>
			<image v-else class="headImage" mode="aspectFill" src="../../static/null.jpg" @click="progressImgClick(imageList)"></image>
		</view>
		<!--Show the avatar-->
		<view class="storeInfo">
			<image class="avatar" :src="avatarUrl"></image>
			<uni-section style="font-size: 40rpx;" title="商家信息" type="line">
				<uni-group mode="card">
					<view class="info">商家名称：{{storeName}}</view>
					<view class="info">地址：{{address}}</view>
					<view class="info">联系方式：{{telephone}}</view>
					<view class="info">简介：{{intro}}</view>
				</uni-group>
				<view style="height: 10rpx;"></view>
			</uni-section>
		</view>
		<view class="myButtonArea">
			<button class="myButton" @click="navigateTo('edit-store-info')">点击修改信息</button>
		</view>
		<view class="myButtonArea">
			<button class="myButton" @click="navigateTo('edit-dishes')">点击管理菜品</button>
		</view>
		<view class="myButtonArea">
			<button class="myButton" @click="navigateTo('edit-acts')">点击管理活动</button>
		</view>
		<!-- tabBar -->
		<lb-tabbar ref="tabbar" :value="active" :animate="animate" @change="handleChange">
			<!-- <lb-tabbar-item v-if="userInfo.identity == 0" v-for="item in tabbar_admin" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<lb-tabbar-item v-if="userInfo.identity == 1" v-for="item in tabbar_user" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
			<lb-tabbar-item v-if="userInfo.identity == 2" v-for="item in tabbar_merchant" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
		</lb-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				current: 0,
				id: "",
				userInfo: "",
				imageList: [],
				avatarUrl: "",
				storeName: "",
				address: "",
				telephone: "",
				intro: "",
				result: "",
				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_merchant: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message.png',
					},
					{
						name: 'homepage',
						text: '我的',
						icon: '../../static/icons/homepage-active.png'
					}
				]


			}
		},
		onLoad() {
			// 根据
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res1) {
					that.id = res1.data.id;
					that.userInfo = res1.data;
				}
			})

		},
		onShow() {
			wx.hideHomeButton();
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res1) {
					that.id = res1.data.id;
					that.userInfo = res1.data;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/getStoreInfo',
						data: {
							id: res1.data.id
						},
						method: 'GET',
						success: (res) => {
							that.result = res;
							console.log('onShow', res)
							that.storeName = res.data.data.nickName;
							that.address = res.data.data.address;
							that.avatarUrl = res.data.data.avatarUrl;
							that.telephone = res.data.data.tel;
							that.intro = res.data.data.intro;
							that.imageList = res.data.data.pictureList;
							if ((res.data.data.nickName == "") && (res.data.data.address == "") &&
								(
									res.data.data.tel == "") && (res.data.data.intro == "")) {
								uni.showToast({
									title: "请完善商家信息",
									icon: "error",
									duration: 1000,
									mask: true
								})
								setTimeout(function() {
									uni.redirectTo({
										url: 'edit-store-info'
									})
								}, 1000);
							}
						}
					})
				}
			})
		},
		methods: {
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.userInfo.identity == 2) {
					// 商家
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
						case 'message':
							uni.redirectTo({
								url: '/pages/message/message'
							})
							break;
							// case 'homepage':
							// 	uni.redirectTo({
							// 		url: '/pages/merchant-pages/store-homepage'
							// 	})
							// 	break;
						default:
							break;
					}
				}
			},
			reload() {
				// 页面重载
				const pages = getCurrentPages()
				// 声明一个pages使用getCurrentPages方法
				const curPage = pages[pages.length - 1]
				// 声明一个当前页面
				curPage.onLoad(curPage.options) // 传入参数
				curPage.onShow()
				curPage.onReady()
				// 执行刷新
			},

			navigateTo(url) {
				if (url == 'edit-store-info') {
					uni.navigateTo({
						url: 'edit-store-info?id=' + this.id
					})
				} else {
					uni.navigateTo({
						url: url
					})
				}
			},
			// 图片预览以及长按下载处理
			progressImgClick(file_arr) {
				let that = this;
				// 图片预览
				uni.previewImage({
					urls: file_arr,
					// 长按图片处理
					longPressActions: {
						itemList: ['保存图片'],
						success: function(data) {
							// 成功预览后下载图片
							uni.downloadFile({
								// 需要下载的图片路径
								url: that.progress_imgs[data.index],
								success: (res) => {
									if (res.statusCode === 200) {
										// 调用图片保存到相册
										uni.saveImageToPhotosAlbum({
											filePath: res.tempFilePath,
											success: function() {
												//uniapp提供的消息提示框。
												uni.showToast({
													title: "保存成功",
													icon: "success"
												});
											},
											fail: function() {
												uni.showToast({
													title: "保存失败，请稍后重试",
													icon: "none"
												});
											}
										});
									}
								}
							})
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			}
		}
	}
</script>

<style>
	.text {
		font-weight: bold;
	}
	.info {
		white-space: normal;
		word-break: break-word;
		word-warp: break-word;
	}

	.swiper {
		width: 100%;
		height: 400rpx;
	}

	.headImage {
		width: 100%;
	}

	.avatar {
		position: relative;
		display: flex;
		margin-top: -65rpx;
		margin-right: 40rpx;
		float: right;
		width: 150rpx;
		height: 150rpx;
		border-radius: 10%;
	}

	.storeInfo {}

	.myButtonArea {
		margin-bottom: 20rpx;
		/*margin-bottom: 20rpx;*/
	}

	.myButton {
		width: 90vw;
		margin: 1vh auto;
		background-image: linear-gradient(to right, #FF8008 0%, #FFC837 51%, #FF8008 100%);
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block;

	}

	.myButton:hover {
		background-position: right center;
		/* change the direction of the change here */
		color: #fff;
		text-decoration: none;
	}
</style>
