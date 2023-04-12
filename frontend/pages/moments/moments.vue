<template>
	<view class="container">
		<view class="title">
			动态
		</view>
		<view class="friendmoment">
			<uni-card v-for="(item,index) in commentList" :title="(item.remark.length==0?item.nickName:item.remark)"
				:sub-title="getTime(item.time)" :isFull="false" :extra="item.score" :thumbnail="item.avatarUrl"
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
		</view>
		<commentInput :placeholder="placeholder" :valueData="tmpCommentData" :isShow="isShowComment"
			:curKey="curCommentId" @submitComment="submitComment" @clickOther="clickOther">
		</commentInput>

		<!-- tabBar -->
		<lb-tabbar ref="tabbar" :value="active" :animate="animate" @change="handleChange">
			<!-- <lb-tabbar-item v-if="userInfo.identity == 0" v-for="item in tabbar_admin" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
			<lb-tabbar-item v-if="userInfo.identity == 1" v-for="item in tabbar_user" :key="item.name" :name="item.name"
				:icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede" icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item>
			<!-- <lb-tabbar-item v-if="userInfo.identity == 2" v-for="item in tabbar_merchant" :key="item.name"
				:name="item.name" :icon="item.icon" :dot="item.dot" :info="item.info" :raisede="item.raisede"
				icon-prefix="iconfont">
				{{ item.text }}
			</lb-tabbar-item> -->
		</lb-tabbar>
	</view>
</template>
<script>
	export default {
		data() {
			return {
				userInfo: {},
				myId: "", //我的Id
				commentList: [],
				isShowComment: false,
				placeholder: "发个友善的评论吧",
				tmpCommentData: [],
				curCommentId: '',
				curtoId: '',
				// tabBar
				active: '',
				animate: 'zoomIn',
				height: '',
				tabbarHeight: '',
				tabbar_user: [{
						name: 'index',
						text: '首页',
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'moments',
						text: '动态',
						icon: '../../static/icons/explore-active.png'
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
				]

			}
		},
		onShow() {
			wx.hideHomeButton();
		},
		onLoad() {
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: (res) => {
					that.userInfo = res.data;
					that.myId = res.data.id;
					that.refresh();
				}
			})
		},
		methods: {
			checkTime(i) {
			    if (i < 10) {
			        i = "0" + i;
			    }
			    return i;
			},
			getTime (inputTime) {
				console.log(inputTime);
			    var nowdate = new Date(inputTime*1);
				console.log(nowdate);
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
				uni.request({
					//获取动态
					url: 'http://124.71.170.100/index.php/index/getAllComment',
					method: "GET",
					data: {
						id: this.myId
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
							uni.navigateTo({
								url: '/pages/home/homepage?id=' + Id
							});
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
			onClick(e, index) {
				if (e == "title") {
					console.log(this.commentList[index].nickName + "被点击了");
					uni.navigateTo({
						url: '/pages/home/homepage?id=' + this.commentList[index].userId
					});
				}
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
					success: () => {
						console.log('取消点赞');
					}
				})
			},
			likedClick(index) {
				//点赞评论
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
					url: '/pages/home/tip-of-comment?commentId=' + this.commentList[index].commentId+ '&id=' + this.commentList[index].userId
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
				uni.navigateTo({
					url: '/pages/index/comment?commentId=' + this.commentList[index].commentId + "&isCanteen=" + this.commentList[index].isCanteen
				});
			},
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.userInfo.identity == 1) {
					// 普通用户
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
							// case 'moments':
							// 	uni.redirectTo({
							// 		url: '/pages/moments/moments'
							// 	})
							// 	break;
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
				} 
			},


		}
	}
</script>

<style>
	page {
		background-image: linear-gradient(to right bottom, #fee140, #ffda41, #ffd342, #ffcd44, #ffc646, #ffc049, #ffba4c, #ffb44f, #ffae54, #ffa759, #ffa25e, #ff9c63);
		background-size: 100% 100%;
		width: 100%;
		height: 100%;
	}

	.title {
		margin-top:50rpx;
		padding-top: 30rpx;
		font-size: 40rpx;
		font-weight: bold;
		text-align: center;
	}

	.friendmoment {
		word-break: break-all;
		margin: 30rpx;
	}

	.card-actions-item-text {
		font-size: 30rpx;
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
