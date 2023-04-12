<!-- 管理页面 -->
<template>
	<view>
		<view class="buttons">
			<button class="btn" @click="setAppeal()">处理申诉</button>
			<button class="btn" @click="setAct()">处理商家活动</button>
			<button class="btn" @click="setTipofUser()">处理用户举报</button>
			<button class="btn" @click="setTipofComment()">处理评论举报</button>
		</view>

		<!-- tabBar -->
		<lb-tabbar ref="tabbar" :value="active" :animate="animate" @change="handleChange">
			<lb-tabbar-item v-if="identity == 0" v-for="item in tabbar_admin" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<!-- <lb-tabbar-item v-if="identity == 1" v-for="item in tabbar_user" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<lb-tabbar-item v-if="identity == 2" v-for="item in tabbar_merchant" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
		</lb-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				identity: 0,
				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_admin: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'manage',
						text: '管理',
						icon: '../../static/icons/cube-active.png'
					},
					{
						name: 'canteen',
						text: '食堂',
						icon: '../../static/icons/edit.png'
					}
				]
			}
		},
		onShow() {
			wx.hideHomeButton();
		},
		onLoad() {
			this.identity = 0
		},
		onPullDownRefresh() {
			console.log('refresh');
			var that = this;
			setTimeout(function() {
				that.refresh("1"); //需要修改
				uni.stopPullDownRefresh();
			}, 1000);
		},
		methods: {
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.identity == 0) {
					// 管理员
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '../index/index'
							})
							break;
							// case 'manage':
							// 	uni.redirectTo({
							// 		url: '../admin/manage'
							// 	})
							// 	break;
						case 'canteen':
							uni.redirectTo({
								url: '../canteen/canteenList'
							})
							break;
						default:
							break;
					}
				} 
			},
			setAppeal() {
				console.log("appeal")
				uni.navigateTo({
					url: 'appealList'
				})
			},
			setAct() {
				console.log("act")
				uni.navigateTo({
					url: 'activityList'
				})
			},
			setTipofUser() {
				console.log("user")
				uni.navigateTo({
					url: 'tipofUser'
				})
			},
			setTipofComment() {
				console.log("comment")
				uni.navigateTo({
					url: 'tipofComment'
				})
			}
		}
	}
</script>

<style>
	.buttons {
		margin-top: 50rpx;
	}
	
	.btn {
		background: #FFC837;
		margin: 50rpx 10rpx;
	}

/* 	.btn { */
		/* background-color: ##FFbd49; */
/* 		margin: 5vh auto;
		display: block;
		background-image: linear-gradient(to right, #FFbd49 0%, #FFbd49 100%); */
		/* text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white; 
 		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block; */
	
/* 	} */
	
	/* .btn:hover {
		background-position: right center;
		/* change the direction of the change here */
	/* 	color: #fff;
		text-decoration: none;
	} */
</style>
