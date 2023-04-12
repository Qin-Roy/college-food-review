<!-- 食堂列表页面 -->
<template>
	<view class="canteenList">
		<view class="button">
			<button class="btn" @click="addCanteens()">创建食堂</button>
		</view>
		<view class="list">
			<uni-section title="食堂列表" type="line">
				<uni-list v-for="(item, index) in canteens">
					<uni-swipe-action>
						<uni-swipe-action-item :right-options="options" @click="bindClick($event, index)">
							<uni-list-item thumbSize="lg" clickable="true" :title="item.name" :thumb="item.avatarUrl"
								@click="click(item)">
							</uni-list-item>
						</uni-swipe-action-item>
					</uni-swipe-action>
				</uni-list>
			</uni-section>
			<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
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
				canteens: [],
				contenDown: {
					contentnomore: '没有更多食堂了'
				},
				options: [{
					text: '取消',
					style: {
						backgroundColor: '#007aff'
					}
				}, {
					text: '删除',
					style: {
						backgroundColor: '#dd524d'
					}
				}],
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
						icon: '../../static/icons/cube.png'
					},
					{
						name: 'canteen',
						text: '食堂',
						icon: '../../static/icons/edit-active.png'
					}
				]
			}
		},
		onShow() {
			wx.hideHomeButton()
			this.getCanteenList()
		},
		onLoad() {
			this.identity = 0
			this.getCanteenList()
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
				// console.log('change::', e)
				if (this.identity == 0) {
					// 管理员
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '../index/index'
							})
							break;
						case 'manage':
							uni.redirectTo({
								url: '../admin/manage'
							})
							break;
							// case 'canteen':
							// 	uni.redirectTo({
							// 		url: '../canteen/canteenList'
							// 	})
							// 	break;
						default:
							break;
					}
				}
			},
			getCanteenList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getAllCanteenInfo',
					method: 'GET',
					success: (res) => {
						this.canteens = res.data.data.canteenList
					}
				})
			},
			addCanteens(url) {
				uni.navigateTo({
					url: "edit-canteen-info"
				})
			},
			deleteCanteens(index) {
				let canteenId = this.canteens[index].canteenId
				console.log(canteenId)
				uni.request({
					url: 'http://124.71.170.100/index.php/index/deleteCanteen',
					data: {
						canteenId: canteenId
					},
					method: 'GET',
					success: (res) => {
						console.log(res.data.code)
						this.getCanteenList()
					}
				})
			},
			click(ref) {
				console.log(ref.canteenId)
				uni.navigateTo({
					url: "./canteenIndex?id=" + ref.canteenId + "&url=" + ref.avatarUrl
				})
			},
			bindClick(e, index) {
				let that = this
				if (e.index == 1) {
					uni.showModal({
						title: '提示',
						content: '是否要删除该食堂',
						success: function(res) {
							if (res.confirm) {
								that.deleteCanteens(index)
							}
						}
					})
				}
			}
		}
	}
</script>

<style>
	.btn {
		background: #FFC837;
		margin: 30rpx 10rpx;
	}
</style>
