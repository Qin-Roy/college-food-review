<template>
	<view class="tipofUserList">
		<view v-if="listNum">
			<uni-section title="未处理的用户举报" type="line">
				<uni-list v-for="(item, index) in users">
					<uni-list-item thumbSize="lg" clickable="true"
					:title="item.nickName"
					:note="item.reason"
					@click="chooseUser(index)">
					</uni-list-item>
				</uni-list>
			</uni-section>
		</view>
		<uni-load-more v-else status="noMore" :contentText="contenDown"></uni-load-more>
		<!-- <text v-else class="text">没有未处理的申诉</text> -->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				listNum: "",
				users: [],
				contenDown: {
					contentnomore: '没有需要处理的用户举报'
				}
			}
		},
		onShow() {
			this.getTipofUserList()
		},
		onLoad() {
			this.getTipofUserList()
		},
		methods: {
			getTipofUserList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getUserReport',
					method: 'GET',
					success: (res) => {
						let tipofList = res.data.data.reportList
						this.users = tipofList
						this.listNum = tipofList.length
						// this.listNum = 0  // for test
					}
				})
			},
			chooseUser(index) {
				uni.navigateTo({
					url: 'setTipofUser?index=' + index
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
