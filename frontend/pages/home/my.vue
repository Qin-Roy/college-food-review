<template>
	<view class="my">
		<view class="top">
			<image class="avatar" @click="previewImg(userInfo.avatarUrl)" :src="userInfo.avatarUrl"></image>
			<view class="content">
				<text class="nickname">{{userInfo.nickName}}</text>
				<button class="bnt indexNav" @click="toHomePage()">我的主页</button>
			</view>
		</view>

		<uni-list>

			<uni-list-item :show-extra-icon="true" showArrow :extra-icon="extraIcon" title="好友" clickable="true"
				@click="toFriends()">
			</uni-list-item>

			<uni-list-item :show-extra-icon="true" showArrow :extra-icon="extraIcon" title="收藏店铺" clickable="true"
				@click="toCollectStores()">
			</uni-list-item>

			<uni-list-item :show-extra-icon="true" showArrow :extra-icon="extraIcon" title="关注用户" clickable="true"
				@click="toFollows()">
			</uni-list-item>

		</uni-list>

		<!-- tabBar -->
		<lb-tabbar ref="tabbar" :value="active" :animate="animate" @change="handleChange">
			<!-- <lb-tabbar-item v-if="userInfo.identity == 0" v-for="item in tabbar_admin" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
			<lb-tabbar-item v-if="userInfo.identity == 1" v-for="item in tabbar_user" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<!-- <lb-tabbar-item v-if="userInfo.identity == 2" v-for="item in tabbar_merchant" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
		</lb-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: "",
				userInfo: "",
				extraIcon: {
					size: '22',
					type: 'gear-filled'
				},

				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_user: [{
						name: 'home',
						text: '首页',
						icon: 'home',
						dot: true
					},
					{
						name: 'cart',
						text: '购物车',
						icon: 'cart'
					},
					{
						name: 'plus',
						text: '发布',
						icon: 'plus',
						raisede: true
					},
					{
						name: 'notice',
						text: '消息',
						icon: 'notice',
						info: 99
					},
					{
						name: 'mine',
						text: '我的',
						icon: 'mine'
					}
				],
				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_user: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'moments',
						text: '动态',
						icon: '../../static/icons/explore.png'
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message.png',
					},
					{
						name: 'home',
						text: '我的',
						icon: '../../static/icons/homepage-active.png'
					}
				],
				

			}
		},
		onLoad() {
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data;
					that.id = res.data.id;
				}
			})
		},
		onShow() {
			wx.hideHomeButton();
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data;
				}
			})
		},
		methods: {
			previewImg(logourl) {
				let _this = this;
				let imgsArray = [];
				imgsArray[0] = logourl
				uni.previewImage({
					current: 0,
					urls: imgsArray
				});
			},
			toHomePage() {
				uni.navigateTo({
					url: '/pages/home/homepage?id=' + this.id
				});
			},
			toFriends() {
				uni.navigateTo({
					url: '/pages/home/friends?id=' + this.id
				});
			},
			toCollectStores() {
				uni.navigateTo({
					url: '/pages/home/love-stores?id=' + this.id
				});
			},
			toFollows() {
				uni.navigateTo({
					url: '/pages/home/follows?id=' + this.id
				});
			},
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.userInfo.identity == 0) {
					// 管理员
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '../index/index'
							})
							break;
						case 'moments':
							uni.redirectTo({
								url: '../moments/moments'
							})
							break;
						case 'message':
							uni.redirectTo({
								url: '../message/message'
							})
							break;
							// case 'home':
							// 	uni.redirectTo({
							// 		url: '../home/my'
							// 	})
							// 	break;
						default:
							break;
					}
				} else if (this.userInfo.identity == 1) {
					// 普通用户
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
						case 'moments':
							uni.redirectTo({
								url: '/pages/moments/moments'
							})
							break;
						case 'message':
							uni.redirectTo({
								url: '/pages/message/message'
							})
							break;
							// case 'home':
							// 	uni.redirectTo({
							// 		url: '/pages/home/my'
							// 	})
							// 	break;
						default:
							break;
					}
				} else if (this.userInfo.identity == 2) {
					// 商家
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
						case 'acts':
							uni.redirectTo({
								url: '/pages/merchant-pages/edit-acts'
							})
							break;
						case 'dishes':
							uni.redirectTo({
								url: '/pages/merchant-pages/edit-dishs'
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
			navigateTo(url) {
				uni.navigateTo({
					url,
				});
			}
		}
	}
</script>

<style>
	.my {
		width: 100%;
		height: 100%;
	}

	.top {
		width: 100%;
		height: 200rpx;
		margin-bottom: 30rpx;
		background: #F7F7F7;
		text-align: center;
		display: flex;
		/*align-items: center;
		padding-top: 20rpx;*/

	}

	.avatar {
		width: 150rpx;
		height: 150rpx;
		margin-top: 20rpx;
		margin-left: 20rpx;
		border-radius: 50%;
		vertical-align: middle;
	}

	.content {
		width: 300rpx;
		height: 50rpx;

	}

	.nickname {
		display: flex;
		margin-top: 40rpx;
		margin-bottom: 10rpx;
		margin-left: 80rpx;
		font-size: 35rpx;
	}

	.indexNav {
		width: 25vw;
		height: 75rpx;
		text-align: center;
		align-items: center;
		margin-top: 10rpx;
		font-size: 30rpx;
		border-radius: 10%;
	}
</style>
