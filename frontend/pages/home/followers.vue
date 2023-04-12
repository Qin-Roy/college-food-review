<!--我的粉丝-->
<template>
	<view class="love-stores">
		<uni-nav-bar title="粉丝" backgroundColor="#f0f0f0" shadow="true" fixed="true">
		</uni-nav-bar>
		<uni-list>
			<uni-list-item clickable="true" thumb-size="lg" showArrow="true" v-for="(item,index) in followerList"
				:title="((item.remark==NULL)||(item.remark==''))||(isShowRemark==true)? item.nickName : item.remark" :thumb="item.avatarUrl"
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
				isShowRemark:false,
				myId:'',
				id: '',
				followerList: [],
				contenDown: {
					contentnomore: '没有更多粉丝啦'
				}
			}
		},
		onLoad(option) {
			this.id = option.id;
		},
		onShow() {
			// 获取存储信息
			let that = this
			// 获取系统数据
			uni.getStorage({
				key: 'userInfo',
				success(res) {
					that.myId = res.data.id
					if(that.myId!=that.id){
						that.isShowRemark = true;
					}
				}
			});
			// 获取数据
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getFollowerList',
				method: "GET",
				data: {
					id: that.id
				},
				success: (res) => {
					console.log('获取粉丝列表成功' + res.data.data.followerList);
					that.followerList = res.data.data.followerList;
				},
				fail: () => {
					console.log('获取粉丝列表失败');
				}
			})

		},
		methods: {
			restaurantClick(id) {
				uni.navigateTo({
					url: "../home/homepage?id=" + id
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
