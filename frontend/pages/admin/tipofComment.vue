<template>
	<view class="tipofCommentList">
		<view v-if="listNum">
			<uni-section title="未处理的评论举报" type="line">
				<uni-list v-for="(item, index) in comments">
					<uni-list-item thumbSize="lg" clickable="true"
					:title="item.nickName"
					:note="item.reason"
					@click="chooseComment(index)">
					</uni-list-item>
				</uni-list>
			</uni-section>
		</view>
		<uni-load-more v-else status="noMore" :contentText="contenDown"></uni-load-more>
		<!-- <text v-else class="text">没有未处理的举报</text> -->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				listNum: "",
				comments: [],
				contenDown: {
					contentnomore: '没有需要处理的评论举报'
				}
			}
		},
		onShow() {
			this.getTipofCommentList()
		},
		onLoad() {
			this.getTipofCommentList()
		},
		methods: {
			getTipofCommentList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getCommentReport',
					method: 'GET',
					success: (res) => {
						let tipofList = res.data.data.reportList
						this.comments = tipofList
						this.listNum = tipofList.length
						console.log(tipofList)
						// this.listNum = 0  // for test
					}
				})
			},
			chooseComment(index) {
				uni.navigateTo({
					url: 'setTipofComment?index=' + index
				})
			}
		}
	}
</script>

<style>
	.text{
		margin: auto 200rpx;
	}
</style>
