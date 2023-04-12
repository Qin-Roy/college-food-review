<!--消息页面-->
<template>
	<view class="messagePage">

		<!-- 系统消息 -->
		<view class="topArea">系统消息</view>
		<uni-list border="true">
			<uni-list-chat clickable="true" avatar-circle="true" v-for="(item,index) in systemMessage"
				:title="item.remark==''?item.nickName:item.remark" 
				:avatar="item.avatarUrl"
				@click="systemClick(index)">
			</uni-list-chat>
		</uni-list>

		<!--系统消息尾部提醒-->
		<view v-show='isShowContent'>
			<uni-load-more status="noMore" :contentText="contentSystem"></uni-load-more>
		</view>

		<!-- 消息列表 -->
		<view class="topArea">我的对话</view>
		<uni-list border="true">
			<uni-list-chat clickable="true" avatar-circle="true" v-for="(item,index) in messageList"
				:title="item.remark=='' ? item.nickName:item.remark" 
				:avatar="item.avatarUrl"
				:note="item.lastChatContent" :time="item.lastChatTime" badge-positon="left" @click="chatClick(item)">
			</uni-list-chat>
		</uni-list>
		<uni-load-more status="noMore" :contentText="contentDown"></uni-load-more>

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
				userInfo: {},
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
						icon: '../../static/icons/explore.png'
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message-active.png',
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
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'message',
						text: '消息',
						icon: '../../static/icons/message-active.png',
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
						icon: '../../static/icons/rank.png',
					},
					{
						name: 'manage',
						text: '管理',
						icon: '../../static/icons/cube-active.png'
					},
					{
						name: 'canteen',
						text: '食堂',
						icon: '../../static/icons/edit.png'
					}
				],

				// pageData

				//我的信息
				myInfo: {
					myId: "50",
				},
				//系统消息的ID-->需要提前假定
				systemId: '20',
				isShowContent: false,
				contentSystem: {
					contentnomore: '快去添加系统消息吧'
				},
				//对话消息
				messageList: [
				// {
				// 	otherUserId: '1',
				// 	nickName: '系统消息',
				// 	remark: "瓜皮",
				// 	avatarUrl: 'https://img0.baidu.com/it/u=1295072258,3925203567&fm=253&fmt=auto&app=138&f=JPEG?w=640&h=427',
				// 	lastChatTime: "2022.5.22",
				// 	lastChatContent: "⚽",
				// },
				],
				contentDown: {
					contentnomore: '没有更多对话啦'
				},
				//系统消息
				systemMessage: [{
						otherUserId: '1',
						nickName: '活动通知',
						remark: "活动通知",
						avatarUrl: '../../../../static/icons/activitySystem.jpg',
						lastChatTime: "2022.5.22",
						lastChatContent: "🏀",
					}, {
						otherUserId: '1',
						nickName: '好友申请通知',
						remark: "好友申请通知",
						avatarUrl: '../../../../static/icons/friendSystem.jpg',
						lastChatTime: "2022.5.22",
						lastChatContent: "🏸",
					},
					{
						otherUserId: '1',
						nickName: '申诉通知',
						remark: "申诉通知",
						avatarUrl: '../../../../static/icons/appealSystem.jpg',
						lastChatTime: "2022.5.22",
						lastChatContent: "🏓",
					},
					// {
					// 	otherUserId: '1',
					// 	nickName: '举报通知',
					// 	remark: "举报通知",
					// 	avatarUrl: 'https://img0.baidu.com/it/u=1295072258,3925203567&fm=253&fmt=auto&app=138&f=JPEG?w=640&h=427',
					// 	lastChatTime: "2022.5.22",
					// 	lastChatContent: "⚽",
					// }
				]

			}
		},
		// onShow() {
		// 	wx.hideHomeButton();
		// },
		onShow() {
			wx.hideHomeButton();
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success(res) {
					that.userInfo = res.data
					that.myInfo.myId = res.data.id
					// 获取对话信息
					uni.request({
						url: 'http://124.71.170.100/index.php/index/getChatList',
						method: "GET",
						data: {
							id: that.myInfo.myId
						},
						success: (res) => {
							console.log('获取对话信息成功' + res.data.data);
							that.messageList = res.data.data.messageList;
							for(let i=0; i<that.messageList.length;i++){
								that.messageList[i].lastChatTime = that.changeTime(that.messageList[i].lastChatTime);
							}
						},
						fail: () => {
							console.log('获取对话信息失败');
						}
					})
				}
			})
		},
		methods: {
			// tabBar跳转
			handleChange(e) {
				console.log('change::', e)
				if (this.userInfo.identity == 0) {
					// 管理员
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '../index/index'
							})
							break;
						case 'moments':
							uni.redirectTo({
								url: '../moments/moments'
							})
							break;
						// case 'message':
						// 	uni.redirectTo({
						// 		url: '../message/message'
						// 	})
						// 	break;
						case 'home':
							uni.redirectTo({
								url: '../home/my'
							})
							break;
						default:
							break;
					}
				} else if (this.userInfo.identity == 1) {
					// 普通用户
					switch (e.name) {
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
						case 'moments':
							uni.redirectTo({
								url: '/pages/moments/moments'
							})
							break;
						// case 'message':
						// 	uni.redirectTo({
						// 		url: '/pages/message/message'
						// 	})
						// 	break;
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
						case 'index':
							uni.redirectTo({
								url: '/pages/index/index'
							})
							break;
						// case 'message':
						// 	uni.redirectTo({
						// 		url: '/pages/message/message'
						// 	})
						// 	break;
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
			// 聊天页面跳转
			chatClick(item) {
				console.log('跳转至聊天界面');
				//页面跳转并传递参数
				uni.navigateTo({
					url: '/pages/message/chat?id=' + this.myInfo.myId + '&otherUserId=' + item.otherUserId
				});
			},
			// 系统通知页面跳转
			systemClick(index) {
				console.log('跳转至系统通知界面');
				//页面跳转并传递参数
				uni.navigateTo({
					url: '/pages/message/chat-system?id=' + this.myInfo.myId + '&pattern=' + index
				});
			},
			// 时间戳转标准时间显示
			checkTime(i) {
			    if (i < 10) {
			        i = "0" + i;
			    }
			    return i;
			},
			changeTime (date) {
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
			    return year + "-" + month + "-" + date + " " + h +":" + m + ":" + s;
			},
		}
	}
</script>

<style>
	.messagePage {}

	.topArea {
		background-image: linear-gradient(to right, #FF8008 0%, #FFC837  51%, #FF8008  100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;            
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block;
	}
</style>

