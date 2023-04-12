<template>
	<view>
		<uni-card :title="user.nickName">
			<!-- <uni-forms ref="form" :modelValue="formData"> -->
			
			<uni-section title="举报原因" type="line">
				<text class="fontStyle info">{{user.reason}}</text>
			</uni-section>
			
			<uni-section title="详细信息" type="line">
				<text class="fontStyle info">{{user.detail}}</text>
			</uni-section>
			
			<uni-section v-if="user.pictureList.length != 0" title="佐证资料" subTitle="图片等任意形式文件" type="line">
				<view v-for="(item, index) in user.pictureList">
					<image class="picture" :src="user.pictureList[index]" @click="previewImage(index)"></image>
				</view>
			</uni-section>
			
			<view class="myButton">
				<button class="btn" @click="agree()">封禁</button>
				<button class="btn" @click="disagree()">无处理</button>
			</view>
		</uni-card>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				index: "",
				user: ""
			}
		},
		onLoad: function(option){
			this.index = option.index
			console.log(this.index)
			this.getTipof()
		},
		methods: {
			getTipof() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getUserReport',
					method: 'GET',
					success: (res) => {
						let index = this.index
						let reportList = res.data.data.reportList
						this.user = reportList[index]
						this.user.nickName = "被举报人 " + this.user.nickName
						// console.log(this.appeal)
					}
				})
			},
			previewImage(index){
			    uni.previewImage({
					urls: this.user.pictureList
			    })
			},
			agree() {
				console.log("agree")
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealUserReport',
					data: {
						id: this.user.id,
						result: "1",
						reportId: this.user.reportId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'tipofUser'
						})
					}
				})
			},
			disagree() {
				console.log("disagree")
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealUserReport',
					data: {
						id: this.user.id,
						result: "0",
						reportId: this.user.reportId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'tipofUser'
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
