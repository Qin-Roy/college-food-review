<template>
	<!--个人主页-->
	<view class="homePage">
		<view class="header">
			<view class="head1">
				<view class="card-actions-item" @click="navigateBack('/pages/home/my')">
					<uni-icons type="back" size="30" color="#999"></uni-icons>
				</view>
				<view style="font-weight: bold; font-size: 40rpx; padding-left: 31%;" v-if="identity === 1">我的主页</view>
				<view style="font-weight: bold; font-size: 40rpx; padding-left: 31%;" v-else-if="identity === 2">Ta的主页
				</view>
			</view>
			<view class="head2">
				<view class="head2-headphoto">
					<image class="headphoto" @click="previewImg(avatarUrl)" :src="avatarUrl"></image>
					<view style="font-size: 30rpx;padding-top: 10rpx;">{{nickName}}</view>
				</view>
				<view class="head2-info">
					<view class="head2-info-top">
						<view class="follow" @click="followListClick()">
							<view>关注</view>
							<view>{{followingNum}}</view>
						</view>
						<view class="fans" @click="fansClick()">
							<view>粉丝</view>
							<view>{{followerNum}}</view>
						</view>
						<view class="favouredStore" @click="favouredStoreClick()">
							<view>收藏</view>
							<view>{{favouredStoreNum}}</view>
						</view>
					</view>
					<button class="btn change-info" style="margin-top: 15rpx;" v-if="identity === 1"
						@click="editInfo()">
						编辑资料
					</button>
					<view class="signature" v-if="(identity == 2)&&(isFriend != 0)" v-show="status">
						备注：{{remark.length == 0 ? nickName : remark}}
						<view style="color: #1B9AF4;padding-top: 10rpx;" @click="editRemark()">修改备注</view>
					</view>
					<view class="ban_TA" v-if="identity === 2" v-show="!status">
						账号封禁中！
					</view>
				</view>
			</view>
		</view>
		<view class="pointBox" v-if="(identity == 2) && (tempIdentity == 1)" v-show="status">
			<view class="friends">
				<button v-if="(isFriend == 0) && (tempIdentity==1)" class="btn buttonnew" @click="addFriend()">
					添加好友
				</button>
				<button v-else class="btn buttonnew" @click="chat()">
					聊天
				</button>
			</view>
			<view class="guanzhu">
				<button v-if="(isFollow == 0) && (tempIdentity==1)" class="btn buttonnew" @click="followClick()">
					关注
				</button>
				<button v-if="(isFollow != 0) && (tempIdentity==1)" class="btn buttonnew" @click="followClick()">
					取消关注
				</button>
			</view>
			<view class="jubao">
				<button class="btn buttonnew" @click="tipof()">
					举报
				</button>
			</view>
		</view>
		<view class="ban" v-if="identity === 1" v-show="!status">
			账号封禁中！点击申诉！
		</view>
		<view class="remark" v-show="status">
			<view v-if="identity === 1" style="font-size: 35rpx;display:flex;">
				<view style="width:67%;">我的评论</view>
				<view style="width:12%;">权限</view>
				<view style="width:15%;">
					<switch :checked="authority" @change="switchChange" />
				</view>
			</view>
			<view v-else-if="identity === 2" style="font-size: 35rpx;display:flex;">
				<view style="width:60%; margin-bottom: 10px;">Ta的评论</view>
				<view style="width:40%;" v-show="!authority">TA关闭了评论</view>
			</view>
			<view class="comment" v-show="(identity === 1)||((identity === 2)&&(authority))">
				<uni-card v-for="(item,index) in commentList" :title="(remark.length==0?nickName:remark)"
					:sub-title="getTime(item.time)" :isFull="true" :extra="item.score" :thumbnail="avatarUrl">
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
						<view style="width:17%;" @click="clickComment(item.commentId,id)">
							<uni-icons type="chatbubble" size="22" color="#999"></uni-icons>
							<text>{{item.responseNum>99 ? "99+" : item.responseNum}}</text>
						</view>

						<view style="width:10%;" v-if="identity == 1" @click="deleteClick(index)">
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
							<text @click="clickComment(item.commentId,item.firstResponse.fromId)">:{{item.firstResponse.detail}}</text>
						</view>
						<view style="display: flex;">
							<view style="color: #1B9AF4; width: 50%;">
								<text v-if="item.responseNum!=0" @click="toSpecific(index)">共{{item.responseNum}}条回复 ></text>
							</view>
							<view style="width: 50%;text-align: right;">
								<text class="overtext" @click="dishClick(index)">
									{{item.dishName}}--{{item.merchantNickName}}
								</text>
							</view>
						</view>
					</view>
				</uni-card>
				<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
			</view>
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
				myId: "", //我的Id
				id: "", //当前页面的用户Id
				tempIdentity: "",//当前页面的用户身份
				avatarUrl: "",
				nickName: "",
				identity: "", //1代表我的主页  2代表Ta的主页
				status: "", //1表示正常  0表示被禁
				authority: "", //1代表权限开放 0代表权限关闭
				followerNum: "", //粉丝
				followingNum: "", //关注用户
				favouredStoreNum: "", //关注商家
				isFriend: "", //是否为好友
				isFollow: '', //是否是粉丝
				remark: '', //备注
				followingList: [],
				commentList: [],
				favouredStoreList: [],
				followerList: [],

				isShowComment: false,
				placeholder: "发个友善的评论吧",
				tmpCommentData: [],
				curCommentId: '',
				curtoId: '',
				contenDown: {
					contentnomore: '没有更多评论啦'
				}
			}
		},
		onShow() {
			let that = this;
			that.refresh();
		},
		onLoad: function(option) { //option为object类型，会序列化上个页面传递的参数
			let that = this;
			this.id = option.id;
			console.log("当前页面用户id：" + this.id);
			that.refresh();
		},
		methods: {
			checkTime(i) {
			    if (i < 10) {
			        i = "0" + i;
			    }
			    return i;
			},
			getTime (inputTime) {
			    var nowdate = new Date(inputTime*1);
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
			    return year + "-" + month + "-" + date + " " + h +":" + m + ":" + s;
			},
			refresh() {
				let that = this;
				uni.getStorage({
					key: 'userInfo',
					success: (res) => {
						console.log(res.data);
						that.myId = res.data.id;
						// that.identity=1;
						that.identity = (that.id == that.myId) ? 1 : 2;
						if (that.identity == 1) {
							that.avatarUrl = res.data.avatarUrl;
							that.nickName = res.data.nickName;
							that.status = (res.data.status=="1")?true:false;
							that.followerNum = res.data.followerNum;
							that.followingNum = res.data.followingNum;
							that.favouredStoreNum = res.data.favouredStoreNum;
							uni.request({
								//获取粉丝数
								url: 'http://124.71.170.100/index.php/index/getFollowerNum',
								method: "GET",
								data: {
									id: that.myId,
								},
								success: (res2) => {
									that.followerNum=res2.data.data.followerNum;
									console.log(res2);
									console.log('获取粉丝成功');
								},
								fail: () => {
									console.log('获取粉丝失败');
								}
							})
						} 
						else if (that.identity == 2) {
							uni.request({
								url: 'http://124.71.170.100/index.php/index/getOtherUserInfo',
								method: "GET",
								data: {
									otherUserId: that.id,
									id: that.myId
								},
								success: (res) => {
									console.log('获取用户信息成功');
									that.avatarUrl = res.data.data.avatarUrl;
									that.nickName = res.data.data.nickName;
									that.status = (res.data.data.status=="1")?true:false;
									that.followerNum = res.data.data.followerNum;
									that.followingNum = res.data.data.followingNum;
									that.favouredStoreNum = res.data.data.favouredStoreNum;
									that.isFriend = res.data.data.isFriend;
									//that.isFriend=0;
									that.isFollow = res.data.data.isFollow;
									that.remark = res.data.data.remark;
								},
								fail: () => {
									console.log('获取用户信息失败');
								}
							});
							uni.request({
								//获取身份
								url: 'http://124.71.170.100/index.php/index/getUserIdentity',
								method: "GET",
								data: {
									id: that.myId
								},
								success: (res) => {
									console.log("获取身份成功");
									console.log(res);
									that.tempIdentity = res.data.data.identity;
									// that.tempIdentity = "2";
									console.log(that.tempIdentity);
								},
								fail: () => {
									console.log('获取身份失败');
								}
							});
						}
					}
				});
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getCommentList',
					method: "GET",
					data: {
						id: this.id,
						userId: this.myId
					},
					success: (res) => {
						console.log('获取评论成功');
						console.log(res);
						this.commentList = res.data.data.commentList;
					},
					fail: () => {
						console.log('获取评论失败');
					}
				});
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getAuthority',
					method: "GET",
					data: {
						id: this.id
					},
					success: (res) => {
						console.log('获取权限成功');
						console.log(res);
						this.authority = (res.data.data.authority=="1")?true:false;
						console.log(this.authority);
					},
					fail: () => {
						console.log('获取权限失败');
					}
				})
			},
			navigateBack(url) {
				uni.navigateBack({
					url,
				});
			},
			editRemark() {
				uni.navigateTo({
					url: '/pages/home/edit-remark?myId=' + this.myId + '&id=' + this.id + '&nickName=' + this
						.nickName + '&remark=' + this.remark
				});
			},
			followListClick() {
				uni.navigateTo({
					url: '/pages/home/follows?id=' + this.id
				});
			},
			fansClick() {
				uni.navigateTo({
					url: '/pages/home/followers?id=' + this.id
				});
			},
			favouredStoreClick() {
				uni.navigateTo({
					url: '/pages/home/love-stores?id=' + this.id
				});
			},
			editInfo() {
				uni.navigateTo({
					url: '/pages/home/edit-info?id=' + this.myId + '&avatarUrl=' + this.avatarUrl + '&nickName=' +this.nickName
				});
			},
			addFriend() {
				uni.navigateTo({
					url: '/pages/home/add-friend?myId=' + this.myId + '&id=' + this.id + '&nickName=' + this.nickName
				});
			},
			followClick() {
				if (this.isFollow == 0) {
					this.isFollow = 1;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/followUser',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							id: this.myId,
							otherUserId: this.id
						},
						success: () => {
							console.log("关注用户成功");
							this.followerNum++;
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
											favouredStoreNum: res.data.favouredStoreNum,
											followerNum: res.data.followerNum,
											followingNum: res.data.followingNum+1
										},
									})
								}
							})
						}
					})
				} else if (this.isFollow != 0) {
					this.isFollow = 0;
					uni.request({
						url: 'http://124.71.170.100/index.php/index/cancelFollowUser',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							id: this.myId,
							otherUserId: this.id
						},
						success: () => {
							console.log("取消关注");
							this.followerNum--;
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
											favouredStoreNum: res.data.favouredStoreNum,
											followerNum: res.data.followerNum,
											followingNum: res.data.followingNum-1
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
					url: '/pages/message/chat?id=' + this.myId + "&otherUserId=" + this.id
				});
			},
			tipof() {
				uni.navigateTo({
					url: '/pages/home/tip-of?id=' + this.id + "&nickName=" + this.nickName
				});
				console.log('发送id: ' + this.id)
			},
			switchChange: function(e) {
				this.authority = e.detail.value;
				var tempAuthority = (this.authority==true)?"1":"0";
				uni.request({
					url: 'http://124.71.170.100/index.php/index/authorize',
					method: "POST",
					header: {
						"content-type": "application/x-www-form-urlencoded",
					},
					data: {
						id:this.myId,
						authority: tempAuthority
					},
					success: (res) => {
						console.log(res);
						console.log(this.myId);
						console.log(tempAuthority);
					}
				})
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
						if(identity=="1"){
							if (Id != this.id) {
								uni.navigateTo({
									url: '/pages/home/homepage?id=' + Id
								});
							}
						}
						else if(identity=="2"){
							uni.navigateTo({
								url: '/pages/index/store?id=' + Id
							});
						}
						else if(identity=="0"){
							uni.showToast({
								title: '此用户为管理员',
								icon:'none',
								duration: 1500
							});
						}
					},
					fail: () => {
						console.log('获取身份失败');
					}
				});
			},
			dishClick(index) {
				uni.navigateTo({
					url: '/pages/index/dish?dishId=' + this.commentList[index].dishId + "&isCanteen=" + this.commentList[index].isCanteen
				});
			},
			hasLikedClick(index) {
				// 取消点赞评论
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
					success: () => {
						console.log('取消点赞');
					}
				})
			},
			likedClick(index) {
				// 点赞评论
				this.commentList[index].isLiked = 1;
				this.commentList[index].likedNum++;
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
					success: () => {
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
					header:{
						"content-type":"application/json"
					},
					data: {
						commentId: this.curCommentId,
						fromId: this.myId,
						detail: e,
						toId: this.curtoId
					},
					success: () => {
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
					url: '/pages/home/tip-of-comment?commentId=' + this.commentList[index].commentId
				});
			},
			deleteClick(index) {
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
								}
							})
							this.refresh();
						} else if (res.cancel) {
							console.log('取消删除');
						}
					}
				});
			},
			toSpecific(index) {
				console.log(index);
				console.log(this.commentList[index].commentId);
				uni.navigateTo({
					url: '/pages/index/comment?commentId=' + this.commentList[index].commentId+ "&isCanteen=" + this.commentList[index].isCanteen
				});
			},
			previewImg(logourl) {
				let _this = this;
				let imgsArray = [];
				imgsArray[0] = logourl
				uni.previewImage({
					current: 0,
					urls: imgsArray
				});
			},
		}
	}
</script>


<style>
	.homePage {
		background: #f6f6f6;
	}

	.header {
		padding-top: 60rpx;
		max-width: 100%;
		height: 430rpx;
		background-size: cover;
		border-bottom-left-radius: 40rpx;
		border-bottom-right-radius: 40rpx;
		background-image: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
		background-position: bottom;
		background-repeat: no-repeat;
	}

	.head1 {
		width: 100%;
		height: 100rpx;
		display: flex;
		align-items: center;
	}

	.back {
		width: 60rpx;
		height: 60rpx;
		padding-left: 20rpx;
	}

	.head2 {
		height: 300rpx;
		text-align: center;
		display: flex;
		align-items: center;
		padding-top: 20rpx;
	}

	.head2-headphoto {
		width: 30%;
		height: 300rpx;
		padding-left: 40rpx;
	}

	.headphoto {
		width: 180rpx;
		height: 180rpx;
		border-radius: 50%;
		border: 3px solid #fff;
	}

	.head2-info {
		font-size: 30rpx;
		width: 70%;
		height: 300rpx;
	}

	.head2-info-top {
		height: 30%;
		width: 100%;
		display: flex;
		padding-left: 40rpx;
		padding-top: 40rpx;
	}

	.follow {
		width: 27%;
		text-align: center;
		align-items: center;
	}

	.fans {
		width: 27%;
		text-align: center;
		align-items: center;
	}

	.favouredStore {
		width: 27%;
		text-align: center;
		align-items: center;
	}

	.signature {
		padding-right: 50rpx;
		padding-left: 50rpx;
		padding-top: 20rpx;
	}

	.change-info {
		border-radius: 10%;
		width: 45vw;
		font-size: 30rpx;
	}

	.pointBox {
		width: 94%;
		height: 160rpx;
		margin: 0 3%;
		background: #f6feff;
		border-radius: 40rpx;
		/* border: 2px solid #ffffff; */
		margin-top: -35rpx;
		display: flex;
		text-align: center;
		align-items: center;
	}

	.friends {
		width: 33%;
	}

	.guanzhu {
		width: 33%;
	}

	.jubao {
		width: 33%;
	}

	.buttonnew {
		align-items: center;
		border-radius: 10%;
		font-size: 30rpx;
		width: 25vw;
		height: 12vw;
		border: 2px solid #fff;
	}

	.ban {
		color: red;
		font-size: 40rpx;
		width: 94%;
		height: 120rpx;
		margin: 0 3%;
		background: #f6feff;
		border-radius: 40rpx;
		border: 2px solid #ffffff;
		margin-top: -35rpx;
		text-align: center;
		padding-top: 60rpx;
	}

	.ban_TA {
		font-size: 30rpx;
		padding-right: 50rpx;
		padding-left: 50rpx;
		padding-top: 20rpx;
		color: red;
	}

	.remark {
		margin: 0 3%;
		background: #ffffff;
		border-radius: 40rpx;
		border: 2px solid #fff;
		padding: 20rpx;
	}

	.comment {
		word-break: break-all;
	}

	.overtext {
		color: #ffaaed;
		overflow: hidden;
		text-overflow: ellipsis;
		text-overflow: ellipsis;
		/* 超出部分省略号 */
		display: -webkit-box;
		/** 对象作为伸缩盒子模型显示 **/
		-webkit-box-orient: vertical;
		/** 设置或检索伸缩盒对象的子元素的排列方式 **/
		-webkit-line-clamp: 1;
		/** 显示的行数 **/
	}
</style>
