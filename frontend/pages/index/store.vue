<template>
	<view class="store">
		<view class="front">
			<view v-if="pictureList.length != 0 && merchantStatus == 1">
				<swiper class="swiper" indicator-dots="true" autoplay="true" interval="3000" circular="true"
					indicator-active-color="white">
					<swiper-item v-for="item in pictureList">
						<image mode="scaleToFill" class="frontImage" @click="progressImgClick(pictureList)" :src="item">
						</image>
					</swiper-item>
				</swiper>
			</view>
			<image v-else mode="aspectFill" class="frontImage" @click="progressImgClick(pictureList)"
				src="../../static/null.jpg"></image>
		</view>
		<button class="updateDish" v-if="isCanteen == 1" @click="updateDish()">
			申请更新菜品
		</button>
		<view style="display: flex;margin-top: 10rpx;" v-if="(merchantStatus!=0)&&(isCanteen==0)&&(userId!=id)">
			<button class="mybutton" v-if="isFavoured == 0" @click="collectStore()">
				关注
			</button>
			<button class="mybutton" v-if="isFavoured != 0" @click="collectStore()">
				取消关注
			</button>
			<button class="mybutton" @click="chat()">
				聊天
			</button>
			<button class="mybutton" @click="tipof()">
				举报
			</button>
		</view>
		<view class="storeInfo">
			<uni-section style="font-size: 40rpx;" title="商家信息" type="line" v-if="isCanteen == 0">
				<uni-group mode="card">
					<text v-if="merchantStatus==0" style="color: red;">此商家被封禁！</text>
					<view v-if="merchantStatus==1" class="info">商家名称：{{nickName}}</view>
					<view v-if="merchantStatus==1" class="info">地址：{{address}}</view>
					<view v-if="merchantStatus==1" class="info">联系方式：{{tel}}</view>
					<view v-if="merchantStatus==1" class="info">简介：{{intro}}</view>
					<text v-if="merchantStatus==1" v-for="(item,index) in categoryList"
						style="color:cornflowerblue; font-size: 30rpx;padding-right: 20rpx;">
						#{{item}}&ensp;
					</text>
				</uni-group>
			</uni-section>
			<uni-section style="font-size: 40rpx;" title="食堂信息" type="line" v-else-if="isCanteen != 0">
				<uni-group mode="card">
					<view class="info"> 食堂名称：{{nickName}} </view>
					<view class="info"> 地址：{{address}} </view>
					<view class="info"> 联系方式：{{tel}} </view>
					<view class="info"> 简介：{{intro}} </view>
					<text v-for="(item,index) in categoryList"
						style="color:cornflowerblue; font-size: 30rpx;padding-right: 20rpx;">
						#{{item}}&ensp;
					</text>
				</uni-group>
			</uni-section>
		</view>
		<view v-if="(activityList.length != 0)&&(merchantStatus!=0)">
			<view style="padding: 15rpx;">活动</view>
			<swiper class="activity" indicator-dots="true" interval="3000" circular="true"
				indicator-active-color="black">
				<swiper-item v-for="(item,index) in activityList">
					<image class="frontImage" mode="aspectFit" @click="activityClick(index)" :src="item.picture">
					</image>
				</swiper-item>
			</swiper>
		</view>
		<view v-if="merchantStatus!=0">
			<uni-section title="菜品" type="line">
				<uni-card :border="false" v-for="(item,index) in dishList" :title="item.dishName"
					:sub-title="item.intro" :isFull="true" :extra="item.price" :thumbnail="item.picture"
					@click="dishClick(index)">
				</uni-card>
			</uni-section>
			<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				contenDown: {
					contentnomore: '没有更多菜品啦'
				},
				current: 0,
				userId: "", //我的Id
				isCanteen: "",
				id: "", //商家Id
				nickName: "",
				address: "",
				merchantStatus: "",
				tel: "",
				intro: "",
				activityList: [],
				categoryList: [],
				pictureList: [],
				dishList: [],
				isFavoured: "",
			}
		},
		onLoad: function(option) { //option为object类型，会序列化上个页面传递的参数
			let that = this;
			this.id = option.id;
			console.log("商家id：" + this.id);
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					that.userId = res.data.id;
					if (option.isCanteen == "1") {
						uni.request({
							//获取食堂信息
							url: 'http://124.71.170.100/index.php/index/getOneCanteenInfo',
							method: "GET",
							data: {
								canteenId: that.id,
								userId: that.userId,
							},
							success: (res2) => {
								console.log(res2);
								that.isCanteen = res2.data.data.isCanteen;
								//this.isCanteen=1;
								that.isFavoured = res2.data.data.isFavoured;
								that.merchantStatus = "1";
								//this.isFavoured=0;
								that.nickName = res2.data.data.nickName;
								that.address = res2.data.data.address;
								that.tel = res2.data.data.tel;
								that.intro = res2.data.data.intro;
								that.activityList = res2.data.data.activityList;
								that.pictureList = res2.data.data.pictureList;
								that.dishList = res2.data.data.dishList;
								that.setCategory(res2)
							},
							fail: () => {
								console.log('获取食堂失败');
							}
						})
					} else {
						uni.request({
							//获取商家信息
							url: 'http://124.71.170.100/index.php/index/getMerchant',
							method: "GET",
							data: {
								merchantId: that.id,
								userId: that.userId,
							},
							success: (res2) => {
								console.log(res2);
								that.merchantStatus = res2.data.data.status;
								// that.merchantStatus="1";
								// that.isCanteen = res2.data.data.isCanteen;
								that.isCanteen = "0";
								that.isFavoured = res2.data.data.isFavoured;
								// that.isFavoured="0";
								that.nickName = res2.data.data.nickName;
								that.address = res2.data.data.address;
								that.tel = res2.data.data.tel;
								that.intro = res2.data.data.intro;
								that.activityList = res2.data.data.activityList;
								that.pictureList = res2.data.data.pictureList;
								that.categoryList = res2.data.data.categoryList;
								that.dishList = res2.data.data.dishList;
							},
							fail: () => {
								console.log('获取商家失败');
							}
						})
					}
				}
			});
		},
		methods: {
			setCategory(ref) {
				let that = this
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getCategory',
					method: 'GET',
					success: (res) => {
						console.log(res.data.data.TotalCategoryList)
						let TotalCategoryList = res.data.data.TotalCategoryList
						// that.categoryList = ref.data.data.categoryList;

						for (let i = 0; i < ref.data.data.categoryList.length; i++) {
							// 为了使得前后端categoryList数据相对应 要减区2
							that.categoryList.push(TotalCategoryList[Number(ref.data.data.categoryList[i]) -
								2])
						}

					}
				})
			},
			dishClick(index) {
				console.log(this.dishList[index].dishName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/dish?dishId=' + this.dishList[index].dishId + "&isCanteen=" + this.isCanteen
				});
			},
			activityClick(index) {
				console.log(this.activityList[index].activityName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/activities?activityId=' + this.activityList[index].activityId
				});
			},
			updateDish() {
				uni.navigateTo({
					url: '/pages/index/update-dish?merchantId=' + this.id
				});
			},
			collectStore() {
				//收藏商家
				if (this.isFavoured == 0) {
					this.isFavoured = 1;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/favourStore',
						method: "POST",
						header: {
							"content-type": "application/x-www-form-urlencoded",
						},
						data: {
							storeId: this.id,
							id: this.userId,
						},
						success: (res) => {
							console.log("关注商家");
							console.log(res);
							console.log(this.id);
							console.log(this.userId);
							uni.getStorage({
								key: 'userInfo',
								success: (res) => {
									uni.setStorage({
										key: 'userInfo',
										data: {
											id: res.data.id,
											nickName: res.data.nickName,
											identity: res.data.identity,
											status: res.data.status,
											schoolName: res.data.schoolName,
											schoolId: res.data.schoolId,
											avatarUrl: res.data.avatarUrl,
											favouredStoreNum: res.data.favouredStoreNum +
												1,
											followerNum: res.data.followerNum,
											followingNum: res.data.followingNum
										},
									})
								}
							})
						}
					})
				}
				//取消收藏商家
				else if (this.isFavoured != 0) {
					this.isFavoured = 0;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/cancelFavourStore',
						method: "POST",
						header: {
							"content-type": "application/x-www-form-urlencoded",
						},
						data: {
							storeId: this.id,
							id: this.userId
						},
						success: () => {
							console.log("取消关注");
							uni.getStorage({
								key: 'userInfo',
								success: (res) => {
									uni.setStorage({
										key: 'userInfo',
										data: {
											id: res.data.id,
											nickName: res.data.nickName,
											identity: res.data.identity,
											status: res.data.status,
											schoolName: res.data.schoolName,
											schoolId: res.data.schoolId,
											avatarUrl: res.data.avatarUrl,
											favouredStoreNum: res.data.favouredStoreNum -
												1,
											followerNum: res.data.followerNum,
											followingNum: res.data.followingNum
										},
									})
								}
							})
						}
					})
				}
			},
			chat() {
				uni.navigateTo({
					url: '/pages/message/chat?id=' + this.userId + "&otherUserId=" + this.id
				});
			},
			tipof() {
				uni.navigateTo({
					url: '/pages/home/tip-of?id=' + this.id + "&nickName=" + this.nickName
				});
			},
			// 图片预览以及长按下载处理
			progressImgClick(file_arr) {
				let that = this;
				// 图片预览
				uni.previewImage({
					urls: file_arr,
					// 长按图片处理
					longPressActions: {
						itemList: ['保存图片'],
						success: function(data) {
							// 成功预览后下载图片
							uni.downloadFile({
								// 需要下载的图片路径
								url: that.progress_imgs[data.index],
								success: (res) => {
									if (res.statusCode === 200) {
										// 调用图片保存到相册
										uni.saveImageToPhotosAlbum({
											filePath: res.tempFilePath,
											success: function() {
												//uniapp提供的消息提示框。
												uni.showToast({
													title: "保存成功",
													icon: "success"
												});
											},
											fail: function() {
												uni.showToast({
													title: "保存失败，请稍后重试",
													icon: "none"
												});
											}
										});
									}
								}
							})
						},
						fail: function(err) {
							console.log(err.errMsg);
						}
					}
				});
			}

		}
	}
</script>

<style>
	.info {
		white-space: normal;
		word-break: break-word;
		word-warp: break-word;
	}

	.swiper {
		width: 100%;
		height: 350rpx;
	}

	.frontImage {
		width: 100%;
	}

	.activity {
		margin: 0 3%;
		width: 94%;
		height: 200rpx;
	}

	.mybutton {
		width: 27vw;
		font-size: 30rpx;
		margin-top: 10rpx;
		margin-bottom: 10rpx;
	}

	.updateDish {
		width: 80vw;
		font-size: 30rpx;
		margin-top: 20rpx;
		margin-bottom: 10rpx;
	}
</style>
