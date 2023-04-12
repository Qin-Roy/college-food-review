<template>
	<view>
		<view style="margin-top: 10rpx;">
			<view style="padding-left: 20rpx;margin-bottom: 20rpx;">{{categoryName}}</view>
			<uni-card :border="false" v-for="(item,index) in storeList" 
				:title="item.nickName"
				:sub-title="item.categoryList" 
				:isFull="true" 
				:thumbnail="item.avatarUrl" @click="storeClick(index)">
			</uni-card>
		</view>
		<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				categoryName:'',
				schoolId:'',
				storeList:[],
				contenDown:{
					contentnomore: '没有更多商家啦'
				}
			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.categoryName=option.categoryName;
			this.schoolId=option.schoolId;
			uni.request({
				//获取某个分类的商家
				url: 'http://124.71.170.100/index.php/index/getStoresList',
				method: "GET",
				data: {
					schoolId: this.schoolId,
					category: this.categoryName
				},
				success: (res) => {
					console.log('获取商家成功');
					this.storeList = res.data.data.storeList;
				},
				fail: () => {
					console.log('获取商家失败');
				}
			})
		},
		methods: {
			storeClick(index) {
				console.log(this.storeList[index].nickName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/store?id=' + this.storeList[index].id + '&isCanteen=' + this.storeList[index].isCanteen
				});
			},
		}
	}
</script>

<style>

</style>
