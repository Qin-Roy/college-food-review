<template>
	<view class="appealList">
		<view v-if="listNum">
			<uni-section title="未处理的申诉列表" type="line">
				<uni-list v-for="(item, index) in appeals">
					<uni-list-item thumbSize="lg" clickable="true"
					:title="item.nickName"
					:note="item.reportList[0].reportReason"
					:rightText="item.reportDate"
					@click="chooseAppeal(index)">
					</uni-list-item>
				</uni-list>
			</uni-section>
		</view>
		<uni-load-more status="noMore" :contentText="contenDown"></uni-load-more>
		<!-- <text v-else class="text">没有需要处理的申诉</text> -->
	</view>
</template>

<script>
	export default {
		data() {
			return {
				listNum: "",
				appeals: [],
				contenDown: {
					contentnomore: '没有需要处理的申诉'
				}
			}
		},
		onShow() {
			this.getAppealList()
		},
		onLoad() {
			this.getAppealList()
		},
		methods: {
			getAppealList() {
				uni.request({
					url: 'http://124.71.170.100/index.php/index/getAppeal',
					method: 'GET',
					success: (res) => {
						let appealList = res.data.data.appealList
						this.appeals = appealList
						this.listNum = appealList.length
						// this.listNum = 0  // for test
					}
				})
			},
			chooseAppeal(index) {
				uni.navigateTo({
					url: 'setAppeal?index=' + index
				})
			}
		}
	}
</script>

<style>
	.text{
		display: flex;
		margin: auto auto;
	}
</style>
