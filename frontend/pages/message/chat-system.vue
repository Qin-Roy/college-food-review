<!--系统消息页面-->
<template>
	<view>
		<uni-popup ref="message" type="message">
			<uni-popup-message :type="msgType" :message="messageText" :duration="1200"></uni-popup-message>
		</uni-popup>
    	<view class="box-1" id="list-box">
			<view class="talk-list" >
				<view v-for="(item,index) in showList" :key="index" :id="`msg-${item.id}`">
					<view class="item flex_col" :class=" item.id == myInfor.id ? 'push':'pull' ">
						<image :src='item.url' mode="aspectFill" class="pic" @click="toHomePage(index)"></image>
						<view class="content">{{item.content}}</view>
					</view>
				</view>
			</view>
		<!--
		无需发送信息
		</view>
		<view class="box-2">
			<view class="flex_col">
				<view class="flex_grow">
					<input type="text" class="content" maxlength="100" v-model="content" placeholder="请输入聊天内容" placeholder-style="color:#DDD;" :cursor-spacing="6">
				</view>
				<button class="send" @tap="send">发送</button>
			</view>
		-->
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				//页面加载头部信息
				msgType:'info',
				messageText:'系统信息加载中',
				systemUrl:'https://img0.baidu.com/it/u=1295072258,3925203567&fm=253&fmt=auto&app=138&f=JPEG?w=640&h=427',
				// 页面显示情况
				pattern : '1',
				// 我的情况
				myInfor:{
				    id:'0',
				    url:''
				},
				// 活动通知消息
				activityList:[{
					merchantId:'0',
					merchantNickName:'0',
					merchantAvatarUrl:"1",
					detail:"1",
					time:"1"	
				}],
				// 好友申请消息
				friendList:[{
					friendId:'0',
					friendNickName:'0',
					friendAvatarUrl:'0',
					result:'0',
					time:'0'
				}],
				// 申诉反馈消息
				appealList:[{
					result:'0',
					time:"0"
				}],
				// 举报反馈消息
				reportList:[{
					targetId:'0',
					NickName:'0',
					AvatarUrl:'0',
					isUserOrComment:'0',
					result:"0",
					time:"0"
				}],
				// 页面显示
				showList:[],
				// 临时添加聊天数据
				content:'',
				// 跳转页面id
				idList:[],
			}
		},
		onLoad(option){
			
			//获取上个页面参数
			this.myInfor.id = option.id;
			this.pattern = option.pattern;
			console.log('参数传递成功+id:'+option.id);//控制台显示结果
			console.log('参数传递成功+pattern:'+option.pattern);//控制台显示结果
			
			// 获取存储信息
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					console.log(res.data.avatarUrl);
					that.myInfor.url = res.data.avatarUrl;
				}
			});
			
			// 获取系统信息
			uni.request({
				url:'http://124.71.170.100/index.php/index/getSystemMessage',
				method:"GET",
				data:{
					id:this.myInfor.id,
				},
				success: (res) => {
					console.log('获取对话信息成功' + res.data.data);
					// 获取初始化的值
					this.activityList = res.data.data.activityList;
					this.friendList = res.data.data.friendList;
					this.appealList = res.data.data.appealList;
					this.reportList = res.data.data.reportList;
					
					this.setShowValue(this.pattern);
					
					// 初入页面翻到底页
					setTimeout(()=>{
					uni.pageScrollTo({
					 scrollTop: 999999,	      // 设置一个超大值，以保证滚动条滚动到底部
					 duration: 0
					}),
					this.$refs.message.open() // 聊天信息获取提示
					},500);	
					
				},
				fail: () => {
					console.log('获取对话信息失败');
				},
		    });	
		},
		methods: {
			// 移动至聊天尾部
			down(){
				uni.pageScrollTo({
				 scrollTop: 999999,
				 duration: 0
				});	
			},
			// 发送信息
			send(){
				// 错误处理
				if(!this.content){
					uni.showToast({
						title:'请输入有效的内容',
						icon:'none'
					})
					return;
				}
				// 显示正在发送标签
				uni.showLoading({
					title:'正在发送'
				})
				setTimeout(()=>{
					// 关闭发送提示
					uni.hideLoading();	
					
					// 产生现在的聊天数据
					let dataTemp = {
						"id":this.myInfor.id,
						"content":this.content,
						"url": this.myInfor.url,
					}
									
					// 消息记录添加至talkList中
					this.showList.push(dataTemp);
					
					// 清空内容框中的内容
					this.$nextTick(()=>{
						this.content = '';
						uni.pageScrollTo({
						    scrollTop: 999999,	// 设置一个超大值，以保证滚动条滚动到底部
						    duration: 0
						});
					})
				},200);
			},
			// 展示对应的值
			setShowValue(index){
				// 产生现在的聊天数据
				
				// 活动详情
				if(index=='0'){
					for(let i=0;i<this.activityList.length;i++){
						this.idList.push(this.activityList[i].merchantId)
						this.activityList[i].startTime = this.changeTime(this.activityList[i].startTime);
						this.activityList[i].endTime = this.changeTime(this.activityList[i].endTime);
						let contentShow = this.activityList[i].merchantNickName+ ' 邀请您参加 '+this.activityList[i].activityName+
						                  '  (a)活动详情：'+this.activityList[i].detail+"  (b)活动时间："+this.activityList[i].startTime+'-'+this.activityList[i].endTime
				        let dataTemp = {
					        "id":'0',
					        "content":contentShow,
					        "url": this.activityList[i].merchantAvatarUrl,
				        }
					    // 消息记录添加至showList中
					    this.showList.push(dataTemp);
					}
				}
				// 好友申请反馈
				else if(index == '1'){
					for(let i=0;i<this.friendList.length;i++){
			            this.idList.push(this.friendList[i].friendId)
						this.friendList[i].time = this.changeTime(this.friendList[i].time);
						let contentShow;
						if(this.friendList[i].result==1){
					        contentShow = this.friendList[i].friendNickName+' 于 '+this.friendList[i].time+' 同意了你的好友申请' 
					    }
						else{
							contentShow = this.friendList[i].friendNickName+' 于 '+this.friendList[i].time+' 拒绝了你的好友申请' 
						}
						let dataTemp = {
					        "id":'0',
					        "content":contentShow,
					        "url": this.friendList[i].friendAvatarUrl,
					    }
					    // 消息记录添加至showList中
					    this.showList.push(dataTemp);
					}
				}
				// 用户申诉反馈
				else if(index == '2'){
					for(let i=0;i<this.appealList.length;i++){
						this.appealList[i].time = this.changeTime(this.appealList[i].time);
						let contentShow;
						if(this.appealList[i].result==1){
						    contentShow = '您的用户申诉 于 '+this.appealList[i].time+'  被管理员同意，您已经解封！' 
						}
						else{
							contentShow = '您的用户申诉 于 '+this.appealList[i].time+'  被管理员拒绝，如有异议请再次提交~' 
						}
						let dataTemp = {
						    "id":'0',
						    "content":contentShow,
						    "url":this.systemUrl,
						}
					    // 消息记录添加至showList中
					    this.showList.push(dataTemp);
					}
				}
				// 举报反馈
				else if(index == '3'){
					for(let i=0;i<this.reportList.length;i++){
						let contentShow;
						this.reportList[i].time  = this.changeTime(this.reportList[i].time);
						if(this.reportList[i].isUserOrComment==1){
							if(this.reportList[i].result==1){
						        contentShow = '您于 '+this.reportList[i].time+' 提交的'+'针对用户 '+this.reportList[i].nickName+'的评论 的举报信息已经被管理员受理，并确定为有效，已对该评论进行处理' 
						    }
							else{
								contentShow = '您于 '+this.reportList[i].time+' 提交的'+'针对用户 '+this.reportList[i].nickName+'的评论 的举报信息已经被管理员受理，并确定为无效，如有异议请再次提交' 
							}
						}
						else{
							if(this.reportList[i].result==1){
							    contentShow = '您于 '+this.reportList[i].time+' 提交的'+'针对用户 '+this.reportList[i].nickName+' 的举报信息已经被管理员受理，并确定为有效，已对该用户进行处理' 
							}
							else{
								contentShow = '您于 '+this.reportList[i].time+' 提交的'+'针对用户 '+this.reportList[i].nickName+' 的举报信息已经被管理员受理，并确定为无效，如有异议请再次提交' 
							}		
						}
						let dataTemp = {
						    "id":'0',
						    "content":contentShow,
						    "url":this.systemUrl,
						}
						// 消息记录添加至showList中
						this.showList.push(dataTemp);
					}
				}				
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
			// 页面跳转
			toHomePage(index) {
				if(this.idList.length>=index){
					let Id = this.idList[index];
					console.log(index)
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
						},
						fail: () => {
							console.log('获取身份失败');
						}
					});
				}
			},	
		}
	}
</script>

<style lang="scss">
	@import "../../global.scss";
	page{
		background-color: #F3F3F3;
		font-size: 28rpx;
	}
	/* 加载数据提示 */
	.tips{
		position: fixed;
		left: 0;
		top:var(--window-top);
		width: 100%;
		z-index: 9;
		background-color: rgba(0,0,0,0.15);
		height: 72rpx;
		line-height: 72rpx;
		transform:translateY(-80rpx);
		transition: transform 0.3s ease-in-out 0s;
		
		&.show{
			transform:translateY(0);
		}
	}
	
	.box-1{
		width: 100%;
		height: auto;
		padding-bottom: 100rpx;
		box-sizing: content-box;
		
		/* 兼容iPhoneX */
		margin-bottom: 0;  
		margin-bottom: constant(safe-area-inset-bottom);  
		margin-bottom: env(safe-area-inset-bottom);  
	}
	.box-2{
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
		
		>view{
			padding: 0 20rpx;
			height: 100rpx;
		}
		
		.content{
			background-color: #fff;
			height: 64rpx;
			padding: 0 20rpx;
			border-radius: 32rpx;
			font-size: 28rpx;
		}
		
		.send{
			background-image: linear-gradient(to right, #efff9e 0%, #ffec8c  51%, #ffa9a1  100%);
			color: #fff;
			height: 64rpx;
			margin-left: 20rpx;
			border-radius: 32rpx;
			padding: 0;
			width: 120rpx;
			line-height: 62rpx;
			
			&:active{
				background-image: linear-gradient(to right, #efff9e 0%, #ffec8c  51%, #ffa9a1  100%);
			}
		}
	}
	
	.talk-list{
		padding-bottom: 20rpx;
		
		/* 消息项，基础类 */
		.item{
			padding: 20rpx 20rpx 0 20rpx;
			align-items:flex-start;
			align-content:flex-start;
			color: #333;
			
			.pic{
				width: 92rpx;
				height: 92rpx;
				border-radius: 50%;
				border: #fff solid 1px;
			}
			
			.content{
				padding: 20rpx;
				border-radius: 4px;
				max-width: 500rpx;
				word-break: break-all;
				line-height: 52rpx;
				position: relative;
			}
			
			/* 收到的消息 */
			&.pull{
				.content{
					margin-left: 32rpx;
					background-color: #fff8f6;
					
					&::after{
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
			&.push{
				/* 主轴为水平方向，起点在右端。使不修改DOM结构，也能改变元素排列顺序 */
				flex-direction: row-reverse;
				
				.content{
					margin-right: 32rpx;
					background-color: #dde9c9;
					
					&::after{
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