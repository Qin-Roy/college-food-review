<!-- 食堂更新菜品列表和添加菜品 -->
<template>
	<view class="edit-dishes">
		<view class="button">
		    <button class="btn" @click="navigateTo('add-dishes')">上传新菜品</button>
		</view>
		<view class="dishList">
			<uni-section title="更新菜品请求" type="line">
				<uni-list v-for="(item, index) in updates">
					<uni-list-item thumbSize="lg" clickable="true"
					:title="item.dishName"
					:note="item.intro"
					:thumb="item.pictureList[0]"
					:rightText="item.price"
					@click="clickUpdate(item, index)">
					</uni-list-item>
				</uni-list>
				<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
			</uni-section>
			<uni-section title="菜品列表" type="line">
				<uni-list v-for="(item, index) in dishes">
					<uni-swipe-action>
						<uni-swipe-action-item :right-options="options" @click="bindClick($event, index)">
							<uni-list-item thumbSize="lg" clickable="true"
							:title="item.dishName"
							:note="item.intro"
							:thumb="item.picture"
							:rightText="item.price"
							@click="click(item, index)">
							</uni-list-item>
						</uni-swipe-action-item>
					</uni-swipe-action>
				</uni-list>
				<uni-load-more status="noMore" :contentText="dishrule"></uni-load-more>
			</uni-section>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: "",
				userId: "",
				updates: "",
				dishes: "",
				contenDown: {
					contentnomore: '没有更多的菜品更新请求'
				},
				dishrule: {
					contentnomore: '没有更多的菜品'
				},
				options:[{
				    text: '取消',
				    style: {
				        backgroundColor: '#007aff'
				    }
				}, {
					text: '删除',
					style: {
				        backgroundColor: '#dd524d'
					}
				}]
			}
		},
		onLoad(option) {
			this.id = option.id
			console.log(this.id)
			this.getUpdate()
			this.getUserInfo()
		},
		onShow() {
			this.getUpdate()
			this.getUserInfo()
		},
		methods: {
			getUserInfo() {
				let that = this
				uni.getStorage({
					key: 'userInfo',
					success: (res) =>{
						that.userId = res.data.id
						that.getDishes()
					}
				})
			},
			getUpdate() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getdishUpdateRequest',
					method: 'GET',
					data: {
						canteenId: this.id
					},
					success: (res) => {
						console.log(res.data.data.updateList)
						this.updates = res.data.data.updateList
					}
				})
			},
			getDishes() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getOneCanteenInfo',
					data: {
						userId: this.userId,
						canteenId: this.id
					},
					method: 'GET',
					success: (res) => {
						console.log(res.data.data.dishList)
						this.dishes = res.data.data.dishList
					}
				})
			},
			
			navigateTo(url) {
				// 添加
				uni.navigateTo({
					// url: "./add-dishes?id=0&status=0"
					url: "./add-dishes?id=" + this.id + "&status=0"
				})
			},
			click(ref, index) {
				// 编辑
				console.log(this.id + " " + this.dishes[index].dishId)
				uni.navigateTo({
					url: "./add-dishes?id=" + this.id + "&index=" + index + "&dishId=" + this.dishes[index].dishId + "&status=1"
				})
			},
			clickUpdate(ref, index) {
				// 更新
				console.log(this.dishes)
				// console.log(this.id + " " + this.dishes[index].dishId)
				uni.navigateTo({
					url: "./add-dishes?id=" + this.id + "&index=" + index + "&status=2"
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
				if(e.index == 1) {
					uni.showModal({
						title: '提示',
						content: '是否要删除该菜品',
						success: function (res) {
							if (res.confirm) {
								uni.request({
									url: 'http://124.71.170.100/index.php/index/deleteDish',
									data: {
										dishId: that.dishes[index].dishId,
										isCanteen: "1"
									},
									method: 'GET',
									success: (res) => {
										console.log("删除成功" + res.data.code);
										that.getUpdate()
										that.getUserInfo()
									}
								})
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
