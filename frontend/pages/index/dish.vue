<template>
	<view class="dish">
		<view class="front">
			<swiper v-if="pictureList.length" class="swiper" indicator-dots="true" autoplay="true" interval="3000"
				circular="true" indicator-active-color="white">
				<swiper-item v-for="item in pictureList">
					<image class="frontImage" mode="aspectFill" @click="progressImgClick(pictureList)" :src="item">
					</image>
				</swiper-item>
			</swiper>
			<image v-else mode="aspectFill" class="frontImage" @click="progressImgClick(pictureList)"
				src="../../static/null.jpg"></image>
		</view>
		<view class="dishInfo">
			<uni-group>
				<view style="display: flex;">
					<view class="dishName"> {{dishName}} </view>
					<view class="price"> ￥{{price}} </view>
				</view>
				<view style="word-break: break-all;" class="introduction"> {{intro}} </view>
			</uni-group>
		</view>
		<view class="comment">
			<view style="font-size: 35rpx;padding-top: 10rpx;display:flex;margin-bottom: 10rpx;">
				<view style="width: 70%;padding-left: 20rpx;">菜品评价(评分：{{score}})</view>
				<view style="width: 30%;" v-if="identity==1">
					评论
					<uni-icons type="chatboxes" size="30" @click="commentClick()"></uni-icons>
				</view>
			</view>
			<uni-card v-for="(item,index) in commentList" :title="(item.remark.length==0?item.nickName:item.remark)"
				:sub-title="getTime(item.time)" :isFull="true" :extra="item.score" :thumbnail="item.avatarUrl"
				@click="onClick($event,index)">
				<view @click="toSpecific(index)">
					<text style="font-size: 30rpx;">{{item.content}}</text>
				</view>
				<view style="display:flex;">
					<view @click="toSpecific(index)" style="width:50%;"></view>
					<view style="width:17%;" v-if="item.isLiked==1" @click="hasLikedClick(index)" color="#ff0000">
						<uni-icons type="heart-filled" size="22" color="#999"></uni-icons>
						<text>{{item.likedNum>99 ? "99+" : item.likedNum}}</text>
					</view>
					<view style="width:17%;" v-else @click="likedClick(index)">
						<uni-icons type="heart" size="22" color="#999"></uni-icons>
						<text>{{item.likedNum>99 ? "99+" : item.likedNum}}</text>
					</view>
					<view style="width:17%;" @click="clickComment(item.commentId,item.userId)">
						<uni-icons type="chatbubble" size="22" color="#999"></uni-icons>
						<text>{{item.responseNum>99 ? "99+" : item.responseNum}}</text>
					</view>

					<view style="width:10%;" v-if="myId == item.userId" @click="deleteClick(index)">
						<uni-icons type="trash" size="22" color="#999"></uni-icons>
					</view>
					<!-- <view style="width:10%;" v-if="1" @click="deleteClick(index)">
						<uni-icons type="trash" size="22" color="#999"></uni-icons>
					</view> -->
					<view style="width:10%;" v-else @click="tipofClick(index)">
						<uni-icons type="info" size="22" color="#999"></uni-icons>
					</view>
				</view>
				<view class="response">
					<view v-if="item.responseNum!=0">
						<text style="font-weight: 700;"
							@click="toHomePage(item.firstResponse.fromId)">{{item.firstResponse.fromRemark==0?item.firstResponse.fromNickName :item.firstResponse.fromRemark}}</text>
						<text
							@click="clickComment(item.commentId,item.firstResponse.fromId)">:{{item.firstResponse.detail}}</text>
						<view style="color: #1B9AF4;">
							<text @click="toSpecific(index)">共{{item.responseNum}}条回复 ></text>
						</view>
					</view>
				</view>
			</uni-card>
		</view>
		<commentInput :placeholder="placeholder" :valueData="tmpCommentData" :isShow="isShowComment"
			:curKey="curCommentId" @submitComment="submitComment" @clickOther="clickOther">
		</commentInput>
	</view>
</template>

<script>
	export default {

		data() {
			return {
				current: 0,
				onLoadFlag: "false",
				myId: "", //我的Id
				identity: "",
				dishId: "",
				dishName: "",
				intro: "",
				score: "",
				price: "",
				isCanteen: "",
				pictureList: [],
				commentList: [],

				isShowComment: false,
				placeholder: "发个友善的评论吧",
				tmpCommentData: [],
				curCommentId: '',
				curtoId: '',
			}
		},
		onLoad: function(option) { //option为object类型，会序列化上个页面传递的参数
			this.dishId = option.dishId;
			this.isCanteen = option.isCanteen;
		},
		onShow() {
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					console.log(res.data);
					//that.userInfo = res.data;
					that.myId = res.data.id;
					that.identity = res.data.identity;
					console.log("菜品id：" + that.dishId);
					that.refresh();
				}
			});
		},
		methods: {
			checkTime(i) {
				if (i < 10) {
					i = "0" + i;
				}
				return i;
			},
			getTime(inputTime) {
				var nowdate = new Date(inputTime * 1);
			 var year = nowdate.getFullYear(),
					month = nowdate.getMonth() + 1,
					date = nowdate.getDate(),
					day = nowdate.getDay(),
					h = nowdate.getHours(),
					m = nowdate.getMinutes(),
					s = nowdate.getSeconds(),
					h = this.checkTime(h),
					m = this.checkTime(m),
					s = this.checkTime(s);
				return year + "-" + month + "-" + date + " " + h + ":" + m + ":" + s;
			},
			refresh() {
				let that = this;
				uni.request({
					//获取某一菜品信息
					url: 'http://124.71.170.100/index.php/index/getMerchantDishes',
					method: "GET",
					data: {
						dishId: this.dishId,
						userId: this.myId,
						isCanteen: this.isCanteen,
					},
					success: (res) => {
						console.log(res.data.data)
						that.dishName = res.data.data.dishName;
						that.intro = res.data.data.intro;
						if (res.data.data.score == -1) {
							that.score = "暂无评价";
						} else {
							that.score = res.data.data.score;
						}
						that.price = res.data.data.price;
						that.pictureList = res.data.data.pictureList;
						that.commentList = res.data.data.commentList;
						console.log('刷新成功');
					},
					fail: () => {
						console.log('刷新失败');
					}
				})
			},
			onClick(e, index) {
				if (e == "title") {
					console.log(this.commentList[index].nickName + "被点击了");
					var identity;
					uni.request({
						//获取身份
						url: 'http://124.71.170.100/index.php/index/getUserIdentity',
						method: "GET",
						data: {
							id: this.commentList[index].userId
						},
						success: (res) => {
							console.log(res);
							identity = res.data.data.identity;
							console.log(identity);
							// identity=2;
							if (identity == "1") {
								uni.navigateTo({
									url: '/pages/home/homepage?id=' + this.commentList[index].userId
								});
							} else if (identity == "2") {
								uni.navigateTo({
									url: '/pages/index/store?id=' + this.commentList[index].userId
								});
							}
						},
						fail: () => {
							console.log('获取身份失败');
						}
					});
				}
			},
			commentClick() {
				uni.navigateTo({
					url: '/pages/index/write-comment?dishId=' + this.dishId + "&isCanteen=" + this.isCanteen
				});
			},
			toHomePage(Id) {
				console.log("Id:" + Id);
				var identity;
				uni.request({
					//获取身份
					url: 'http://124.71.170.100/index.php/index/getUserIdentity',
					method: "GET",
					data: {
						id: Id
					},
					success: (res) => {
						console.log(res);
						identity = res.data.data.identity;
						console.log(identity);
						//identity=2;
						if (identity == "1") {
							uni.navigateTo({
								url: '/pages/home/homepage?id=' + Id
							});
						} else if (identity == "2") {
							uni.navigateTo({
								url: '/pages/index/store?id=' + Id
							});
						} else if (identity == "0") {
							uni.showToast({
								title: '此用户为管理员',
								icon: 'none',
								duration: 1500
							});
						}
					},
					fail: () => {
						console.log('获取身份失败');
					}
				});
			},
			hasLikedClick(index) {
				//取消点赞评论
				this.commentList[index].isLiked = 0;
				this.commentList[index].likedNum--;
				uni.request({
					url: 'http://124.71.170.100/index.php/index/likeComment',
					method: "POST",
					header: {
						"content-type": "application/w-xxx-form-unlencoded"
					},
					data: {
						commentId: this.commentList[index].commentId,
						id: this.myId
					},
					success: (res) => {
						console.log(res)
						console.log('取消点赞');
					}
				})
			},
			likedClick(index) {
				//点赞评论
				this.commentList[index].isLiked = 1;
				this.commentList[index].likedNum++;
				console.log(this.commentList[index].commentId);
				console.log(this.myId);
				uni.request({
					url: 'http://124.71.170.100/index.php/index/likeComment',
					method: "POST",
					header: {
						"content-type": "application/json"
					},
					data: {
						commentId: this.commentList[index].commentId,
						id: this.myId
					},
					success: (res) => {
						console.log(res);
						console.log('点赞成功');
					}
				})
			},

			// 点击评论触发评论输入框
			clickComment(commentId, toId) {
				this.isShowComment = true;
				this.curCommentId = commentId;
				this.curtoId = toId;
			},
			// 提交评论并清除记忆
			submitComment(e) {
				//回复评论
				uni.request({
					url: 'http://124.71.170.100/index.php/index/replyToComment',
					method: "POST",
					header: {
						"content-type": "application/json"
					},
					data: {
						commentId: this.curCommentId,
						fromId: this.myId,
						detail: e,
						toId: this.curtoId
					},
					success: (res) => {
						console.log(res)
						console.log(this.curCommentId);
						console.log(this.myId);
						console.log(e);
						console.log(this.curtoId);
						this.isShowComment = false;
						this.refresh();
					}
				})
			},
			// 点击空白返回并保存记忆
			clickOther(e) {
				this.isShowComment = false;
			},

			tipofClick(index) {
				console.log("举报");
				uni.navigateTo({
					url: '/pages/home/tip-of-comment?commentId=' + this.commentList[index].commentId + '&id=' +
						this.userId
				});
			},
			deleteClick(index) {
				//撤回评论
				uni.showModal({
					title: '确认撤回该评论？',
					success: (res) => {
						if (res.confirm) {
							uni.request({
								url: 'http://124.71.170.100/index.php/index/deleteComment',
								method: "POST",
								header: {
									"content-type": "application/x-www-form-urlencoded",
								},
								data: {
									commentId: this.commentList[index].commentId,
									id: this.myId,
								},
								success: () => {
									console.log('删除成功,评论Id为' + this.commentList[index].commentId);
									this.refresh();
								}
							})
						} else if (res.cancel) {
							console.log('取消删除');
						}
					}
				});
			},
			toSpecific(index) {
				uni.navigateTo({
					url: '/pages/index/comment?commentId=' + this.commentList[index].commentId + "&isCanteen=" +
						this.isCanteen
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
	.swiper {
		width: 100%;
		height: 350rpx;
	}

	.frontImage {
		width: 100%;
	}

	.dishInfo {
		font-size: 40rpx;
	}

	.dishName {
		width: 80%;
	}

	.price {
		width: 20%;
		font-size: 35rpx;
	}

	.introduction {
		font-size: 25rpx;
	}

	.comment {
		word-break: break-all;
	}
</style>
