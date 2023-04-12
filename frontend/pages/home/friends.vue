<!--好友页面-->
<template>
	<view class="friends">
		
		<!--给出结果反馈-->
		<uni-popup ref="message" type="message">
			<uni-popup-message :type="headMessage.msgType" :message="headMessage.messageText" :duration="1200">
			</uni-popup-message>
		</uni-popup>
		
		<!--是否添加新朋友弹窗-->
		<uni-popup ref="getBack" type="dialog">
			<uni-popup-dialog :type="msgType" cancelText="考虑一下" confirmText="给Ta答复"
			 :title="newFriendsList[dealIndex].nickName" :content="newFriendsList[dealIndex].detail"
			 @confirm="continueJudge"></uni-popup-dialog>
		</uni-popup>
		<uni-popup ref="Reject_Agree" type="dialog">
			<uni-popup-dialog :type="msgType" cancelText="拒绝" confirmText="同意" 
			:title="newFriendsList[dealIndex].nickName" :content="newFriendsList[dealIndex].detail"
			@confirm="agreeApl" @close="rejectApl"></uni-popup-dialog>
		</uni-popup>
		
		<!-- 新朋友 -->
		<view class="newFirendArea">新朋友申请</view>
		<uni-list border="true">
			<uni-list-chat clickable="true" avatar-circle="true"
			v-for="(item,index) in newFriendsList"
			v-show="item.isShow"
			:title="item.nickName" 
			:avatar="item.avatarUrl" 
			:note="item.detail" 
			badge-positon="left" badge-text="dot" 
			@click="newFriendClick(index)">
			</uni-list-chat>
		</uni-list>
		<view v-show="isShowContent">
			<uni-load-more status="noMore" :contentText="contentNew" ></uni-load-more>
		</view>
		
		<!-- 好友列表 -->
		<view class="friendArea">好友列表</view>
		 <uni-list border="true" v-for="(item,index) in  friendList" >
			<uni-swipe-action>
			 <uni-swipe-action-item :right-options="options" @click="friendDelete($event, item.id)" @change="change()">
			     <uni-list-item thumbSize="lg"  clickable="true" showArrow="true"
			     :title="item.remark==''?item.nickName:item.remark" 
			     :thumb="item.avatarUrl"
			     @click="firendClick(item)">
			     </uni-list-item>
			  </uni-swipe-action-item>
			 </uni-swipe-action>
		 </uni-list>
		
		<uni-load-more status="noMore" :contentText="contentDown" ></uni-load-more>
		
	</view>
	
</template>

<script>
    export default {
		data() {
			return {
				
				// 头部信息反馈
				headMessage:{
				    msgType:'info',
				    messageText:'聊天信息加载中',
				},
				
				// 我的信息
				myInfo:{
					myId:"50",
				},
				
				// 新好友申请
				newFriendsList:[
					// {
					//	id :'1',
					// 	avatarUrl:"../../../../static/icons/news.png",
					// 	nickName:"甄姬",
					//	detail:'1',
					//	isShow: true,
					//}
				],
				isShowContent : true,
				contentNew:{
					contentnomore: '暂无好友申请'
				},
				dealIndex:'1',  //正在处理的申请好友信息
				answer:'1',     //给出的答复
				
				// 好友列表
				friendList:[],
				contentDown:{
					contentnomore: '没有更多好友啦'
				},
				
				// 左右滑动选择
				options:[{
				    text: '取消',
				    style: {
				        backgroundColor: '#007aff'
				    }
				}, {
					text: '删除',
					style: {
				        backgroundColor: '#dd524d'
					}
				}],
				
			}
		},
		onShow() {
			// 获取存储信息
			let that = this;
			uni.getStorage({
				key: 'userInfo',
				success: function(res) {
					console.log(res.data.id);
					that.myInfo.myId = res.data.id;
					// 获取好友列表
					uni.request({
						url:'http://124.71.170.100/index.php/index/getFriendList',
						method:"GET",
						header: {
							"content-type": "application/w-xxx-form-unlencoded"
						},
						data:{
							id:that.myInfo.myId
						},
						success: (res) => {
							console.log('获取好友列表成功' + res.data.data.friendList);
							that.friendList=res.data.data.friendList;
						},
						fail: () => {
							console.log('获取好友列表失败');
						}
					}),
					// 获取好友申请列表
					uni.request({
						url:'http://124.71.170.100/index.php/index/getFriendRequest',
						method:"GET",
						header: {
							"content-type": "application/w-xxx-form-unlencoded"
						},
						data:{
							id:that.myInfo.myId
						},
						success: (res) => {
							console.log('获取好友申请列表成功' + res.data.data.requestList);
							let len = res.data.data.requestList.length;
							that.newFriendsList = [];
							if(len>=1){
								that.isShowContent = false;
							}
							else{
								that.isShowContent = true;
							}
							for(let i=0;i<len;i++){
								let temp={
									id : res.data.data.requestList[i].id,
									avatarUrl:res.data.data.requestList[i].avatarUrl,
									nickName:res.data.data.requestList[i].nickName,
									detail:res.data.data.requestList[i].detail,
									isShow: true,
								};
								that.newFriendsList.push(temp);
							}
						},
						fail: () => {
							console.log('获取好友申请列表失败');
						}
					})
				}
			});
		},
		methods: {
			// 外部跳转函数
			navigateTo(url) {
			  uni.navigateTo({
			    url,
			  });
			},
			// 同意申请
			agreeApl(){
				uni.showLoading({
					title:'发送中'
				});
				this.headMessage.messageText="添加成功";
				this.headMessage.msgType='success';
				this.answer = 1;
				setTimeout(()=>{
					uni.hideLoading(); //关闭发送信息
				    uni.request({
					    url:'http://124.71.170.100/index.php/index/confirmFriendRequest',
					    method:"POST",
						header: {
							"content-type": "application/json"
						},
				    	data:{
					    	id:this.myInfo.myId,
					    	otherUserId:this.newFriendsList[this.dealIndex].id,
							add:this.answer
				    	},
				    	success: (res) => {
							this.getData();  //刷新重新获取信息
						    console.log('好友申请反馈成功'+res.data.code);
				    	},
				    	fail: () => {
						    console.log('好友申请反馈失败');
					    },
				    });
					this.$refs.message.open(); //结果显示
				},1000);
				//this.newFriendsList[this.dealIndex].isShow = !this.newFriendsList[this.dealIndex].isShow;// 不进行显示
			},
			// 拒绝申请
			rejectApl(){
				uni.showLoading({
					title:'发送中'
				});
				this.headMessage.messageText="拒绝成功";
				this.headMessage.msgType='warn';
				this.answer = 0;
				setTimeout(()=>{
					uni.hideLoading(); //关闭发送信息
				    uni.request({
					    url:'http://124.71.170.100/index.php/index/confirmFriendRequest',
					    method:"POST",
						header: {
							"content-type": "application/json"
						},
				    	data:{
					    	id:this.myInfo.myId,
							otherUserId:this.newFriendsList[this.dealIndex].id,
					    	add:this.answer
				    	},
				    	success: (res) => {
							this.getData(); //刷新重新获取信息
						    console.log('好友申请反馈成功'+res.data.code);
				    	},
				    	fail: () => {
						    console.log('好友申请反馈失败');
					    },
				    });
					this.$refs.message.open(); //结果显示
				},1000);
			
				//this.newFriendsList[this.dealIndex].isShow = !this.newFriendsList[this.dealIndex].isShow;// 不进行显示
			},
			// 进行反馈
			continueJudge(){
				this.$refs.Reject_Agree.open();
			},
			// 对新朋友申请进行反馈
			newFriendClick(index){
				console.log(this.newFriendsList[index].id+"被点击了");
				//this.headMessage.messageText="添加成功";
				this.$refs.getBack.open();
				this.dealIndex = index;
			},
			// 跳转至对应好友页面
			firendClick(item){
				console.log(item.id+"被点击了");
				uni.navigateTo({
				  url:'./homepage?id=' + item.id
				});
			},
			// 删除好友
			friendDelete(e, id){
				let that = this;
				if(e.index==1){
					uni.showLoading({
						title:'正在删除'
					});
					that.headMessage.messageText="删除成功";
					that.headMessage.msgType='warn';
					setTimeout(()=>{
						uni.hideLoading();	          //关闭发送信息
				        uni.request({
				            url:'http://124.71.170.100/index.php/index/deleteFriend',
				            method:"POST",
							header: {
								"content-type": "application/json"
							},
					        data:{
				            	id:that.myInfo.myId,
					        	friendId:id
				    	    },
					        success: (res) => {
					            console.log('删除好友成功'+res.data.code);
								that.getData(); //重新获取信息
								console.log(id)
								console.log(that.myInfo.myId)
				    	    },
					        fail: () => {
				    	        console.log('删除好友失败');
				            },
			            });
						that.$refs.message.open(); //结果显示
					},1000);
				}
			},
			// 空余暂留函数
			back(){
				console.log("返回被点击")
			},
			// 获取好友/申请信息
			getData(){
				// 获取好友列表
				uni.request({
					url:'http://124.71.170.100/index.php/index/getFriendList',
					method:"GET",
					header: {
						"content-type": "application/w-xxx-form-unlencoded"
					},
					data:{
						id:this.myInfo.myId
					},
					success: (res) => {
						console.log('获取好友列表成功' + res.data.data.friendList);
						this.friendList=res.data.data.friendList;
					},
					fail: () => {
						console.log('获取好友列表失败');
					}
				});
				// 获取好友申请列表
				uni.request({
					url:'http://124.71.170.100/index.php/index/getFriendRequest',
					method:"GET",
					header: {
						"content-type": "application/w-xxx-form-unlencoded"
					},
					data:{
						id:this.myInfo.myId
					},
					success: (res) => {
						console.log('获取好友申请列表成功' + res.data.data.requestList);
						let len = res.data.data.requestList.length;
						if(len>=1){
							this.isShowContent = false;
						}
						else{
							this.isShowContent = true;
						}
						this.newFriendsList = [];
						for(let i=0;i<len;i++){
							let temp={
								id : res.data.data.requestList[i].id,
								avatarUrl:res.data.data.requestList[i].avatarUrl,
								nickName:res.data.data.requestList[i].nickName,
								detail:res.data.data.requestList[i].detail,
								isShow: true,
							};
							this.newFriendsList.push(temp);
						}
					},
					fail: () => {
						console.log('获取好友申请列表失败');
					}
				});
			},
			change(){
				//something need to add
			}
		}
	}
</script>

<style>
	.newFirendArea{
		background-image: linear-gradient(to right, #dcffc5 0%, #ffcc8c  51%, #ffc27c  100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;            
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block
		
	}
	.friendArea{
		background-image: linear-gradient(to right, #ffe786 0%, #ffca6e  51%, #ff9763  100%);
		margin: 10px;
		padding: 5px 20px;
		text-align: center;
		text-transform: uppercase;
		transition: 0.5s;
		background-size: 200% auto;
		color: white;            
		box-shadow: 0 0 20px #eee;
		border-radius: 10px;
		display: block
	}
	.friendCustomRight {
		flex: 1;
		display: flex;
		flex-direction: column;
		justify-content: space-between;
		align-items: flex-end;
	}
	
	.friendCustomText {
		font-size: 12px;
		color: #999;
	}
</style>
