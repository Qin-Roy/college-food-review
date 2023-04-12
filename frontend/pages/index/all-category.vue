<template>
	<view>
		<uni-grid :column="3" :highlight="true" :showBorder="false" @change="categoryClick">
			<uni-grid-item v-for="(item, index)  in category" :index="index">
				<view class="grid-item-box">
					<image :src="item.pictureUrl" class="image" mode="aspectFill" />
					<text class="text">{{item.name}}</text>
				</view>
			</uni-grid-item>
		</uni-grid>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				schoolId: '',
				category: [],
			}
		},
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.schoolId=option.schoolId;
			uni.request({
				//获取所有类别
				url: 'http://124.71.170.100/index.php/index/getStore_Act_throughSchoolid',
				method: 'GET',
				header: {
					"content-type": "application/x-www-form-urlencoded",
				},
				data: {
					schoolId: this.schoolId
				},
				success: (res) => {
					this.category = res.data.data.category;
					console.log('获取类别成功');
				},
				fail: () => {
					console.log('获取类别失败');
				}
			})
		},
		methods: {
			categoryClick(e) {
				let {
					index
				} = e.detail
				console.log(this.category[index].name + "被点击了");
				uni.navigateTo({
					url: '/pages/index/category?categoryName=' + this.category[index].name +"&schoolId="+this.schoolId
				});
			},
		}
	}
</script>

<style>
	.grid-item-box {
		flex: 1;
		// position: relative;
		/* #ifndef APP-NVUE */
		display: flex;
		/* #endif */
		flex-direction: column;
		align-items: center;
		justify-content: center;
		padding: 15px 0;
	}
	
	.text {
		font-size: 30rpx;
	}
	.image {
		width: 130rpx;
		height: 130rpx;
		border-radius: 50%;
	}
</style>
