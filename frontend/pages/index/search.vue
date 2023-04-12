<template>
	<view class="serach">
		<view>
			<uni-search-bar radius="100" placeholder="搜索商家或菜品" v-model="searchValue" clearButton="auto"
				cancelButton="none" @confirm="search" />
		</view>
		<view class="stores">
			<view style="margin-bottom: 20rpx;">商家</view>
			<uni-card :border="false" v-for="(item,index) in storeList" 
			:title="item.nickName"
			:sub-title="item.categoryList" 
			:isFull="true" 
			:thumbnail="item.avatarUrl" 
			@click="storeClick(index)">
			</uni-card>
			<uni-load-more status="noMore" :contentText="contenDown1"></uni-load-more>
			<view style="margin-top: 20rpx;margin-bottom: 20rpx;">菜品</view>
			<uni-card :border="false" v-for="(item,index) in dishList" 
			:title="item.dishName"
			:sub-title="item.intro" 
			:isFull="true" 
			:extra="item.price" 
			:thumbnail="item.picture"
			@click="dishClick(index)">
			</uni-card>
			<uni-load-more status="noMore" :contentText="contenDown2"></uni-load-more>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				searchValue: '',
				storeList: [],
				dishList: [],
				contenDown1:{
					contentnomore: '没有更多商家啦'
				},
				contenDown2:{
					contentnomore: '没有更多菜品啦'
				}
			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.searchValue=option.res;
			console.log("搜索传值：" + this.searchValue);
			this.refresh(this.searchValue); 
		},
		methods: {
			refresh(searchValue) {
				//通过搜索结果请求商家信息
				uni.request({
					url: 'http://124.71.170.100/index.php/index/searchDish',
					method: 'GET',
					data: {
						keywords: this.searchValue
					},
					success: (res) => {
						this.storeList = res.data.data.storeList;
						this.dishList = res.data.data.dishList;
						console.log('搜索成功');
						console.log(res);
						console.log(this.searchValue);
					},
					fail: () => {
						console.log('搜索失败');
					}
				})
			},
			search(res) {
				console.log("搜索：" + this.searchValue);
				this.refresh(this.searchValue); 
			},
			storeClick(index) {
				console.log(this.storeList[index].nickName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/store?id=' + this.storeList[index].id + '&isCanteen=' + this.storeList[index].isCanteen
				});
			},
			dishClick(index) {
				console.log(this.dishList[index].dishName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/dish?dishId=' + this.dishList[index].dishId + "&isCanteen=" + this.dishList[index].isCanteen
				});
			},
		}
	}
</script>

<style>
	.stores {
		margin-top: 30rpx;
	}
</style>
