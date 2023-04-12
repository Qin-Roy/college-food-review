<!-- 删除功能mock 正确 服务器端还未测试-->
<template>
	<view class="dishes">
		<view class="myButtonArea">
			<button class="myButton" style="position: absolute; bottom: 10rpx; right: 5vw"
				@click="navigateTo('add-dishes')">上传新菜品</button>
		</view>
		<view class="dish">
			<uni-section title="菜品列表" type="line">
				<uni-list v-for="(item, index) in dishes">
					<uni-swipe-action>
						<uni-swipe-action-item :right-options="options" @change="change()"
							@click="bindClick($event, index)">
							<uni-list-item thumbSize="lg" clickable="true" :title="item.dishName" :note="item.intro"
								:thumb="item.dishPictureList[0]" :rightText="item.price" @click="click(item)">
							</uni-list-item>
						</uni-swipe-action-item>
					</uni-swipe-action>
				</uni-list>
			</uni-section>
		</view>

	</view>
</template>

<script>
	export default {
		data() {
			return {
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
				dishes: [],
				userInfo: "",

			}
		},
		onLoad() {
			// let that = this;
			// uni.getStorage({
			// 	key:'userInfo',
			// 	success:(res)=>{
			// 		that.userInfo= res.data;
			// 		uni.request({
			// 			url: 'http://124.71.170.100/index.php/index/getStoreInfo',
			// 			data:{
			// 				id: res.data.id
			// 			},
			// 			method: 'GET',
			// 			success: (res) => {
			// 				console.log("菜品列表" + res.data.data.dishList)
			// 				that.dishes = res.data.data.dishList;
			// 			}
			// 		})
			// 	}
			// })
		},
		onShow() {
			wx.hideHomeButton();
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/getStoreInfo',
						data: {
							id: res.data.id
						},
						method: 'GET',
						success: (res) => {
							console.log("菜品列表", res.data.data.dishList)
							that.dishes = res.data.data.dishList;
						}
					})
				}
			})
		},
		methods: {
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
				if (this.dishes.length < 100) {
					uni.navigateTo({
						url: "./add-dishes?id=0&status=0"
					})
				} else {
					uni.showToast({
						icon: "error",
						title: "菜品太多了啦~"
					})
				}
			},
			click(ref) {
				console.log(ref.dishId)
				uni.navigateTo({
					url: "./add-dishes?id=" + ref.dishId + "&status=1"
				})
			},
			change(e) {
				// this.isOpened = e;
				// console.log('返回：', e);
			},
			bindClick(e, index) {
				// console.log(e.index); // 等于 1 时进行删除操作
				// let dishId1 = this.dishes[index].dishId;
				let that = this;
				console.log("dishId:  " + that.dishes[index].dishId);
				if (e.index == 1) {
					uni.request({
						url: 'http://124.71.170.100/index.php/index/deleteDish',
						data: {
							dishId: that.dishes[index].dishId,
							isCanteen: "0"
						},
						method: 'GET',
						success: (res) => {
							console.log("删除成功" + res.data.code);
							that.reload()


						}
					})
				}
			}
		}
	}
</script>

<style>
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

	.overflow {
		/* width: 400rpx; */
		white-space: nowarp;
		/*强制在一行显示*/
		over-flow: hidden;
		/*溢出隐藏*/
		text-overflow: ellipsip;
		/*溢出显示省略号*/
	}
</style>
