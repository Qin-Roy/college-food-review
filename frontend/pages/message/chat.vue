<!--私聊页面-->
<template>
	<view>
		<uni-popup ref="message" type="message">
			<uni-popup-message :type="msgType" :message="messageText" :duration="1200"></uni-popup-message>
		</uni-popup>
		<view class="box-1" id="list-box">
			<view class="talk-list">
				<view v-for="(item,index) in talkList" :key="index" :id="`msg-${item.id}`">
					<view class="item flex_col" :class=" item.id == my.id ? 'push':'pull' ">
						<image :src="item.id == my.id? my.myAvatarUrl :friend.otherAvatarUrl" mode="aspectFill"
							class="pic" @click="toHomePage(item.id)"></image>
						<view class="content">{{item.content}}</view>
					</view>
				</view>
			</view>
		</view>
		<view class="box-2">
			<view class="flex_col">
				<view class="flex_grow">
					<input type="text" class="content" maxlength="100" v-model="content" placeholder="请输入聊天内容"
						placeholder-style="color:#DDD;" :cursor-spacing="6">
				</view>
				<button class="send" @tap="send">发送</button>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {

				//页面加载头部信息
				msgType: 'info',
				messageText: '聊天信息加载中',

				// 我的基本信息
				my: {
					// 	id:'20',
					// 	myNickName:"",
					// 	myAvatarUrl:'https://img0.baidu.com/it/u=1295072258,3925203567&fm=253&fmt=auto&app=138&f=JPEG?w=640&h=427'
				},

				// 对方基本信息
				friend: {
					// 	otherUserId:'1',
					// 	otherNickName:"",
					// 	otherRemark:"",
					// 	otherAvatarUrl:'https://img2.baidu.com/it/u=2630063864,152433897&fm=253&fmt=auto&app=138&f=JPEG?w=750&h=500'
					// 
				},

				// 聊天数据
				talkList: [
					// {
					// 	id:'1',
					// 	content:'1',
					// 	time:'1'
					// },
				],

				// 临时添加聊天数据
				content: ''
			}
		},
		onLoad(option) {

			//获取上个页面参数
			this.my.id = option.id;
			console.log('参数传递成功+myId:', option.id); //控制台显示结果
			this.friend.otherUserId = option.otherUserId;

			    // 获取对话信息
			    uni.request({
					url: 'http://124.71.170.100/index.php/index/getChatInfo',
					method: "GET",
					data: {
						id: option.id,
						otherUserId: option.otherUserId
					},
					success: (res) => {
						console.log('获取对话信息成功', res.data.data);
						this.my.myNickName = res.data.data.myNickName;
						this.my.myAvatarUrl = res.data.data.myAvatarUrl;
						this.friend.otherNickName = res.data.data.otherNickName;
						this.friend.otherAvatarUrl = res.data.data.otherAvatarUrl;
						this.friend.otherRemark = res.data.data.otherRemark;
					},
					fail: () => {
						console.log('获取对话信息失败');
					},
				}),

				// 获取聊天内容
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getChatRecord',
					method: "GET",
					data: {
						id: option.id,
						otherUserId: option.otherUserId
					},
					success: (res) => {
						console.log('获取对话成功');
						this.talkList = res.data.data.msgList;
						// 初入页面翻到底页
						setTimeout(() => {
							uni.pageScrollTo({
									scrollTop: 999999, // 设置一个超大值，以保证滚动条滚动到底部
									duration: 0
								}),
								this.$refs.message.open() // 聊天信息获取提示
						}, 500);
					},
					fail: () => {
						console.log('获取对话失败');
					}
				}),

				// 每隔1.5s刷新聊天记录
				this.timer = setInterval(() => {
					// 获取聊天内容
					uni.request({
						url: 'http://124.71.170.100/index.php/index/getChatRecord',
						method: "GET",
						data: {
							id: this.my.id,
							otherUserId: this.friend.otherUserId
						},
						success: (res) => {
							//console.log('更新对话成功');
							if (this.talkList.length != res.data.data.msgList.length) {
								uni.pageScrollTo({
									scrollTop: 999999, // 设置一个超大值，以保证滚动条滚动到底部
									duration: 0
								})
								this.talkList = res.data.data.msgList;
								console.log('此时更新')
							}
						},
						fail: () => {
							console.log('获取对话失败');
						}
					});
				}, 1500)
		},
		methods: {
			// 移动至聊天尾部
			down() {
				uni.pageScrollTo({
					scrollTop: 999999,
					duration: 0
				});
			},
			// 发送信息
			send() {
				// 错误处理
				if (!this.content) {
					uni.showToast({
						title: '请输入有效的内容',
						icon: 'none'
					})
					return;
				} else {
					// 显示正在发送标签
					uni.showLoading({
						title: '正在发送',
						mask: true
					})

					// 产生现在的聊天数据
					let dataTemp = {
						"id": this.my.id,
						"content": this.content,
						"time": new Date().getTime()
					}

					// 将消息传递给后端
					uni.request({
						url: 'http://124.71.170.100/index.php/index/sendMessage',
						method: "POST",
						header: {
							"content-type": "application/json"
						},
						data: {
							"id": this.my.id, //发送者ID
							"otherUserId": this.friend.otherUserId, //发送对象ID
							"content": this.content, //发送内容
							"time": new Date().getTime() //发送时间戳
						},
						success: (res) => {
							console.log('提交成功', res);
							// 清空内容框中的内容
							this.content = '';
							uni.request({
								url: 'http://124.71.170.100/index.php/index/getChatRecord',
								method: "GET",
								data: {
									id: this.my.id,
									otherUserId: this.friend.otherUserId
								},
								success: (res) => {
									//console.log('更新对话成功');
									if (this.talkList.length != res.data.data.msgList.length) {
										uni.pageScrollTo({
											scrollTop: 999999, // 设置一个超大值，以保证滚动条滚动到底部
											duration: 0
										})
										this.talkList = res.data.data.msgList;
										this.$nextTick(() => {
											uni.pageScrollTo({
												scrollTop: 999999, // 设置一个超大值，以保证滚动条滚动到底部
												duration: 0
											});
										})
										console.log('此时更新')
									}
									uni.hideLoading();
								},
								fail: () => {
									console.log('获取对话失败');
								}
							});
						},
						fail: () => {
							console.log('提交失败');
						},
					})
				}
			},
			// 点击图片跳转至对应页面
			jumpHomePage(id) {
				uni.navigateTo({
					url: '../home/homepage?id=' + id,
				});
			},
			// 时间戳转标准时间显示
			checkTime(i) {
				if (i < 10) {
					i = "0" + i;
				}
				return i;
			},
			changeTime(date) {
				var nowdate = new Date(date * 1)
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
			// 页面跳转
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
						//管理员
						if (identity == "0") {
							uni.showToast({
								icon: "none",
								mask: true,
								title: "无权进入管理员主页！",
							})
						}
						//用户
						else if (identity == "1") {
							uni.navigateTo({
								url: '/pages/home/homepage?id=' + Id
							});
						}
						//商家
						else if (identity == "2") {
							uni.navigateTo({
								url: '/pages/index/store?id=' + Id
							});
						}
					},
					fail: () => {
						console.log('获取身份失败');
					}
				});
			},
		}
	}
</script>

<style lang="scss">
	@import "../../global.scss";

	page {
		background-color: #F3F3F3;
		font-size: 28rpx;
	}

	/* 加载数据提示 */
	.tips {
		position: fixed;
		left: 0;
		top: var(--window-top);
		width: 100%;
		z-index: 9;
		background-color: rgba(0, 0, 0, 0.15);
		height: 72rpx;
		line-height: 72rpx;
		transform: translateY(-80rpx);
		transition: transform 0.3s ease-in-out 0s;

		&.show {
			transform: translateY(0);
		}
	}

	.box-1 {
		width: 100%;
		height: auto;
		padding-bottom: 100rpx;
		box-sizing: content-box;

		/* 兼容iPhoneX */
		margin-bottom: 0;
		margin-bottom: constant(safe-area-inset-bottom);
		margin-bottom: env(safe-area-inset-bottom);
	}

	.box-2 {
		position: fixed;
		left: 0;
		width: 100%;
		bottom: 0;
		height: auto;
		z-index: 2;
		border-top: #e5e5e5 solid 1px;
		box-sizing: content-box;
		background-color: #F3F3F3;

		/* 兼容iPhoneX */
		padding-bottom: 0;
		padding-bottom: constant(safe-area-inset-bottom);
		padding-bottom: env(safe-area-inset-bottom);

		>view {
			padding: 0 20rpx;
			height: 100rpx;
		}

		.content {
			background-color: #fff;
			height: 64rpx;
			padding: 0 20rpx;
			border-radius: 32rpx;
			font-size: 28rpx;
		}

		.send {
			background-image: linear-gradient(to right, #efff9e 0%, #ffec8c 51%, #ffa9a1 100%);
			color: #fff;
			height: 64rpx;
			margin-left: 20rpx;
			border-radius: 32rpx;
			padding: 0;
			width: 120rpx;
			line-height: 62rpx;

			&:active {
				background-image: linear-gradient(to right, #efff9e 0%, #ffec8c 51%, #ffa9a1 100%);
			}
		}
	}

	.talk-list {
		padding-bottom: 20rpx;

		/* 消息项，基础类 */
		.item {
			padding: 20rpx 20rpx 0 20rpx;
			align-items: flex-start;
			align-content: flex-start;
			color: #333;

			.pic {
				width: 92rpx;
				height: 92rpx;
				border-radius: 50%;
				border: #fff solid 1px;
			}

			.content {
				padding: 20rpx;
				border-radius: 4px;
				min-width: 30rpx;
				min-height: 87rpx;
				max-width: 500rpx;
				word-break: break-all;
				line-height: 52rpx;
				position: relative;
			}

			/* 收到的消息 */
			&.pull {
				.content {
					margin-left: 32rpx;
					background-color: #fff8f6;

					&::after {
						content: '';
						display: block;
						width: 0;
						height: 0;
						border-top: 16rpx solid transparent;
						border-bottom: 16rpx solid transparent;
						border-right: 20rpx solid #fff8f6;
						position: absolute;
						top: 30rpx;
						left: -18rpx;
					}
				}
			}

			/* 发出的消息 */
			&.push {
				/* 主轴为水平方向，起点在右端。使不修改DOM结构，也能改变元素排列顺序 */
				flex-direction: row-reverse;

				.content {
					margin-right: 32rpx;
					background-color: #dde9c9;

					&::after {
						content: '';
						display: block;
						width: 0;
						height: 0;
						border-top: 16rpx solid transparent;
						border-bottom: 16rpx solid transparent;
						border-left: 20rpx solid #dde9c9;
						position: absolute;
						top: 30rpx;
						right: -18rpx;
					}
				}
			}
		}
	}
</style>
