<template>
	<view>
		<uni-card :title="comment.nickName">
			<!-- <uni-forms ref="form" :modelValue="formData"> -->
			<uni-section title="评论内容" type="line">
				<text class="fontStyle info">{{content}}</text>
			</uni-section>
			
			<uni-section title="举报原因" type="line">
				<text class="fontStyle info">{{comment.reason}}</text>
			</uni-section>
			
			<uni-section title="详细信息" type="line">
				<text class="fontStyle info">{{comment.detail}}</text>
			</uni-section>
			
			<view class="myButton">
				<button class="btn" @click="deleteComment()">删除评论</button>
				<button class="btn" @click="noAction()">无操作</button>
			</view>
		</uni-card>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				index: "",
				comment: "",
				content: "",
				userId: ""
			}
		},
		onLoad: function(option){
			this.index = option.index
			console.log(this.index)
			this.getTipof()
			
		},
		methods: {
			getMyInfo() {
				let that = this
				uni.getStorage({
					key: 'userInfo',
					success: function(res) {
						console.log(res)
						that.userId = res.data.id
						that.getContent()
					}
				})
			},
			getContent() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getDetailedResponseList',
					method: 'GET',
					data:{
						commentId: this.comment.commentId,
						id: this.userId
					},
					success: (res) => {
						console.log(res)
						this.content = res.data.data.content
					}
				})
			},
			getTipof() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getCommentReport',
					method: 'GET',
					success: (res) => {
						let index = this.index
						let reportList = res.data.data.reportList
						this.comment = reportList[index]
						this.comment.nickName = "被举报人 " + this.comment.nickName
						this.getMyInfo()
						// console.log(this.appeal)
					}
				})
			},
			deleteComment() {
				console.log()
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealCommentReport',
					data: {
						commentId: this.comment.commentId,
						result: "1",
						reportId: this.comment.reportId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'tipofComment'
						})
					}
				})
			},
			noAction() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealCommentReport',
					data: {
						commentId: this.comment.commentId,
						result: "0",
						reportId: this.comment.reportId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'tipofComment'
						})
					}
				})
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
	.picture {
		width: 200px;
		height: 200px;
		background-color: #eeeeee;
	}
	.myButton {
		margin-top: 50rpx;
	}
	.btn {
		background: #FFC837;
		margin: 10rpx auto;
	}
</style>
