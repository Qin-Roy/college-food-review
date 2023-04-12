<!--收藏店铺-->
<template>
	<view class="love-stores">
		<uni-nav-bar title="收藏店铺" backgroundColor="#f0f0f0" shadow="true" fixed="true">
		</uni-nav-bar>
		<uni-list>
			<uni-list-item clickable="true" thumb-size="lg" showArrow="true" v-for="(item,index) in collectedStoreList"
				:title="item.nickName" :note="item.categoryList" :thumb="item.avatarUrl"
				@click="restaurantClick(item.id)">
			</uni-list-item>
		</uni-list>

		<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: '',
				collectedStoreList: [],
				contenDown: {
					contentnomore: '没有更多商家啦'
				}
			}
		},
		onLoad(option) {
			this.id = option.id;
		},
		onShow() {
			// 获取存储信息
			let that = this;

			// 获取收藏列表
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getFavouredStoreList',
				method: "GET",
				data: {
					id: that.id
				},
				success: (res) => {
					console.log('获取商家列表成功' + res.data.data.favouredStoreList);
					console.log(that.id);
					console.log(res.data.code);
					console.log(res.data.data.favouredStoreList);
					that.collectedStoreList = res.data.data.favouredStoreList;
				},
				fail: () => {
					console.log('获取商家列表失败');
				}
			});

		},
		methods: {
			restaurantClick(id) {
				uni.navigateTo({
					url: "../index/store?id=" + id
				});
				console.log(id + "被点击了");
			},
			back() {
				console.log("点击了返回");
			}
		}
	}
</script>

<style>
	.restaurantTitle {
		background: #d1d1d1;
		font-weight: 100;
		flex-direction: row;
	}

	.box-bg {
		background-color: #F5F5F5;
		padding: 5px 0;
	}
</style>
