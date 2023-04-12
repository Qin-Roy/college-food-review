<template>
	<view>
		<uni-card :title="appeal.nickName" >
			<!-- <uni-forms ref="form" :modelValue="formData"> -->
			
			<uni-section title="申诉理由" type="line">
				<text class="fontStyle info">{{appeal.appealDetail}}</text>
			</uni-section>
			
			<view v-for="(item,index) in appeal.reportList">
				<uni-section title="封禁原因" type="line">
					<text class="fontStyle info">{{appeal.reportList[index].reportReason}}</text>
				</uni-section>
				
				<uni-section title="详细信息" type="line">
					<text class="fontStyle info">{{appeal.reportList[index].reportDetail}}</text>
				</uni-section>
				
				<uni-section v-if="appeal.reportList[index].reportPictureList.length != 0" title="举报图片" subTitle="图片/视频/文档等任意形式文件" type="line">
					<view v-for="(item, index2) in appeal.reportList[index].reportPictureList">
						<image class="picture" :src="appeal.reportList[index].reportPictureList[index2]" @click="previewImage(index2)"></image>
					</view>
				</uni-section>
			</view>
			
			<uni-section v-if="appeal.appealPictureList.length != 0" title="申诉图片" subTitle="图片/视频/文档等任意形式文件" type="line">
				<view v-for="(item, index) in appeal.appealPictureList">
					<image class="picture" :src="appeal.appealPictureList[index]" @click="previewImage(index)"></image>
				</view>
			</uni-section>
			
			<view class="myButton">
				<button class="btn" @click="agree()">同意解封</button>
				<button class="btn" @click="disagree()">不同意解封</button>
			</view>
			
		</uni-card>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				index: "",
				appeal: ""
			}
		},
		onLoad: function(option){
			this.index = option.index
			console.log(this.index)
			this.getAppeal()
		},
		methods: {
			getAppeal() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getAppeal',
					method: 'GET',
					success: (res) => {
						let index = this.index
						let appealList = res.data.data.appealList
						this.appeal = appealList[index]
						console.log(this.appeal)
					}
				})
			},
			previewImage(index){
			    uni.previewImage({
					urls: this.appeal.reportPicture
			    })
			},
			agree() {
				console.log("agree")
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealAppeal',
					data: {
						id: this.appeal.id,
						result: "1",
						appealId: this.appeal.appealId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'appealList'
						})
					}
				})
			},
			disagree() {
				console.log("disagree")
				uni.request({
					url: 'http://124.71.170.100/index.php/index/dealAppeal',
					data: {
						id: this.appeal.id,
						result: "0",
						appealId: this.appeal.appealId
					},
					method:'GET',
					success: (res) => {
						console.log(res.data.code)
						uni.navigateBack({
							url: 'appealList'
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
