<template>
	<view>
		<view class="comment">
			<uni-card
			:title="(remark.length==0?nickName:remark)"
			:sub-title="getTime(time)"
			:isFull="true"
			:extra="score"
			:thumbnail="avatarUrl" @click="onClick($event)">
				<text style="font-size: 30rpx;">{{content}}</text>
				<view style="display:flex;">
					<view style="width:50%;">
					</view>
					<view style="width:17%;" v-if="isLiked==1" @click="hasLikedClick()" color="#ff0000">
						<uni-icons type="heart-filled" size="22" color="#999"></uni-icons>
						<text>{{likedNum>99 ? "99+" : likedNum}}</text>
					</view>
					<view style="width:17%;" v-else @click="likedClick()">
						<uni-icons type="heart" size="22" color="#999"></uni-icons>
						<text>{{likedNum>99 ? "99+" : likedNum}}</text>
					</view>
					<view style="width:17%;" @click="clickComment(commentId,userId)">
						<uni-icons type="chatbubble" size="22" color="#999"></uni-icons>
						<text>{{responseList.length>99 ? "99+" : responseList.length}}</text>
					</view>
					
					<view style="width:10%;" v-if="myId == userId" @click="deleteClick(index)">
						<uni-icons type="trash" size="22" color="#999"></uni-icons>
					</view>
					<!-- <view style="width:10%;" v-if="1" @click="deleteClick(index)">
						<uni-icons type="trash" size="22" color="#999"></uni-icons>
					</view> -->
					<view style="width:10%;" v-else @click="tipofClick()">
						<uni-icons type="info" size="22" color="#999"></uni-icons>
					</view>
				</view>
				<view v-if="likedList.length!=0" style="display: flex;">
					<view v-for="(item,index) in likedList">
						<view v-if="index<5" style="font-weight: bold;">
							<text @click="toHomePage(item.id)">{{item.remark==0?item.nickName:item.remark}}</text>
							<text v-if="(index+1!=likedNum)&&(index<4)">、</text>
						</view>
					</view>
					等{{likedNum}}人已点赞
				</view>
				<view class="response" v-for="(item,index) in responseList">
					<view v-if="item.toId==userId">
						<text style="font-weight: 700;" @click="toHomePage(item.fromId)">{{item.fromRemark==0?item.fromNickName:item.fromRemark}}</text>
						<text @click="clickComment(commentId,item.fromId)">: {{item.detail}}</text>
					</view>
					<view v-else>
						<text style="font-weight: 700;" @click="toHomePage(item.fromId)">{{item.fromRemark==0?item.fromNickName:item.fromRemark}} </text>
						<text style="margin-left: 4rpx;margin-right: 4rpx;"> 回复 </text>
						<text style="font-weight: 700;" @click="toHomePage(item.toId)">{{item.toRemark==0?item.toNickName:item.toRemark}}</text>
						<text @click="clickComment(commentId,item.fromId)">: {{item.detail}}</text>
					</view>
				</view>
				<view style="display: flex;">
					<view style="width: 50%;"></view>
					<view style="text-align: right;width: 50%;">
						<text class="overtext" @click="dishClick()">
							{{dishName}}--{{merchantNickName}}
						</text>
					</view>
				</view>
			</uni-card>
		</view>
		<commentInput
		    :placeholder="placeholder" 
		    :valueData="tmpCommentData" 
		    :isShow="isShowComment" 
		    :curKey="curCommentId"
		    @submitComment="submitComment"  
		    @clickOther="clickOther">
		</commentInput>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				isCanteen:"",
				myId: "", //我的Id
				commentId: "",
				userId: "",
				nickName: "",
				remark: "",
				avatarUrl: "",
				dishId: "",
				dishName: "",
				merchantNickName: "",
				time: "",
				score: "",
				content: "",
				likedNum: "",
				likedList:[],
				isLiked: "",
				responseList:[],
				
				isShowComment:false,
				placeholder:"发个友善的评论吧",
				tmpCommentData:[],
				curCommentId:'',
				curtoId:'',
			}
		},
		// onShow(){
		// 	let that=this;
		// 	that.refresh();
		// },
		onLoad: function (option) { //option为object类型，会序列化上个页面传递的参数
			this.commentId=option.commentId;
			this.isCanteen=option.isCanteen;
			let that = this;
			uni.getStorage({
				key:'userInfo',
				success: function (res) {
					console.log(res.data);
					//that.userInfo = res.data;
					that.myId=res.data.id;
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
			refresh(){
				uni.request({
					//获取某一评论详细信息
					url: 'http://124.71.170.100/index.php/index/getDetailedResponseList',
					method: "GET",
					data: {
						commentId: this.commentId,
						id: this.myId
					},
					success: (res) => {
						console.log(this.commentId);
						console.log(this.myId);
						console.log(res);
						// this.commentId = res.data.data.commentId;
						this.userId = res.data.data.userId;
						this.nickName = res.data.data.nickName;
						this.remark = res.data.data.remark;
						this.avatarUrl = res.data.data.avatarUrl;
						this.dishId = res.data.data.dishId;
						this.dishName = res.data.data.dishName;
						this.merchantNickName = res.data.data.merchantNickName;
						this.time = res.data.data.time;
						this.score = res.data.data.score;
						this.content = res.data.data.content;
						this.likedList = res.data.data.likedList;
						this.likedNum = this.likedList.length;
						this.isLiked = res.data.data.isLiked;
						this.responseList = res.data.data.responseList;
						console.log('刷新成功');
					},
					fail: () => {
						console.log('刷新失败');
					}
				})
			},
			onClick(e) {
				if (e == "title") {
					var identity;
					uni.request({
						//获取身份
						url: 'http://124.71.170.100/index.php/index/getUserIdentity',
						method: "GET",
						data: {
							id: this.userId
						},
						success: (res) => {
							console.log(res);
							identity = res.data.data.identity;
							console.log(identity);
							// identity=2;
							if(identity=="1"){
								uni.navigateTo({
									url: '/pages/home/homepage?id=' + this.userId
								});
							}
							else if(identity=="2"){
								uni.navigateTo({
									url: '/pages/index/store?id=' + this.userId
								});
							}
						},
						fail: () => {
							console.log('获取身份失败');
						}
					});
				}
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
			hasLikedClick(){
				//取消点赞评论
				this.isLiked=0;
				this.likedNum--;
				uni.request({
					url: 'http://124.71.170.100/index.php/index/likeComment',
					method: "POST",
					header:{
						"content-type":"application/w-xxx-form-unlencoded"
					},
					data: {
						commentId: this.commentId,
						id: this.myId
					},
					success: () => {
						console.log('取消点赞');
						this.refresh();
					}
				})
			},
			likedClick(index){
				//点赞评论
				this.isLiked=1;
				this.likedNum++;
				uni.request({
					url: 'http://124.71.170.100/index.php/index/likeComment',
					method: "POST",
					header:{
						"content-type":"application/w-xxx-form-unlencoded"
					},
					data: {
						commentId: this.commentId,
						id: this.myId
					},
					success: () => {
						console.log('点赞成功');
						this.refresh();
					}
				})
			},
			
			// 点击评论触发评论输入框
			clickComment(commentId,toId){
			    this.isShowComment = true;
				this.curCommentId = commentId;
				this.curtoId = toId;
			},
			// 提交评论并清除记忆
			submitComment(e){
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
					this.isShowComment=false;
					this.refresh();
					}
				})
			},
			// 点击空白返回并保存记忆
			clickOther(e){
			    this.isShowComment=false;
			},
			
			tipofClick(){
				console.log("举报");
				uni.navigateTo({
					url: '/pages/home/tip-of-comment?commentId='+this.commentId + '&id=' + this.userId
				});
			},
			deleteClick(){
				//撤回评论
				uni.showModal({
					title: '确认撤回该评论？',
					success: (res) =>{
						if (res.confirm) {
							uni.request({
								url: 'http://124.71.170.100/index.php/index/deleteComment',
								method: "POST",
								header: {
									"content-type": "application/x-www-form-urlencoded",
								},
								data: {
									commentId: this.commentId,
									id: this.myId,
								},
								success: () => {
									console.log('删除成功,评论Id为'+this.commentId);
								}
							})
							this.refresh();
						} 
						else if (res.cancel) {
							console.log('取消删除');
						}
					}
				});
			},
			dishClick(){
				uni.navigateTo({
					url: '/pages/index/dish?dishId='+this.dishId + "&isCanteen=" + this.isCanteen
				});
			},
		}
	}
</script>

<style>
.comment{
	word-break:break-all;
}
.overtext{
	color: #ffaaed;
	overflow: hidden;
	text-overflow: ellipsis;
	text-overflow: ellipsis;  /* 超出部分省略号 */
	display: -webkit-box; /** 对象作为伸缩盒子模型显示 **/
	-webkit-box-orient: vertical; /** 设置或检索伸缩盒对象的子元素的排列方式 **/
	-webkit-line-clamp: 1; /** 显示的行数 **/
	}
</style>
