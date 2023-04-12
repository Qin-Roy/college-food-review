<template>
	<view class="index">
		<view class="top">
			<view class="choiceSchool">
				<uni-combox :candidates="schools" placeholder="学校选择" v-model="selectedSchoolName"
					@input="refreshSchool()"></uni-combox>
			</view>
			<view class="toptitle">高校美食点评</view>
		</view>
		<view>
			<uni-search-bar radius="100" placeholder="搜索商家或菜品" v-model="searchValue" clearButton="auto"
				cancelButton="none" @confirm="search" />
		</view>
		<view class="front">
			<swiper v-if="activityList.length != 0" class="swiper" indicator-dots="true" autoplay="true" interval="3000"
				circular="true" indicator-active-color="white">
				<swiper-item v-for="(item,index) in activityList">
					<image class="frontImage" mode="aspectFill" @click="activityClick(index)" :src="item.picture">
					</image>
				</swiper-item>
			</swiper>
		</view>
		<view class="classification">
			<view style="display: flex;">
				<view style="width: 80%;">商家分类</view>
				<view style="width: 20%;font-size: 35rpx;" @click="seeAll()">查看全部</view>
			</view>
			<view style="padding-top: 20rpx;">
				<uni-grid :column="4" :highlight="true" :showBorder="false" @change="categoryClick">
					<uni-grid-item v-for="(item, index)  in category" :index="index" v-if="index <= 3">
						<view class="grid-item-box">
							<image :src="item.pictureUrl" class="image" mode="aspectFill" />
							<text class="text">{{item.name}}</text>
						</view>
					</uni-grid-item>
				</uni-grid>
			</view>
		</view>
		<view class="stores">
			<view style="padding-left: 20rpx;margin-bottom: 20rpx;">商家推荐</view>
			<uni-card :border="false" v-for="(item,index) in storeList" :title="item.nickName"
				:sub-title="item.categoryList" :isFull="true" :thumbnail="item.avatarUrl" @click="storeClick(index)">
			</uni-card>
		</view>
		<!-- tabBar -->
		<lb-tabbar ref="tabbar" :value="active" :animate="animate" @change="handleChange">
			<lb-tabbar-item v-if="userInfo.identity == 0" v-for="item in tabbar_admin" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<lb-tabbar-item v-if="userInfo.identity == 1" v-for="item in tabbar_user" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<lb-tabbar-item v-if="userInfo.identity == 2" v-for="item in tabbar_merchant" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
		</lb-tabbar>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				id: '',
				userInfo: {},
				searchValue: '',
				selectedSchoolName: '',
				schoolId: '',
				schools: [],
				schoolIdList: [],
				storeList: [],
				category: [],
				activityList: [],

				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_user: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank-active.png',
					},
					{
						name: 'moments',
						text: '动态',
						icon: '../../static/icons/explore.png'
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message.png',
					},
					{
						name: 'home',
						text: '我的',
						icon: '../../static/icons/homepage.png'
					}
				],
				tabbar_merchant: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank-active.png',
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message.png',
					},
					{
						name: 'homepage',
						text: '我的',
						icon: '../../static/icons/homepage.png'
					}
				],
				tabbar_admin: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank-active.png',
					},
					{
						name: 'manage',
						text: '管理',
						icon: '../../static/icons/cube.png'
					},
					{
						name: 'canteen',
						text: '食堂',
						icon: '../../static/icons/edit.png'
					}
				]

			}
		},
		onLoad() {
			let that = this;
			//请求学校列表
			uni.request({
				url: 'http://124.71.170.100/index.php/index/getSchoolList',
				method: "GET",
				header: {
					"content-type": "application/x-www-form-urlencoded",
				},
				success: (res) => {
					console.log('获取学校列表成功:' + res.data.data.schoolList);
					for (let i = 0; i < res.data.data.schoolList.length; i++) {
						this.schools[i] = res.data.data.schoolList[i].schoolName;
						this.schoolIdList[i] = res.data.data.schoolList[i].schoolId;
					}
					uni.getStorage({
						key: 'userInfo',
						success: function(res) {
							console.log(res.data);
							that.userInfo = res.data;
							that.id = res.data.id;
							that.identity = res.data.identity;
							that.schoolId = res.data.schoolId;
							that.selectedSchoolName = res.data.schoolName;
							that.refresh(res.data.schoolId);
							if (res.data.identity == "2") {
								uni.request({
									url: 'http://124.71.170.100/index.php/index/getStoreInfo',
									data: {
										id: res.data.id
									},
									method: 'GET',
									success: (res2) => {
										if ((res2.data.data.nickName == "") && (res2.data.data.address == "") &&
											(
												res2.data.data.tel == "") && (res2.data.data.intro == "")) {
											uni.showToast({
												title: "请完善商家信息",
												icon: "error",
												duration: 1000,
												mask: true
											})
											setTimeout(function() {
												uni.redirectTo({
													url: '../merchant-pages/edit-store-info'
												})
											}, 1000);
										}
									}
								})
							}
						}
					});
				}
			});
		},
		onShow() {
			wx.hideHomeButton();
		},
		onPullDownRefresh() {
			console.log('refresh');
			var that = this;
			setTimeout(function() {
				that.refresh(that.schoolId); //需要修改
				uni.stopPullDownRefresh();
			}, 1000);
		},
		methods: {
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.userInfo.identity == 0) {
					// 管理员
					switch (e.name) {
						// case 'index':
						// 	uni.redirectTo({
						// 		url: '../index/index'
						// 	})
						// 	break;
						case 'manage':
							uni.redirectTo({
								url: '../admin/manage'
							})
							break;
						case 'canteen':
							uni.redirectTo({
								url: '../canteen/canteenList'
							})
							break;
						default:
							break;
					}
				} else if (this.userInfo.identity == 1) {
					// 普通用户
					switch (e.name) {
						// case 'index':
						// 	uni.redirectTo({
						// 		url: '/pages/index/index'
						// 	})
						// 	break;
						case 'moments':
							uni.redirectTo({
								url: '/pages/moments/moments'
							})
							break;
						case 'message':
							uni.redirectTo({
								url: '/pages/message/message'
							})
							break;
						case 'home':
							uni.redirectTo({
								url: '/pages/home/my'
							})
							break;
						default:
							break;
					}
				} else if (this.userInfo.identity == 2) {
					// 商家
					switch (e.name) {
						// case 'index':
						// 	uni.redirectTo({
						// 		url: '/pages/index/index'
						// 	})
						// 	break;
						case 'message':
							uni.redirectTo({
								url: '/pages/message/message'
							})
							break;
						case 'homepage':
							uni.redirectTo({
								url: '/pages/merchant-pages/store-homepage'
							})
							break;
						default:
							break;
					}
				}
			},
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
			refreshSchool() {
				let index = 0;
				for (let i = 0; i < this.schoolIdList.length; i++) {
					if (this.selectedSchoolName == this.schools[i]) {
						index = i;
						break;
					}
				}
				console.log('所选择的学校是：' + this.schools[index]);
				this.schoolId = this.schoolIdList[index];
				//更新首页学校信息
				uni.request({
					url: 'http://124.71.170.100/index.php/index/updateSchoolInfo',
					method: "POST",
					header: {
						"content-type": "application/json",
					},
					data: {
						schoolId: this.schoolIdList[index],
						id: this.id,
					},
					success: (res) => {
						console.log("选择学校成功");
						console.log(res);
						this.userInfo.schoolId = this.schoolIdList[index];
						this.userInfo.schoolName = this.schools[index];
						uni.setStorage({
							data: this.userInfo,
							key: 'userInfo',
							success: () => {

							}
						})
						this.refresh(this.schoolIdList[index]);
						this.reload();
					}
				})
			},
			refresh(schoolId) {
				// console.log(schoolId);
				//通过学校请求商家、菜品类别及活动信息
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getStore_Act_throughSchoolid',
					method: 'GET',
					header: {
						"content-type": "application/x-www-form-urlencoded",
					},
					data: {
						schoolId: schoolId
					},
					success: (res) => {
						console.log(res);
						console.log(schoolId);
						this.storeList = res.data.data.storeList;
						this.category = res.data.data.category;
						this.activityList = res.data.data.activityList;
						console.log('刷新成功');
					},
					fail: () => {
						console.log('刷新失败');
					}
				})
			},
			seeAll() {
				console.log("查看全部");
				uni.navigateTo({
					url: '/pages/index/all-category?schoolId=' + this.schoolId
				});
			},
			categoryClick(e) {
				let {
					index
				} = e.detail
				console.log(this.category[index].name + "被点击了");
				uni.navigateTo({
					url: '/pages/index/category?categoryName=' + this.category[index].name + "&schoolId=" + this
						.schoolId
				});
			},
			search(res) {
				console.log("搜索：" + this.searchValue);
				uni.navigateTo({
					url: '/pages/index/search?res=' + this.searchValue
				});
			},
			activityClick(index) {
				console.log(this.activityList[index].activityName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/activities?activityId=' + this.activityList[index].activityId
				});
			},
			storeClick(index) {
				console.log(this.storeList[index].nickName + "被点击了");
				uni.navigateTo({
					url: '/pages/index/store?id=' + this.storeList[index].id + '&isCanteen=' + this.storeList[
						index].isCanteen
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
	.top {
		width: 100%;
		height: 70rpx;
		display: flex;
	}

	.choiceSchool {
		width: 30%;
		font-size: 25rpx;
	}

	.toptitle {
		width: 74%;
		font-size: 40rpx;
		padding-left: 45rpx;
		padding-top: 10rpx;
	}

	.swiper {
		width: 100%;
		height: 350rpx;
	}

	.frontImage {
		height: 100%;
		width: 100%;
	}

	.classification {
		margin: 20rpx;
	}

	.activity {
		margin: 0 3%;
		width: 94%;
		height: 200rpx;
	}

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
		width: 100rpx;
		height: 100rpx;
		border-radius: 50%;
	}

	.stores {
		margin-top: 30rpx;
	}
</style>
