<template>
	<view class="canteenList">
		<view class="header">
			<view v-if="canteenInfo.pictureList.length != 0">
				<swiper class="swiper" indicator-dots="true" autoplay="true" interval="3000" circular="true" indicator-active-color="white">
					<swiper-item v-for="(item, index) in canteenInfo.pictureList">
						<!--No more than 3 photos-->
						<image class="headImage" mode="aspectFill" :src="item" @click="progressImgClick(canteenInfo.pictureList, index)"></image>
					</swiper-item>
					<!-- <image class="headImage" mode="aspectFill" src="../../static/uni.png" @click="progressImgClick(canteenInfo.pictureList, index)"></image> -->
				</swiper><!-- v-if="JSON.stringify(this.canteenInfo.pictureList) === '[]'" -->
			</view>
			<image v-else class="headImage" mode="aspectFill" src="../../static/null.jpg" @click="progressImgClick(canteenInfo.pictureList, index)"></image>
		</view>
		<!--Show the avatar-->
		<image class="avatar" :src="canteenInfo.avatarUrl"></image>
		<view class="canteenInfo">
			<uni-section style="font-size: 40rpx;" title="食堂信息" type="line">
				<uni-group mode="card">
					<text class="text"> 食堂名称：</text>
					<view class="info"> {{canteenInfo.nickName}} </view>
					<text class="text"> 地址：</text>
					<view class="info"> {{canteenInfo.address}} </view>
					<text class="text"> 联系方式：</text>
					<view class="info"> {{canteenInfo.tel}} </view>
					<text class="text"> 简介：</text>
					<view class="info"> {{canteenInfo.intro}} </view>
				</uni-group>
			</uni-section>
		</view>
		<view class="myButtonArea">
		    <button class="myButton" @click="editCanteen('edit-canteen-info')">点击修改信息</button>
			<button class="myButton" @click="editDishes('edit-dishes')">点击修改菜品</button>
		</view>
		
	</view>
</template>

<script>
	export default {
		data() {
			return {
				userId: "",
				current:0,
				id: "",
				avatarUrl: "",
				canteenInfo: ""
			}
		},
		onLoad(option) {
			
			console.log(option)
			this.id = option.id
			this.avatarUrl = option.url
			this.getMyInfo()
			// this.getCanteenInfo()
		},
		onShow(){
			this.getMyInfo()
			
			// this.getCanteenInfo()
		},
		methods: {
			getMyInfo() {
				let that = this
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						console.log(res)
						that.userId = res.data.id
						console.log(that.userId)
						that.getCanteenInfo()
					}
				})
			},
			getCanteenInfo() {
				console.log(this.userId)
				console.log("此时的id: " + this.id)
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getOneCanteenInfo',
					data: {
						userId: this.userId,
						canteenId: this.id
					},
					method: 'GET',
					success: (res) => {
						console.log(res.data.data)
						console.log(JSON.stringify(this.canteenInfo.pictureList) === '[]')
						this.canteenInfo = res.data.data
					}
				})
			},
			editCanteen(url) {
				uni.navigateTo({
					url: 'edit-canteen-info?id=' + this.id + '&url=' + this.avatarUrl + '&userId=' + this.userId
				})
			},
			editDishes(url) {
				uni.navigateTo({
					url: 'edit-dishes?id=' + this.id
				})
			},
			// 图片预览以及长按下载处理
			progressImgClick(file_arr, index){
				console.log(file_arr)
				console.log(index)
				uni.previewImage({
					urls: file_arr,
					current: index,
					longPressActions: {
						itemList: ['保存图片'],
						success: function(data) {
							console.log("准备保存图片" + file_arr[index])
							this.downloadImage(file_arr[index])
						}, fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			},
			downloadImage(url) {
				uni.saveImageToPhotosAlbum({
					filePath: url,
					success: function () {
						console.log('save success');
					}
				});
			}
		}
	}
</script>

<style>
	.swiper {
		width: 100%;
		height: 400rpx;
	}
	
	.headImage {
		width: 100%;
	}
	
	.avatar {
		position: relative;
		display: flex;
		margin-top: -65rpx;
		margin-right: 40rpx;
		float: right;
		width: 150rpx;
		height: 150rpx;
		border-radius: 10%;
	}
	.storeInfo {
		margin-top: 120rpx;
	}
	.text {
		font-weight: bold;
	}
	.myButtonArea{
		/* background-color: #000000; */
		/* margin-bottom: 20rpx; */
		margin-top: 20rpx;
		/*margin-bottom: 20rpx;*/
	}
	.info {
		white-space: normal;
		word-break: break-word;
		word-warp: break-word;
	}
	.myButton {
		margin: 10rpx 10rpx;
		bottom: 20rpx;
		background-color: #FFC837;
	}
</style>
